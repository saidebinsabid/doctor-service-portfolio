@extends('layouts.public')

@section('title', __('booking.title') . ' — ' . \App\Models\Setting::get('doctor_short'))

@section('content')
<section class="section">
    <div class="container-x">

        <div class="section-head">
            <p class="eyebrow"><x-icon name="clock" class="w-3.5 h-3.5"/> {{ __('nav.book') }}</p>
            <h1 class="section-title">{{ __('booking.title') }}</h1>
            <p class="section-sub">{{ __('booking.sub') }}</p>
        </div>

        @if(session('error'))
            <div class="max-w-2xl mx-auto mb-6 rounded-xl bg-red-50 border border-red-200 px-4 py-3
                        text-sm text-red-700">{{ session('error') }}</div>
        @endif

        @unless($enabled)
            {{-- অ্যাডমিন বুকিং বন্ধ রাখলে ক্যালেন্ডারই দেখানো হয় না --}}
            <div class="max-w-xl mx-auto card p-8 text-center">
                <span class="icon-bubble bg-amber-50 text-amber-600 !w-14 !h-14 mx-auto">
                    <x-icon name="clock" class="w-7 h-7"/></span>
                <p class="mt-4 text-slate-600">
                    {{ \App\Models\Setting::bool('holiday_mode')
                        ? __('booking.holiday_mode') : __('booking.disabled') }}
                </p>
                @if($hotline = \App\Models\Setting::get('hotline'))
                    <a href="tel:{{ $hotline }}" class="btn btn-primary mt-5">
                        <x-icon name="phone" class="w-4 h-4"/> {{ bn_number($hotline) }}
                    </a>
                @endif
            </div>
        @else

        <div class="grid lg:grid-cols-2 gap-6 items-start">

            {{-- ================= ধাপ ১ ও ২: তারিখ ও সময় ================= --}}
            <div class="card p-4 sm:p-5">

                <div class="flex flex-wrap items-center justify-between gap-x-2 gap-y-3 mb-4">
                    <p class="font-bold text-brand-900 flex items-center gap-2">
                        <span class="grid place-items-center w-7 h-7 rounded-lg bg-brand-900
                                     text-white text-xs font-bold shrink-0">{{ bn_number(1) }}</span>
                        {{ __('booking.step1') }}
                    </p>

                    <div class="flex items-center gap-1 ms-auto">
                        @if($prevMonth)
                            <a href="{{ route('booking.create', ['month' => $prevMonth]) }}"
                               class="p-1.5 rounded-lg hover:bg-brand-50" aria-label="{{ __('booking.prev_month') }}">
                                <svg class="w-5 h-5 text-brand-900" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <path d="m15 18-6-6 6-6"/></svg>
                            </a>
                        @else
                            <span class="p-1.5 opacity-25"><svg class="w-5 h-5 text-brand-900"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg></span>
                        @endif

                        <span class="text-sm font-semibold text-brand-900 min-w-[6.5rem]
                                     sm:min-w-[8.5rem] text-center">
                            {{ (app()->getLocale() === 'en' ? $month->format('F') : bn_months()[$month->month - 1]) }}
                            {{ bn_number($month->year) }}
                        </span>

                        @if($nextMonth)
                            <a href="{{ route('booking.create', ['month' => $nextMonth]) }}"
                               class="p-1.5 rounded-lg hover:bg-brand-50" aria-label="{{ __('booking.next_month') }}">
                                <svg class="w-5 h-5 text-brand-900" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        @else
                            <span class="p-1.5 opacity-25"><svg class="w-5 h-5 text-brand-900"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round"><path d="m9 18 6-6-6-6"/></svg></span>
                        @endif
                    </div>
                </div>

                {{-- বারের নাম --}}
                <div class="grid grid-cols-7 gap-1 mb-1.5">
                    @foreach(range(0, 6) as $dow)
                        <div class="text-center text-[0.68rem] font-semibold text-slate-400 py-1">
                            {{ app()->getLocale() === 'en'
                                ? ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][$dow]
                                : bn_days(true)[$dow] }}
                        </div>
                    @endforeach
                </div>

                {{-- তারিখের ঘর --}}
                <div class="grid grid-cols-7 gap-1" id="calendar-grid">
                    @php $lead = (int) $month->startOfMonth()->format('w'); @endphp
                    @for($i = 0; $i < $lead; $i++)
                        <div></div>
                    @endfor

                    @foreach($calendar as $d)
                        @if($d['selectable'])
                            <a href="{{ route('booking.create', ['month' => $month->format('Y-m'), 'date' => $d['date']]) }}"
                               class="cal-day" data-date="{{ $d['date'] }}"
                               aria-pressed="{{ $selected && $selected->toDateString() === $d['date'] ? 'true' : 'false' }}"
                               title="{{ $d['label'] }}" aria-label="{{ $d['label'] }}">
                                <span class="text-[0.95rem] leading-none">{{ $d['day_bn'] }}</span>
                                <span class="cal-meta">{{ $d['open_bn'] }}</span>
                            </a>
                        @else
                            <span class="cal-day pointer-events-none opacity-100"
                                  aria-disabled="true" title="{{ $d['label'] }}"
                                  style="background:var(--color-slate-50);border-color:var(--color-slate-100);color:var(--color-slate-300)">
                                <span class="text-[0.95rem] leading-none">{{ $d['day_bn'] }}</span>
                            </span>
                        @endif
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-4 pt-4
                            border-t border-brand-100 text-[0.7rem] text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded border-[1.5px] border-brand-100 bg-white"></span>
                        {{ __('booking.legend_open') }}</span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-slate-100"></span>{{ __('booking.legend_closed') }}</span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-brand-900"></span>{{ __('booking.legend_picked') }}</span>
                    <span class="w-full text-[0.68rem] text-slate-400">{{ __('booking.legend_count') }}</span>
                </div>

                {{-- ---------- ধাপ ২: সময় ---------- --}}
                <div id="slot-wrap" class="mt-5 pt-5 border-t border-brand-100"
                     data-slots-url="{{ route('booking.slots') }}"
                     data-step-two="{{ bn_number(2) }}"
                     data-step-two-label="{{ __('booking.step2') }}">
                    @if(! $selected)
                        <p class="text-center text-sm text-slate-400 py-3">
                            {{ __('booking.pick_date_first') }}</p>
                    @elseif($day && $day['status'] === \App\Services\SlotService::OPEN)
                        <p class="font-bold text-brand-900 flex items-center gap-2 mb-1">
                            <span class="grid place-items-center w-7 h-7 rounded-lg bg-brand-900
                                         text-white text-xs font-bold">{{ bn_number(2) }}</span>
                            {{ __('booking.step2') }}
                        </p>
                        <p class="text-xs text-slate-500 mb-3 ms-9">
                            {{ fmt_day($selected) }}, {{ fmt_date($selected) }}</p>

                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-1.5">
                            @foreach($day['slots'] as $slot)
                                <button type="button" class="slot"
                                        data-time="{{ $slot['time'] }}"
                                        data-serial="{{ $slot['serial'] }}"
                                        aria-pressed="{{ old('slot_time') === $slot['time'] ? 'true' : 'false' }}"
                                        @disabled(! $slot['available'])>
                                    {{ fmt_time($slot['time']) }}
                                </button>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-sm text-slate-400 py-3">
                            {{ $day['reason'] ?? __('booking.full') }}</p>
                    @endif
                </div>
            </div>

            {{-- ================= ধাপ ৩: রোগীর তথ্য ================= --}}
            @include('public.partials.booking-form')

        </div>
        @endunless

    </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/booking.js')
@endpush
