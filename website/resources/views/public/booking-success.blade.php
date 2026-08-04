@extends('layouts.public')

@section('title', __('booking.ok_title') . ' — ' . __('booking.f_serial') . ' ' . bn_number($a->serial_no))

@push('head')
    {{-- রোগীর তথ্যসহ পাতা — গুগলে আসা উচিত নয় --}}
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
<section class="section">
    <div class="container-x">
        <div class="max-w-2xl mx-auto">

            <div class="card print-slip p-6 md:p-8">

                <div class="text-center">
                    <span class="grid place-items-center w-16 h-16 rounded-full bg-wa-500/10
                                 text-wa-600 mx-auto">
                        <x-icon name="check" class="w-8 h-8"/>
                    </span>
                    <h1 class="mt-4 text-2xl">{{ __('booking.ok_title') }}</h1>
                    <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">{{ __('booking.ok_sub') }}</p>
                </div>

                <dl class="mt-7 divide-y divide-brand-100 border-y border-brand-100">
                    @foreach([
                        [__('booking.f_serial'), bn_number($a->serial_no), true],
                        [__('booking.f_date'),   fmt_date($a->appointment_date) . ' (' . fmt_day($a->appointment_date) . ')', false],
                        [__('booking.f_time'),   fmt_time($a->slotHm()), false],
                        [__('booking.f_patient'), $a->patient_name, false],
                        [__('booking.f_chamber'), $a->chamber->name, false],
                    ] as [$label, $value, $strong])
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500 shrink-0">{{ $label }}</dt>
                            <dd class="text-end {{ $strong
                                ? 'text-base font-extrabold text-brand-900 tracking-wide'
                                : 'text-sm font-semibold text-brand-900' }}">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>

                <p class="mt-4 text-[0.72rem] text-slate-400 text-center">{{ __('booking.time_note') }}</p>

                {{-- ⭐ Phase 1 — ফ্রি WhatsApp নিশ্চিতকরণ।
                     বার্তাটি আগে থেকেই লেখা থাকে, রোগী শুধু Send চাপেন।
                     Phase 2 (Cloud API) চালু হলে এই বাটনের বদলে
                     স্বয়ংক্রিয় বার্তার নিশ্চয়তা দেখানো হবে। --}}
                @if($waLink && ! $automatic)
                    <div class="mt-6 rounded-xl bg-wa-500/10 border border-wa-500/25 p-4 no-print">
                        <a href="{{ $waLink }}" target="_blank" rel="noopener"
                           class="btn btn-wa w-full !py-3.5">
                            <x-icon name="phone" class="w-5 h-5"/> {{ __('booking.wa_cta') }}
                        </a>
                        <p class="mt-2.5 text-[0.73rem] text-slate-600 text-center leading-relaxed">
                            {{ __('booking.wa_note') }}</p>
                    </div>
                @elseif($automatic)
                    <p class="mt-6 rounded-xl bg-wa-500/10 border border-wa-500/25 p-4
                              text-sm text-wa-700 text-center flex items-center justify-center gap-2">
                        <x-icon name="check" class="w-4 h-4 shrink-0"/>
                        {{ app()->getLocale() === 'en'
                            ? 'A confirmation message has been sent to your WhatsApp.'
                            : 'আপনার হোয়াটসঅ্যাপে নিশ্চিতকরণ বার্তা পাঠানো হয়েছে।' }}
                    </p>
                @endif

                <div class="mt-3 grid sm:grid-cols-2 gap-2.5 no-print">
                    <a href="{{ $calendarLink }}" target="_blank" rel="noopener"
                       class="btn btn-outline !py-2.5 !text-sm">
                        <x-icon name="clock" class="w-4 h-4"/> {{ __('booking.add_calendar') }}
                    </a>
                    <button type="button" onclick="window.print()" class="btn btn-outline !py-2.5 !text-sm">
                        <x-icon name="book" class="w-4 h-4"/> {{ __('common.print') }}
                    </button>
                </div>

                <p class="mt-5 text-xs text-amber-700 bg-amber-50 border border-amber-200
                          rounded-lg px-3 py-2.5 text-center">{{ __('booking.arrive_early') }}</p>

                <div class="mt-5 pt-5 border-t border-brand-100 text-center text-xs text-slate-500">
                    <p class="font-semibold text-brand-900">{{ $a->chamber->name }}</p>
                    <p class="mt-1">{{ $a->chamber->address }}</p>
                    @if($a->chamber->hotline)
                        <p class="mt-1">{{ __('chm.hotline') }}: {{ $a->chamber->hotline }}</p>
                    @endif
                </div>
            </div>

            <div class="text-center mt-5 no-print flex flex-wrap justify-center gap-x-5 gap-y-2">
                <a href="{{ route('home') }}"
                   class="text-sm font-semibold text-brand-700 hover:text-brand-900 underline underline-offset-4">
                    {{ __('common.backHome') }}</a>
                <a href="{{ route('booking.status') }}"
                   class="text-sm font-semibold text-brand-700 hover:text-brand-900 underline underline-offset-4">
                    {{ __('nav.status') }}</a>
            </div>

        </div>
    </div>
</section>
@endsection
