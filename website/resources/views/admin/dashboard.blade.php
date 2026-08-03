@extends('admin.layouts.app')
@section('title', 'ড্যাশবোর্ড')
@section('heading', 'ড্যাশবোর্ড')

@php
    $badge = fn ($tone) => match ($tone) {
        'amber' => 'badge-amber', 'sky' => 'badge-blue',
        'green' => 'badge-green', 'red' => 'badge-red', default => 'badge-slate',
    };
@endphp

@section('content')

    {{-- সতর্কতা --}}
    @if($holidayMode)
        <div class="flash flash-error mb-4">🏖️ <b>ছুটির মোড চালু</b> — সব তারিখে নতুন বুকিং বন্ধ আছে। সাইট সেটিংস থেকে বন্ধ করুন।</div>
    @endif
    @if($bookingOff)
        <div class="flash flash-error mb-4">⛔ <b>অনলাইন বুকিং বন্ধ</b> — ভিজিটররা ক্যালেন্ডার দেখতে পাচ্ছেন না।</div>
    @endif

    {{-- স্ট্যাট টাইল --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="stat-tile"><div class="stat-num">{{ bn_number($stats['today_total']) }}</div><div class="stat-cap">আজকের সিরিয়াল</div></div>
        <div class="stat-tile"><div class="stat-num text-amber-600">{{ bn_number($stats['today_pending']) }}</div><div class="stat-cap">আজ অপেক্ষমাণ</div></div>
        <div class="stat-tile"><div class="stat-num text-green-600">{{ bn_number($stats['today_done']) }}</div><div class="stat-cap">আজ সম্পন্ন</div></div>
        <div class="stat-tile"><div class="stat-num">{{ bn_number($stats['tomorrow']) }}</div><div class="stat-cap">আগামীকাল</div></div>
        <div class="stat-tile"><div class="stat-num">{{ bn_number($stats['week']) }}</div><div class="stat-cap">এই সপ্তাহে</div></div>
        <div class="stat-tile"><div class="stat-num">{{ bn_number($stats['month_total']) }}</div><div class="stat-cap">এই মাসে</div></div>
        <div class="stat-tile"><div class="stat-num text-slate-500">{{ bn_number($stats['no_show_month']) }}</div><div class="stat-cap">মাসে অনুপস্থিত</div></div>
        <a href="{{ route('admin.appointments.index') }}" class="stat-tile hover:border-brand-400 transition grid place-items-center text-center">
            <span class="text-sm font-semibold text-brand-900">সব অ্যাপয়েন্টমেন্ট →</span>
        </a>
    </div>

    {{-- আজকের তালিকা --}}
    <div class="a-card overflow-hidden mb-6">
        <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-b border-brand-100">
            <h2 class="font-bold text-brand-900">আজকের সিরিয়াল — {{ fmt_date($today) }} ({{ fmt_day($today) }})</h2>
            <span class="badge badge-blue">{{ bn_number($todayList->count()) }} জন</span>
        </div>
        @if($todayList->isEmpty())
            <p class="px-5 py-8 text-center text-slate-500 text-sm">আজ কোনো সিরিয়াল নেই।</p>
        @else
            <div class="overflow-x-auto">
                <table class="a-table">
                    <thead><tr><th>সিরিয়াল</th><th>রোগী</th><th>বয়স</th><th>ধরন</th><th>সময়</th><th>অবস্থা</th><th></th></tr></thead>
                    <tbody>
                        @foreach($todayList as $a)
                            <tr>
                                <td class="font-bold text-brand-900">{{ bn_number($a->serial_no) }}</td>
                                <td>
                                    <div class="font-medium text-slate-800">{{ $a->patient_name }}</div>
                                    <div class="text-xs text-slate-400" dir="ltr">{{ $a->patient_phone }}</div>
                                </td>
                                <td class="text-sm">{{ $a->ageLabel() }}</td>
                                <td class="text-sm">{{ $a->visitLabel() }}</td>
                                <td class="text-sm" dir="ltr">{{ fmt_time($a->slotHm()) }}</td>
                                <td><span class="badge {{ $badge($a->statusTone()) }}">{{ $a->statusLabel() }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.appointments.show', $a) }}" class="a-btn a-btn-light a-btn-sm">দেখুন</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- আগামীকাল --}}
        <div class="a-card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-brand-100"><h2 class="font-bold text-brand-900">আগামীকালের সিরিয়াল</h2></div>
            @forelse($upcoming as $a)
                <div class="flex items-center gap-3 px-5 py-2.5 border-b border-slate-100 last:border-0">
                    <span class="grid place-items-center w-8 h-8 rounded-lg bg-brand-50 text-brand-900 font-bold text-sm">{{ bn_number($a->serial_no) }}</span>
                    <div class="min-w-0 flex-1"><div class="font-medium text-slate-800 truncate">{{ $a->patient_name }}</div>
                        <div class="text-xs text-slate-400" dir="ltr">{{ fmt_time($a->slotHm()) }}</div></div>
                    <span class="badge {{ $badge($a->statusTone()) }}">{{ $a->statusLabel() }}</span>
                </div>
            @empty
                <p class="px-5 py-6 text-center text-slate-500 text-sm">আগামীকাল কোনো সিরিয়াল নেই।</p>
            @endforelse
        </div>

        {{-- অপেক্ষমাণ (নিশ্চিত করা বাকি) --}}
        <div class="a-card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-brand-100"><h2 class="font-bold text-brand-900">নিশ্চিত করা বাকি</h2></div>
            @forelse($pending as $a)
                <div class="flex items-center gap-3 px-5 py-2.5 border-b border-slate-100 last:border-0">
                    <div class="min-w-0 flex-1"><div class="font-medium text-slate-800 truncate">{{ $a->patient_name }}</div>
                        <div class="text-xs text-slate-400">{{ fmt_date($a->appointment_date) }} · সিরিয়াল {{ bn_number($a->serial_no) }}</div></div>
                    <a href="{{ route('admin.appointments.show', $a) }}" class="a-btn a-btn-light a-btn-sm">দেখুন</a>
                </div>
            @empty
                <p class="px-5 py-6 text-center text-slate-500 text-sm">সব সিরিয়াল নিশ্চিত করা আছে।</p>
            @endforelse
        </div>
    </div>

@endsection
