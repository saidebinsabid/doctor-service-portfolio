@extends('admin.layouts.app')
@section('title', 'ক্যালেন্ডার')
@section('heading', 'অ্যাপয়েন্টমেন্ট ক্যালেন্ডার')

@php
    $start = $month->startOfMonth();
    $daysInMonth = $month->daysInMonth;
    $lead = (int) $start->format('w'); // 0=রবি
    $prev = $month->subMonth()->format('Y-m');
    $next = $month->addMonth()->format('Y-m');
@endphp

@section('content')
    <div class="flex items-center justify-between gap-3 mb-4">
        <a href="{{ route('admin.appointments.calendar', ['month' => $prev]) }}" class="a-btn a-btn-light a-btn-sm">← আগের মাস</a>
        <h2 class="font-bold text-brand-900 text-lg">{{ bn_months()[(int)$month->format('n')-1] }} {{ bn_number($month->format('Y')) }}</h2>
        <a href="{{ route('admin.appointments.calendar', ['month' => $next]) }}" class="a-btn a-btn-light a-btn-sm">পরের মাস →</a>
    </div>

    <div class="a-card p-3 lg:p-4">
        <div class="grid grid-cols-7 gap-1.5 text-center text-xs font-semibold text-slate-400 mb-1.5">
            @foreach(bn_days(true) as $d)<div>{{ $d }}</div>@endforeach
        </div>
        <div class="grid grid-cols-7 gap-1.5">
            @for($i = 0; $i < $lead; $i++)<div></div>@endfor
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateStr = $start->addDays($day - 1)->toDateString();
                    $count = (int) ($counts[$dateStr] ?? 0);
                @endphp
                <a href="{{ route('admin.appointments.index', ['date' => $dateStr]) }}"
                   class="aspect-square rounded-lg border flex flex-col items-center justify-center gap-1 transition
                          {{ $count ? 'border-brand-200 bg-brand-50 hover:border-brand-400' : 'border-slate-100 hover:bg-slate-50' }}">
                    <span class="text-sm font-medium text-brand-900">{{ bn_number($day) }}</span>
                    @if($count)<span class="badge badge-blue !py-0 !px-1.5 text-[.62rem]">{{ bn_number($count) }}</span>@endif
                </a>
            @endfor
        </div>
    </div>
@endsection
