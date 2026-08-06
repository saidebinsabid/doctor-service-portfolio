<?php

namespace App\Services;

use App\Exceptions\BookingLimitException;
use App\Exceptions\SlotUnavailableException;
use App\Models\Appointment;
use App\Models\Chamber;
use App\Services\Notifications\NotificationDispatcher;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected SlotService $slots,
        protected NotificationDispatcher $notifier,
    ) {
    }

    /**
     * নতুন সিরিয়াল নেওয়া।
     *
     * @throws SlotUnavailableException  স্লট আর খালি নেই
     * @throws BookingLimitException     সীমা পেরিয়ে গেছে
     */
    public function book(Chamber $chamber, array $data): Appointment
    {
        $date = CarbonImmutable::parse($data['appointment_date']);
        $requestedTime = ! empty($data['slot_time']) ? substr($data['slot_time'], 0, 5) : null;
        $phone = normalize_bd_phone($data['patient_phone']);

        $this->guardLimits($phone, $data['ip_address'] ?? null);

        try {
            $appointment = DB::transaction(function () use ($chamber, $date, $requestedTime, $phone, $data) {

                /*
                | ⭐ ডাবল-বুকিং প্রতিরোধ — প্রথম স্তর
                |
                | ওই দিনের সারিগুলো লক করে রাখা হয়, যাতে যাচাই ও সংরক্ষণের
                | মাঝের ফাঁকে অন্য কেউ একই স্লট নিয়ে নিতে না পারে।
                |
                | (MySQL-এ প্রকৃত রো-লক হয়; SQLite এমনিতেই লেখা ধারাবাহিক করে।)
                */
                Appointment::query()
                    ->where('chamber_id', $chamber->id)
                    ->whereDate('appointment_date', $date->toDateString())
                    ->lockForUpdate()
                    ->get(['id']);

                /* সময় বাছাই করা থাকলে সেটাই; না থাকলে ওই দিনের পরের খালি সময়
                   অটোমেটিক (লক নেওয়ার পর নির্ধারণ, যাতে রেস না হয়)। */
                $time = $requestedTime ?? $this->firstOpenSlot($chamber, $date);

                /* লক নেওয়ার পর আবার যাচাই — এই মুহূর্তের প্রকৃত অবস্থা */
                if ($time === null || ! $this->slots->isAvailable($chamber, $date, $time)) {
                    throw new SlotUnavailableException;
                }

                $serial = $this->slots->serialFor($chamber, $date, $time);

                if ($serial === null) {
                    throw new SlotUnavailableException;
                }

                return Appointment::create([
                    'booking_code'     => $this->generateCode($date, $serial),
                    'chamber_id'       => $chamber->id,
                    'appointment_date' => $date->toDateString(),
                    'slot_time'        => $time,
                    'serial_no'        => $serial,

                    'patient_name'     => trim($data['patient_name']),
                    'patient_phone'    => $phone,
                    'patient_age'      => $data['patient_age'] ?? null,
                    'patient_age_unit' => $data['patient_age_unit'] ?? 'year',
                    'gender'           => $data['gender'] ?? null,
                    'guardian_name'    => $data['guardian_name'] ?? null,
                    'address'          => $data['address'] ?? null,
                    'visit_type'       => $data['visit_type'] ?? 'new',
                    'problem'          => $data['problem'] ?? null,

                    'status'     => 'pending',
                    'source'     => $data['source'] ?? 'web',
                    'locale'     => $data['locale'] ?? app()->getLocale(),
                    'ip_address' => $data['ip_address'] ?? null,
                    'user_agent' => substr((string) ($data['user_agent'] ?? ''), 0, 255) ?: null,
                ]);
            });
        } catch (QueryException $e) {
            /*
            | ⭐ ডাবল-বুকিং প্রতিরোধ — দ্বিতীয় স্তর
            |
            | লক ফাঁকি দিয়েও যদি দুটি বুকিং একই স্লটে পৌঁছে যায়, ডাটাবেসের
            | unique index শেষ রক্ষা করে। সেই ত্রুটিটিকে এখানে রোগীর
            | বোধগম্য বার্তায় রূপান্তর করা হয়।
            */
            if ($this->isUniqueViolation($e)) {
                throw new SlotUnavailableException;
            }

            throw $e;
        }

        /* বার্তা পাঠানো — কিউতে যায়, রোগীকে অপেক্ষা করতে হয় না */
        $this->notifier->appointmentCreated($appointment);

        return $appointment;
    }

    /** সিরিয়াল বাতিল — স্লটটি সাথে সাথে আবার খালি হয়ে যায় */
    public function cancel(Appointment $appointment, ?string $reason = null): Appointment
    {
        $appointment->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
            'admin_note'   => $reason ?: $appointment->admin_note,
            'handled_by'   => auth()->id(),
        ]);

        $this->notifier->appointmentCancelled($appointment);

        return $appointment->refresh();
    }

    /** অন্য তারিখ/সময়ে সরানো */
    public function reschedule(Appointment $appointment, string $date, string $time): Appointment
    {
        $newDate = CarbonImmutable::parse($date);
        $newTime = substr($time, 0, 5);
        $chamber = $appointment->chamber;

        return DB::transaction(function () use ($appointment, $chamber, $newDate, $newTime) {

            Appointment::query()
                ->where('chamber_id', $chamber->id)
                ->whereDate('appointment_date', $newDate->toDateString())
                ->lockForUpdate()
                ->get(['id']);

            /* নিজের বর্তমান স্লটে ফিরে যাওয়া সবসময় বৈধ */
            $sameSlot = $appointment->dateString() === $newDate->toDateString()
                && $appointment->slotHm() === $newTime;

            if (! $sameSlot && ! $this->slots->isAvailable($chamber, $newDate, $newTime)) {
                throw new SlotUnavailableException;
            }

            $serial = $this->slots->serialFor($chamber, $newDate, $newTime);

            if ($serial === null) {
                throw new SlotUnavailableException;
            }

            $appointment->update([
                'appointment_date' => $newDate->toDateString(),
                'slot_time'        => $newTime,
                'serial_no'        => $serial,
                'handled_by'       => auth()->id(),
            ]);

            $this->notifier->appointmentRescheduled($appointment);

            return $appointment->refresh();
        });
    }

    public function confirm(Appointment $appointment): Appointment
    {
        $appointment->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
            'handled_by'   => auth()->id(),
        ]);

        $this->notifier->appointmentConfirmed($appointment);

        return $appointment->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | ভেতরের কাজ
    |--------------------------------------------------------------------------
    */

    /**
     * স্প্যাম ও ভুয়া বুকিং ঠেকানো।
     *
     * একজন বট চাইলে ২৫টি সিরিয়ালই দখল করে রাখতে পারত, আর প্রকৃত
     * রোগীরা বঞ্চিত হতেন। তাই নম্বর ও IP — দুই দিক থেকেই সীমা।
     */
    protected function guardLimits(string $phone, ?string $ip): void
    {
        $perPhone = config('site.booking.max_per_phone_per_day');

        $todayByPhone = Appointment::query()
            ->where('patient_phone', $phone)
            ->holding()
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($todayByPhone >= $perPhone) {
            throw new BookingLimitException(
                __('booking.limit_phone', ['count' => bn_number($perPhone)])
            );
        }

        if ($ip) {
            $perIp = config('site.booking.max_per_ip_per_hour');

            $lastHourByIp = Appointment::query()
                ->where('ip_address', $ip)
                ->where('created_at', '>=', now()->subHour())
                ->count();

            if ($lastHourByIp >= $perIp) {
                throw new BookingLimitException(__('booking.limit_ip'));
            }
        }
    }

    /**
     * ওই দিনের পরের (সবচেয়ে আগের) খালি সময় — রোগী সময় না বাছলে ব্যবহৃত হয়।
     * কোনো খালি সময় না থাকলে null (দিন পূর্ণ)।
     */
    protected function firstOpenSlot(Chamber $chamber, CarbonImmutable $date): ?string
    {
        foreach ($this->slots->day($chamber, $date)['slots'] as $slot) {
            if ($slot['available'] ?? false) {
                return $slot['time'];
            }
        }

        return null;
    }

    /**
     * বুকিং কোড — ASF-260805-04
     *
     * বাতিল হওয়া সিরিয়াল আবার বুক হলে একই কোড তৈরি হতে পারত,
     * তাই সংঘর্ষ হলে শেষে একটি অক্ষর যোগ করা হয় (…-04B)।
     */
    protected function generateCode(CarbonImmutable $date, int $serial): string
    {
        $base = sprintf(
            '%s-%s-%02d',
            config('site.booking.code_prefix'),
            $date->format('ymd'),
            $serial,
        );

        if (! Appointment::where('booking_code', $base)->exists()) {
            return $base;
        }

        foreach (range('B', 'Z') as $suffix) {
            $candidate = $base . $suffix;

            if (! Appointment::where('booking_code', $candidate)->exists()) {
                return $candidate;
            }
        }

        /* একই স্লটে ২৫ বার বাতিল-পুনর্বুকিং — বাস্তবে হবে না,
           তবু কোড যেন কখনো ডুপ্লিকেট না হয় */
        return $base . '-' . strtoupper(substr(uniqid(), -4));
    }

    protected function isUniqueViolation(QueryException $e): bool
    {
        $code = (string) ($e->errorInfo[1] ?? '');
        $message = strtolower($e->getMessage());

        return $code === '1062'                              // MySQL
            || str_contains($message, 'unique constraint')   // SQLite
            || str_contains($message, 'duplicate entry');
    }
}
