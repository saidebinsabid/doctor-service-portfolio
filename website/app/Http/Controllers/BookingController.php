<?php

namespace App\Http\Controllers;

use App\Exceptions\BookingLimitException;
use App\Exceptions\SlotUnavailableException;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Appointment;
use App\Models\Chamber;
use App\Models\Setting;
use App\Services\BookingService;
use App\Services\Notifications\NotificationDispatcher;
use App\Services\SlotService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected SlotService $slots,
        protected BookingService $booking,
        protected NotificationDispatcher $notifier,
    ) {
    }

    /**
     * বুকিং পাতা — ক্যালেন্ডার ও ফর্ম।
     *
     * তারিখ ও মাস ঠিকানার প্যারামিটার হিসেবে নেওয়া হয়, যাতে
     * জাভাস্ক্রিপ্ট বন্ধ থাকলেও সাধারণ লিংকে ক্লিক করে কাজ চলে।
     * জাভাস্ক্রিপ্ট থাকলে সে-ই লিংক আটকে দিয়ে শুধু স্লটগুলো আনে।
     */
    public function create(Request $request): View
    {
        $chamber = $this->chamber();
        $today = CarbonImmutable::today();

        $month = $today->startOfMonth();
        if ($request->filled('month')) {
            try {
                $month = CarbonImmutable::createFromFormat(
                    'Y-m', $request->string('month')->toString()
                )->startOfMonth();
            } catch (\Throwable) {
                /* ভুল মাস দিলে চলতি মাসেই থাকবে */
            }
        }

        $selected = null;
        $day = null;

        if ($request->filled('date')) {
            try {
                $candidate = CarbonImmutable::parse($request->string('date')->toString());

                if ($candidate->gte($today)) {
                    $selected = $candidate;
                    $day = $this->slots->day($chamber, $selected);
                }
            } catch (\Throwable) {
                /* ভুল তারিখ উপেক্ষা করা হয় */
            }
        }

        return view('public.booking', [
            'chamber'   => $chamber,
            'enabled'   => Setting::bool('booking_enabled', true)
                            && ! Setting::bool('holiday_mode'),
            'calendar'  => $this->calendarData($chamber, $month),
            'month'     => $month,
            'prevMonth' => $month->gt($today->startOfMonth()) ? $month->subMonth()->format('Y-m') : null,
            'nextMonth' => $month->lt($today->addDays(config('site.booking.advance_days'))->startOfMonth())
                            ? $month->addMonth()->format('Y-m') : null,
            'selected'  => $selected,
            'day'       => $day,
        ]);
    }

    /**
     * ক্যালেন্ডারের এক মাসের অবস্থা (JSON)।
     * তারিখ বা মাস বদলালে পুরো পাতা রিলোড না করে শুধু এটুকু আনা হয়।
     */
    public function calendar(Request $request): JsonResponse
    {
        $chamber = $this->chamber();

        $month = $request->filled('month')
            ? CarbonImmutable::createFromFormat('Y-m', $request->string('month')->toString())->startOfMonth()
            : CarbonImmutable::today()->startOfMonth();

        return response()->json([
            'month' => $month->format('Y-m'),
            'label' => fmt_date($month->startOfMonth()),
            'days'  => $this->calendarData($chamber, $month),
        ]);
    }

    /** একটি নির্দিষ্ট দিনের স্লট তালিকা (JSON) */
    public function slots(Request $request): JsonResponse
    {
        $request->validate(['date' => ['required', 'date_format:Y-m-d']]);

        $chamber = $this->chamber();
        $date = CarbonImmutable::parse($request->string('date')->toString());

        $day = $this->slots->day($chamber, $date);

        return response()->json([
            'status' => $day['status'],
            'reason' => $day['reason'],
            'date_label' => fmt_date($date) . ' (' . fmt_day($date) . ')',
            'slots' => collect($day['slots'])->map(fn ($s) => [
                'serial'    => $s['serial'],
                'serial_bn' => bn_number($s['serial']),
                'time'      => $s['time'],
                'label'     => fmt_time($s['time']),
                'available' => $s['available'],
            ])->values(),
        ]);
    }

    /** সিরিয়াল সংরক্ষণ */
    public function store(StoreBookingRequest $request): RedirectResponse
    {
        if (! Setting::bool('booking_enabled', true)) {
            return back()->withInput()->with('error', __('booking.disabled'));
        }

        $chamber = Chamber::findOrFail($request->integer('chamber_id'));

        try {
            $appointment = $this->booking->book($chamber, [
                ...$request->validated(),
                'source'     => 'web',
                'locale'     => app()->getLocale(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (SlotUnavailableException | BookingLimitException $e) {
            /* স্লট হাতছাড়া হলে রোগীর লেখা তথ্য যেন হারিয়ে না যায় —
               ফর্ম পূরণ করাই থাকবে, শুধু নতুন সময় বেছে নিতে হবে */
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('booking.success', ['code' => $appointment->booking_code])
            ->with('just_booked', true);
    }

    /** সাকসেস পাতা — সিরিয়াল স্লিপ ও WhatsApp বাটন */
    public function success(string $code): View
    {
        $appointment = Appointment::where('booking_code', $code)
            ->with('chamber')
            ->firstOrFail();

        return view('public.booking-success', [
            'a' => $appointment,
            'waLink' => $this->notifier->whatsappLink($appointment),
            'calendarLink' => $this->googleCalendarLink($appointment),
            'automatic' => $this->notifier->isAutomatic(),
        ]);
    }

    /** "সিরিয়াল দেখুন" ফর্ম */
    public function statusForm(): View
    {
        return view('public.booking-status', ['appointment' => null]);
    }

    /**
     * বুকিং কোড + মোবাইল নম্বর দিয়ে অবস্থা দেখা।
     *
     * ⚠️ দুটোই মিলতে হবে। শুধু কোড দিয়ে দেখা গেলে কেউ কোড অনুমান করে
     *    অন্য রোগীর নাম ও ফোন নম্বর দেখে ফেলতে পারত।
     */
    public function statusLookup(Request $request): View
    {
        $data = $request->validate([
            'booking_code' => ['required', 'string', 'max:20'],
            'phone'        => ['required', 'string', 'max:20'],
        ]);

        $appointment = Appointment::query()
            ->where('booking_code', trim($data['booking_code']))
            ->where('patient_phone', normalize_bd_phone($data['phone']))
            ->with('chamber')
            ->first();

        return view('public.booking-status', [
            'appointment' => $appointment,
            'searched'    => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ভেতরের কাজ
    |--------------------------------------------------------------------------
    */

    protected function chamber(): Chamber
    {
        return Chamber::forPublic()->firstOrFail();
    }

    /** ক্যালেন্ডারে দেখানোর জন্য এক মাসের দিনভিত্তিক অবস্থা */
    protected function calendarData(Chamber $chamber, CarbonImmutable $month): array
    {
        $from = $month->startOfMonth();
        $to   = $month->endOfMonth();

        /* চলতি মাস হলে আজ থেকেই শুরু — অতীতের দিন হিসাব করে লাভ নেই */
        if ($from->lt(CarbonImmutable::today())) {
            $from = CarbonImmutable::today();
        }

        $range = $this->slots->range($chamber, $from, $to);

        $days = [];

        for ($d = $month->startOfMonth(); $d->lte($to); $d = $d->addDay()) {
            $key = $d->toDateString();
            $info = $range[$key] ?? ['status' => SlotService::PAST, 'open_count' => 0, 'reason' => null];

            $days[] = [
                'date'       => $key,
                'day'        => (int) $d->format('j'),
                'day_bn'     => bn_number($d->format('j')),
                'dow'        => (int) $d->format('w'),
                'status'     => $info['status'],
                'open_count' => $info['open_count'],
                'open_bn'    => bn_number($info['open_count']),
                'reason'     => $info['reason'],
                'label'      => fmt_date($d) . ' — ' . $this->statusLabel($info),
                'selectable' => $info['status'] === SlotService::OPEN,
            ];
        }

        return $days;
    }

    protected function statusLabel(array $info): string
    {
        return match ($info['status']) {
            SlotService::OPEN => bn_number($info['open_count']) . ' ' . __('booking.available'),
            SlotService::FULL => __('booking.full'),
            default           => __('booking.closed'),
        };
    }

    /**
     * "ক্যালেন্ডারে যোগ করুন" লিংক।
     * সম্পূর্ণ ফ্রি — Google Calendar API বা কোনো কী লাগে না।
     */
    protected function googleCalendarLink(Appointment $a): string
    {
        $start = CarbonImmutable::parse($a->dateString() . ' ' . $a->slotHm(), config('app.timezone'));
        $end   = $start->addMinutes(30);

        return 'https://calendar.google.com/calendar/render?' . http_build_query([
            'action' => 'TEMPLATE',
            'text'   => Setting::get('doctor_short') . ' — ' . __('booking.f_serial') . ' ' . $a->serial_no,
            'dates'  => $start->utc()->format('Ymd\THis\Z') . '/' . $end->utc()->format('Ymd\THis\Z'),
            'location' => $a->chamber->address,
            'details'  => __('booking.f_code') . ': ' . $a->booking_code,
        ]);
    }
}
