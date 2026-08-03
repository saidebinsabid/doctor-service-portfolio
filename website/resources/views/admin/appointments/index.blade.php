@extends('admin.layouts.app')
@section('title', 'অ্যাপয়েন্টমেন্ট')
@section('heading', 'অ্যাপয়েন্টমেন্ট')

@php
    use App\Models\Appointment;
    $badge = fn ($tone) => match ($tone) {
        'amber' => 'badge-amber', 'sky' => 'badge-blue',
        'green' => 'badge-green', 'red' => 'badge-red', default => 'badge-slate',
    };
@endphp

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.appointments.create') }}" class="a-btn a-btn-primary a-btn-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                ম্যানুয়াল সিরিয়াল
            </a>
            <a href="{{ route('admin.appointments.calendar') }}" class="a-btn a-btn-light a-btn-sm">ক্যালেন্ডার</a>
            <a href="{{ route('admin.appointments.print', $filters) }}" target="_blank" class="a-btn a-btn-light a-btn-sm">দিনের তালিকা ছাপুন</a>
            <a href="{{ route('admin.appointments.export', $filters) }}" class="a-btn a-btn-light a-btn-sm">CSV এক্সপোর্ট</a>
        </div>
    </div>

    {{-- ফিল্টার --}}
    <form method="GET" class="a-card p-4 mb-4 grid sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <div><label class="a-label">তারিখ</label>
            <input type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="a-input"></div>
        <div><label class="a-label">অবস্থা</label>
            <select name="status" class="a-select">
                <option value="">সব</option>
                @foreach(Appointment::STATUS_LABELS as $k => $l)
                    <option value="{{ $k }}" @selected(($filters['status'] ?? '') === $k)>{{ $l['bn'] }}</option>
                @endforeach
            </select></div>
        <div class="lg:col-span-2"><label class="a-label">নাম / ফোন / কোড</label>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="a-input" placeholder="খুঁজুন…"></div>
        <div class="flex items-end gap-2">
            <button class="a-btn a-btn-primary">খুঁজুন</button>
            <a href="{{ route('admin.appointments.index') }}" class="a-btn a-btn-light">রিসেট</a>
        </div>
    </form>

    {{-- স্ট্যাটাস চিপ --}}
    @if(!empty($statusCounts))
        <div class="flex flex-wrap gap-2 mb-3 text-sm">
            @foreach(Appointment::STATUS_LABELS as $k => $l)
                @if($c = ($statusCounts[$k] ?? 0))
                    <span class="badge {{ $badge($l['tone']) }}">{{ $l['bn'] }}: {{ bn_number($c) }}</span>
                @endif
            @endforeach
        </div>
    @endif

    <div class="a-card overflow-hidden">
        @if($appointments->isEmpty())
            <p class="px-5 py-10 text-center text-slate-500 text-sm">কোনো সিরিয়াল পাওয়া যায়নি।</p>
        @else
            <div class="overflow-x-auto">
                <table class="a-table">
                    <thead><tr><th>সিরিয়াল</th><th>রোগী</th><th>তারিখ ও সময়</th><th>ধরন</th><th>অবস্থা</th><th class="text-end">কাজ</th></tr></thead>
                    <tbody>
                        @foreach($appointments as $a)
                            <tr>
                                <td class="font-bold text-brand-900">{{ bn_number($a->serial_no) }}<div class="text-[.68rem] font-normal text-slate-400" dir="ltr">{{ $a->booking_code }}</div></td>
                                <td><div class="font-medium text-slate-800">{{ $a->patient_name }}</div>
                                    <div class="text-xs text-slate-400" dir="ltr">{{ $a->patient_phone }}</div></td>
                                <td class="text-sm">{{ fmt_date($a->appointment_date) }}<div class="text-xs text-slate-400" dir="ltr">{{ fmt_time($a->slotHm()) }}</div></td>
                                <td class="text-sm">{{ $a->visitLabel() }}</td>
                                <td><span class="badge {{ $badge($a->statusTone()) }}">{{ $a->statusLabel() }}</span></td>
                                <td class="text-end"><a href="{{ route('admin.appointments.show', $a) }}" class="a-btn a-btn-light a-btn-sm">বিস্তারিত</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-brand-100">{{ $appointments->links() }}</div>
        @endif
    </div>

@endsection
