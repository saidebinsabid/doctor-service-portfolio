<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ContactMessage;
use App\Models\Setting;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = CarbonImmutable::today();

        /* আজকের সিরিয়াল তালিকা — ডাক্তার সকালে এসেই এটা দেখবেন,
           তাই সিরিয়াল নম্বরের ক্রমে সাজানো */
        $todayList = Appointment::query()
            ->with('chamber')
            ->forDate($today->toDateString())
            ->holding()
            ->orderBy('serial_no')
            ->get();

        return view('admin.dashboard', [
            'today'      => $today,
            'todayList'  => $todayList,
            'stats'      => $this->stats($today),
            'weekChart'  => $this->weekChart($today),
            'upcoming'   => Appointment::with('chamber')
                                ->whereDate('appointment_date', $today->addDay()->toDateString())
                                ->holding()
                                ->orderBy('serial_no')
                                ->limit(10)
                                ->get(),
            'pending'    => Appointment::where('status', 'pending')
                                ->upcoming()
                                ->orderBy('appointment_date')
                                ->orderBy('serial_no')
                                ->limit(10)
                                ->get(),
            'unread'     => ContactMessage::unread()->count(),
            'holidayMode' => Setting::bool('holiday_mode'),
            'bookingOff'  => ! Setting::bool('booking_enabled', true),
        ]);
    }

    protected function stats(CarbonImmutable $today): array
    {
        return [
            'today_total'   => Appointment::forDate($today->toDateString())->holding()->count(),
            'today_pending' => Appointment::forDate($today->toDateString())->where('status', 'pending')->count(),
            'today_done'    => Appointment::forDate($today->toDateString())->where('status', 'completed')->count(),
            'tomorrow'      => Appointment::forDate($today->addDay()->toDateString())->holding()->count(),
            'week'          => Appointment::holding()
                                ->whereBetween('appointment_date', [
                                    $today->toDateString(),
                                    $today->addDays(6)->toDateString(),
                                ])->count(),
            'month_total'   => Appointment::whereBetween('appointment_date', [
                                    $today->startOfMonth()->toDateString(),
                                    $today->endOfMonth()->toDateString(),
                                ])->holding()->count(),
            'no_show_month' => Appointment::where('status', 'no_show')
                                ->whereBetween('appointment_date', [
                                    $today->startOfMonth()->toDateString(),
                                    $today->endOfMonth()->toDateString(),
                                ])->count(),
        ];
    }

    /** আগামী ৭ দিনের বুকিং — ড্যাশবোর্ডের ছোট চার্টে */
    protected function weekChart(CarbonImmutable $today): array
    {
        $rows = Appointment::query()
            ->holding()
            ->whereBetween('appointment_date', [
                $today->toDateString(),
                $today->addDays(6)->toDateString(),
            ])
            ->select('appointment_date', DB::raw('count(*) as total'))
            ->groupBy('appointment_date')
            ->pluck('total', 'appointment_date');

        $out = [];

        for ($i = 0; $i < 7; $i++) {
            $d = $today->addDays($i);
            $key = $d->toDateString();

            $out[] = [
                'date'  => $key,
                'label' => fmt_day($d, true, 'bn'),
                'day'   => bn_number($d->format('j')),
                'count' => (int) ($rows[$key] ?? 0),
            ];
        }

        return $out;
    }
}
