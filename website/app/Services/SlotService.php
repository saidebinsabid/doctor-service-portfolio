<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\BlockedSlot;
use App\Models\Chamber;
use App\Models\Holiday;
use App\Models\Schedule;
use App\Models\Setting;
use Carbon\CarbonImmutable;

/**
 * কোন দিনে কোন সময়ে সিরিয়াল খালি আছে — তার একমাত্র উৎস।
 *
 * ওয়েবসাইটের ক্যালেন্ডার, বুকিং যাচাই, অ্যাডমিন প্যানেল — সবাই এখান থেকেই
 * হিসাব নেয়। এক জায়গায় রাখার কারণ: নিয়ম বদলালে (যেমন ছুটির দিন যোগ)
 * যেন সব জায়গায় একসাথে বদলায়, কোথাও পুরনো হিসাব রয়ে না যায়।
 */
class SlotService
{
    /* একটি দিনের সম্ভাব্য অবস্থা */
    public const OPEN    = 'open';       // সিরিয়াল খালি আছে
    public const FULL    = 'full';       // সব সিরিয়াল শেষ
    public const CLOSED  = 'closed';     // ওই বারে চেম্বার বসে না
    public const HOLIDAY = 'holiday';    // ছুটি
    public const PAST    = 'past';       // তারিখ পেরিয়ে গেছে
    public const BEYOND  = 'beyond';     // এত আগাম বুকিং নেওয়া হয় না

    /**
     * একটি দিনের পূর্ণ চিত্র।
     *
     * ফেরত দেয়:
     *   status  → উপরের ধ্রুবকগুলোর একটি
     *   slots   → [['serial' => 1, 'time' => '10:30', 'available' => true, 'reason' => null], …]
     *   open    → শুধু খালি স্লটগুলো
     *   reason  → বন্ধ থাকলে কারণ (ছুটির নোট)
     */
    public function day(Chamber $chamber, CarbonImmutable $date): array
    {
        $empty = ['slots' => [], 'open' => [], 'reason' => null, 'schedule' => null];

        /* ---- অতীত ---- */
        if ($date->lt(CarbonImmutable::today())) {
            return ['status' => self::PAST] + $empty;
        }

        /* ---- কত দিন আগাম পর্যন্ত ---- */
        $maxDate = CarbonImmutable::today()->addDays(config('site.booking.advance_days'));
        if ($date->gt($maxDate)) {
            return ['status' => self::BEYOND] + $empty;
        }

        /* ---- ছুটির মোড: অ্যাডমিন এক ক্লিকে সব বুকিং বন্ধ করতে পারেন ---- */
        if (Setting::bool('holiday_mode')) {
            return ['status' => self::HOLIDAY] + $empty
                + ['reason' => __('booking.holiday_mode')];
        }

        /* ---- নির্দিষ্ট দিনের ছুটি ---- */
        $holiday = $this->holidayFor($chamber, $date);

        if ($holiday?->isClosed()) {
            return ['status' => self::HOLIDAY] + $empty
                + ['reason' => $holiday->reason ?: null];
        }

        /* ---- ওই বারের সময়সূচি ---- */
        $schedule = $this->scheduleFor($chamber, $date);

        if (! $schedule) {
            return ['status' => self::CLOSED] + $empty;
        }

        /* ছুটির সারিতে বিকল্প সময় দেওয়া থাকলে সেটিই প্রাধান্য পাবে */
        $window = $this->window($schedule, $holiday);

        $slots = $this->buildSlots($window);

        if ($slots === []) {
            return ['status' => self::CLOSED] + $empty + ['schedule' => $schedule];
        }

        /* ---- দখল হয়ে থাকা ও ব্লক করা স্লট ---- */
        $taken   = $this->takenTimes($chamber, $date);
        $blocked = $this->blockedTimes($chamber, $date);

        /* ---- আজকের দিনে যে সময়গুলো পেরিয়ে গেছে ---- */
        $cutoff = null;
        if ($date->isSameDay(CarbonImmutable::today())) {
            $cutoff = (int) now()->format('H') * 60
                + (int) now()->format('i')
                + config('site.booking.cutoff_minutes');
        }

        $open = [];

        foreach ($slots as &$slot) {
            $reason = match (true) {
                in_array($slot['time'], $taken, true)   => 'taken',
                in_array($slot['time'], $blocked, true) => 'blocked',
                $cutoff !== null
                    && to_minutes($slot['time']) <= $cutoff => 'passed',
                default => null,
            };

            $slot['available'] = $reason === null;
            $slot['reason'] = $reason;

            if ($slot['available']) {
                $open[] = $slot;
            }
        }
        unset($slot);

        return [
            'status'   => $open === [] ? self::FULL : self::OPEN,
            'slots'    => $slots,
            'open'     => $open,
            'reason'   => $holiday?->reason ?: null,
            'schedule' => $schedule,
        ];
    }

    /**
     * ক্যালেন্ডারের জন্য একসাথে অনেকগুলো দিন।
     *
     * প্রতিটি দিনের জন্য আলাদা কোয়েরি না করে দখল ও ব্লকের তথ্য
     * একবারে এনে রাখা হয় — ৩০ দিনের ক্যালেন্ডারে ৬০+ কোয়েরির বদলে ৩টি।
     */
    public function range(Chamber $chamber, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $this->warmCache($chamber, $from, $to);

        $out = [];

        for ($d = $from; $d->lte($to); $d = $d->addDay()) {
            $day = $this->day($chamber, $d);

            $out[$d->toDateString()] = [
                'date'       => $d,
                'status'     => $day['status'],
                'open_count' => count($day['open']),
                'reason'     => $day['reason'],
            ];
        }

        $this->clearCache();

        return $out;
    }

    /*
    |--------------------------------------------------------------------------
    | ভেতরের কাজ
    |--------------------------------------------------------------------------
    */

    /** ওই বারের সক্রিয় সময়সূচি */
    public function scheduleFor(Chamber $chamber, CarbonImmutable $date): ?Schedule
    {
        return $chamber->schedules()
            ->active()
            ->where('day_of_week', (int) $date->format('w'))
            ->first();
    }

    public function holidayFor(Chamber $chamber, CarbonImmutable $date): ?Holiday
    {
        return Holiday::query()
            ->forChamber($chamber->id)
            ->whereDate('date', $date->toDateString())
            ->orderByRaw('chamber_id IS NULL')   // চেম্বার-নির্দিষ্ট ছুটি আগে
            ->first();
    }

    /** সময়সূচি + ছুটির বিকল্প সময় মিলিয়ে চূড়ান্ত সময়সীমা */
    protected function window(Schedule $schedule, ?Holiday $holiday): array
    {
        $useCustom = $holiday
            && $holiday->type === 'custom_time'
            && filled($holiday->custom_start)
            && filled($holiday->custom_end);

        return [
            'start'        => $useCustom ? substr((string) $holiday->custom_start, 0, 5) : $schedule->startHm(),
            'end'          => $useCustom ? substr((string) $holiday->custom_end, 0, 5) : $schedule->endHm(),
            'slot_minutes' => max(1, $schedule->slot_minutes),
            'max_serials'  => $useCustom && $holiday->custom_max_serials
                ? $holiday->custom_max_serials
                : $schedule->max_serials,
        ];
    }

    /** সময়সীমা থেকে সিরিয়াল ও সময়ের তালিকা */
    public function buildSlots(array $window): array
    {
        $start = to_minutes($window['start']);
        $end   = to_minutes($window['end']);
        $step  = $window['slot_minutes'];

        $slots = [];

        for ($i = 0; $i < $window['max_serials']; $i++) {
            $at = $start + ($i * $step);

            /* শেষ স্লটটিও যেন পুরো সময় পায় — চেম্বার বন্ধের পরে গড়ায় না */
            if ($at + $step > $end) {
                break;
            }

            $slots[] = [
                'serial'    => $i + 1,
                'time'      => from_minutes($at),
                'available' => true,
                'reason'    => null,
            ];
        }

        return $slots;
    }

    /* ---- একাধিক দিনের কোয়েরি একবারে সেরে রাখার ক্যাশ ---- */
    protected ?array $takenCache = null;
    protected ?array $blockedCache = null;

    protected function warmCache(Chamber $chamber, CarbonImmutable $from, CarbonImmutable $to): void
    {
        $this->takenCache = Appointment::query()
            ->where('chamber_id', $chamber->id)
            ->holding()
            ->whereBetween('appointment_date', [$from->toDateString(), $to->toDateString()])
            ->get(['appointment_date', 'slot_time'])
            ->groupBy(fn ($a) => $a->dateString())
            ->map(fn ($rows) => $rows->map(fn ($a) => $a->slotHm())->all())
            ->all();

        $this->blockedCache = BlockedSlot::query()
            ->where('chamber_id', $chamber->id)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get(['date', 'slot_time'])
            ->groupBy(fn ($b) => $b->date->toDateString())
            ->map(fn ($rows) => $rows->map(fn ($b) => $b->slotHm())->all())
            ->all();
    }

    protected function clearCache(): void
    {
        $this->takenCache = null;
        $this->blockedCache = null;
    }

    /** ওই দিনে দখল হয়ে থাকা সময়গুলো ("10:30" আকারে) */
    public function takenTimes(Chamber $chamber, CarbonImmutable $date): array
    {
        if ($this->takenCache !== null) {
            return $this->takenCache[$date->toDateString()] ?? [];
        }

        return Appointment::query()
            ->where('chamber_id', $chamber->id)
            ->holding()
            ->whereDate('appointment_date', $date->toDateString())
            ->get(['slot_time'])
            ->map(fn ($a) => $a->slotHm())
            ->all();
    }

    public function blockedTimes(Chamber $chamber, CarbonImmutable $date): array
    {
        if ($this->blockedCache !== null) {
            return $this->blockedCache[$date->toDateString()] ?? [];
        }

        return BlockedSlot::query()
            ->where('chamber_id', $chamber->id)
            ->whereDate('date', $date->toDateString())
            ->get(['slot_time'])
            ->map(fn ($b) => $b->slotHm())
            ->all();
    }

    /** নির্দিষ্ট একটি সময় এখন বুক করা যাবে কি না */
    public function isAvailable(Chamber $chamber, CarbonImmutable $date, string $time): bool
    {
        $day = $this->day($chamber, $date);

        if ($day['status'] !== self::OPEN) {
            return false;
        }

        foreach ($day['open'] as $slot) {
            if ($slot['time'] === $time) {
                return true;
            }
        }

        return false;
    }

    /** ওই সময়ের সিরিয়াল নম্বর কত */
    public function serialFor(Chamber $chamber, CarbonImmutable $date, string $time): ?int
    {
        $day = $this->day($chamber, $date);

        foreach ($day['slots'] as $slot) {
            if ($slot['time'] === $time) {
                return $slot['serial'];
            }
        }

        return null;
    }
}
