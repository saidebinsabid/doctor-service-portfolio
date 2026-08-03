<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\SlotUnavailableException;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Chamber;
use App\Services\BookingService;
use App\Services\Notifications\MessageBuilder;
use App\Services\SlotService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AppointmentController extends Controller
{
    public function __construct(
        protected BookingService $booking,
        protected SlotService $slots,
        protected MessageBuilder $messages,
    ) {
    }

    /** তালিকা — ফিল্টার, সার্চ ও পেজিনেশন সহ */
    public function index(Request $request): View
    {
        $query = Appointment::query()->with('chamber');

        /* ডিফল্টে আজকের সিরিয়াল দেখানো হয় — অ্যাডমিন সাধারণত
           এটাই দেখতে চান, প্রতিবার ফিল্টার করতে হয় না */
        $date = $request->input('date', $request->hasAny(['status', 'q', 'from', 'to'])
            ? null
            : now()->toDateString());

        if ($date) {
            $query->forDate($date);
        }

        if ($from = $request->input('from')) {
            $query->whereDate('appointment_date', '>=', $from);
        }

        if ($to = $request->input('to')) {
            $query->whereDate('appointment_date', '<=', $to);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($chamberId = $request->input('chamber_id')) {
            $query->where('chamber_id', $chamberId);
        }

        /* নাম, ফোন বা বুকিং কোড — যেকোনো একটি দিয়ে খোঁজা */
        if ($q = trim((string) $request->input('q'))) {
            $phone = normalize_bd_phone($q);

            $query->where(function ($sub) use ($q, $phone) {
                $sub->where('patient_name', 'like', "%{$q}%")
                    ->orWhere('booking_code', 'like', "%{$q}%");

                if ($phone !== '') {
                    $sub->orWhere('patient_phone', 'like', "%{$phone}%");
                }
            });
        }

        $appointments = $query
            ->orderBy('appointment_date', $date ? 'asc' : 'desc')
            ->orderBy('serial_no')
            ->paginate(30)
            ->withQueryString();

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'chambers'     => Chamber::ordered()->get(),
            'filters'      => $request->only(['date', 'from', 'to', 'status', 'q', 'chamber_id']),
            'date'         => $date,
            'statusCounts' => $this->statusCounts($date),
        ]);
    }

    public function show(Appointment $appointment): View
    {
        $appointment->load('chamber', 'handler', 'messageLogs');

        return view('admin.appointments.show', [
            'a' => $appointment,
            /* Phase 1-এ অ্যাডমিন নিজে WhatsApp থেকে বার্তা পাঠান —
               তাই লেখাটি প্রস্তুত করে "কপি করুন" ও সরাসরি লিংক দেওয়া হয় */
            'confirmText' => $this->messages->confirmationToPatient($appointment),
            'waLink' => 'https://wa.me/' . intl_bd_phone($appointment->patient_phone)
                . '?text=' . rawurlencode($this->messages->confirmationToPatient($appointment)),
        ]);
    }

    /** মাসিক ক্যালেন্ডার ভিউ */
    public function calendar(Request $request): View
    {
        $month = $request->filled('month')
            ? CarbonImmutable::createFromFormat('Y-m', $request->string('month')->toString())->startOfMonth()
            : CarbonImmutable::today()->startOfMonth();

        $counts = Appointment::query()
            ->holding()
            ->whereBetween('appointment_date', [
                $month->startOfMonth()->toDateString(),
                $month->endOfMonth()->toDateString(),
            ])
            ->selectRaw('appointment_date, count(*) as total')
            ->groupBy('appointment_date')
            ->pluck('total', 'appointment_date');

        return view('admin.appointments.calendar', [
            'month'  => $month,
            'counts' => $counts,
        ]);
    }

    /** ফোনে আসা সিরিয়াল অ্যাডমিন নিজে যোগ করেন */
    public function create(Request $request): View
    {
        return view('admin.appointments.create', [
            'chambers' => Chamber::forPublic()->get(),
            'date'     => $request->input('date', now()->toDateString()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'chamber_id'       => ['required', 'exists:chambers,id'],
            'appointment_date' => ['required', 'date_format:Y-m-d'],
            'slot_time'        => ['required', 'date_format:H:i'],
            'patient_name'     => ['required', 'string', 'max:100'],
            'patient_phone'    => ['required', 'string', 'max:20'],
            'patient_age'      => ['nullable', 'integer', 'min:0', 'max:200'],
            'patient_age_unit' => ['required', Rule::in(['day', 'month', 'year'])],
            'gender'           => ['nullable', Rule::in(['male', 'female'])],
            'guardian_name'    => ['nullable', 'string', 'max:100'],
            'visit_type'       => ['required', Rule::in(['new', 'followup', 'report'])],
            'problem'          => ['nullable', 'string', 'max:500'],
        ]);

        $chamber = Chamber::findOrFail($data['chamber_id']);

        try {
            $appointment = $this->booking->book($chamber, [
                ...$data,
                'source' => 'manual',
                'locale' => 'bn',
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        /* অ্যাডমিন নিজে যোগ করেছেন মানে সিরিয়াল ইতিমধ্যেই নিশ্চিত */
        $appointment->update(['status' => 'confirmed', 'confirmed_at' => now()]);

        ActivityLog::record('created', $appointment,
            "ম্যানুয়ালি সিরিয়াল যোগ: {$appointment->patient_name} ({$appointment->booking_code})");

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('success', 'সিরিয়াল যোগ করা হয়েছে — ' . $appointment->booking_code);
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Appointment::STATUS_LABELS))],
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        $before = $appointment->status;

        match ($data['status']) {
            'confirmed' => $this->booking->confirm($appointment),
            'cancelled' => $this->booking->cancel($appointment, $data['note'] ?? null),
            default     => $appointment->update([
                'status'     => $data['status'],
                'admin_note' => $data['note'] ?? $appointment->admin_note,
                'handled_by' => auth()->id(),
            ]),
        };

        ActivityLog::record('status', $appointment,
            "স্ট্যাটাস: {$before} → {$data['status']} ({$appointment->booking_code})");

        $label = Appointment::STATUS_LABELS[$data['status']]['bn'];

        return back()->with('success', "অবস্থা পরিবর্তন করা হয়েছে — {$label}");
    }

    public function reschedule(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate([
            'appointment_date' => ['required', 'date_format:Y-m-d'],
            'slot_time'        => ['required', 'date_format:H:i'],
        ]);

        $old = fmt_date($appointment->appointment_date, 'bn') . ' ' . fmt_time($appointment->slotHm(), 'bn');

        try {
            $this->booking->reschedule($appointment, $data['appointment_date'], $data['slot_time']);
        } catch (SlotUnavailableException $e) {
            return back()->with('error', $e->getMessage());
        }

        $new = fmt_date($appointment->appointment_date, 'bn') . ' ' . fmt_time($appointment->slotHm(), 'bn');

        ActivityLog::record('reschedule', $appointment, "সময় পরিবর্তন: {$old} → {$new}");

        return back()->with('success', 'নতুন সময়ে সরানো হয়েছে।');
    }

    public function updateNote(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate(['admin_note' => ['nullable', 'string', 'max:1000']]);

        $appointment->update($data);

        return back()->with('success', 'নোট সংরক্ষিত হয়েছে।');
    }

    /** ছাপার উপযোগী সিরিয়াল স্লিপ */
    public function slip(Appointment $appointment): View
    {
        return view('admin.appointments.slip', ['a' => $appointment->load('chamber')]);
    }

    /** দিনের রোগীর তালিকা — ছাপিয়ে চেম্বারে রাখার জন্য */
    public function printList(Request $request): View
    {
        $date = $request->input('date', now()->toDateString());

        return view('admin.appointments.print', [
            'date' => CarbonImmutable::parse($date),
            'list' => Appointment::with('chamber')
                ->forDate($date)
                ->holding()
                ->orderBy('serial_no')
                ->get(),
        ]);
    }

    /**
     * CSV এক্সপোর্ট।
     *
     * UTF-8 BOM যোগ করা হয় — নইলে Excel-এ বাংলা লেখা ভেঙে যায়।
     * স্ট্রিম করে পাঠানো হয়, তাই হাজার হাজার সারিতেও মেমোরি ভরে না।
     */
    public function export(Request $request): StreamedResponse
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->endOfMonth()->toDateString());

        $filename = "appointments-{$from}-to-{$to}.csv";

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');

            fwrite($out, "\xEF\xBB\xBF");           // Excel-এ বাংলার জন্য BOM

            fputcsv($out, [
                'বুকিং কোড', 'তারিখ', 'সময়', 'সিরিয়াল',
                'রোগীর নাম', 'বয়স', 'লিঙ্গ', 'মোবাইল', 'অভিভাবক',
                'ভিজিটের ধরন', 'সমস্যা', 'অবস্থা', 'চেম্বার', 'উৎস', 'বুক করার সময়',
            ]);

            Appointment::with('chamber')
                ->whereBetween('appointment_date', [$from, $to])
                ->orderBy('appointment_date')
                ->orderBy('serial_no')
                ->chunk(500, function ($rows) use ($out) {
                    foreach ($rows as $a) {
                        fputcsv($out, [
                            $a->booking_code,
                            fmt_date($a->appointment_date, 'bn'),
                            fmt_time($a->slotHm(), 'bn'),
                            $a->serial_no,
                            $a->patient_name,
                            $a->ageLabel('bn'),
                            $a->gender === 'female' ? 'মেয়ে' : ($a->gender === 'male' ? 'ছেলে' : ''),
                            $a->patient_phone,
                            $a->guardian_name,
                            $a->visitLabel('bn'),
                            $a->problem,
                            $a->statusLabel('bn'),
                            $a->chamber?->name_bn,
                            $a->source,
                            $a->created_at?->format('Y-m-d H:i'),
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function statusCounts(?string $date): array
    {
        $query = Appointment::query();

        if ($date) {
            $query->forDate($date);
        }

        return $query->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }
}
