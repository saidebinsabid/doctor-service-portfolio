@extends('layouts.public')

@section('title', __('status.title'))

@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
<section class="section">
    <div class="container-x">
        <div class="max-w-xl mx-auto">

            <div class="section-head">
                <p class="eyebrow"><x-icon name="book" class="w-3.5 h-3.5"/> {{ __('nav.status') }}</p>
                <h1 class="section-title">{{ __('status.title') }}</h1>
                <p class="section-sub">{{ __('status.sub') }}</p>
            </div>

            <form method="POST" action="{{ route('booking.status.lookup') }}" class="card p-5 sm:p-6 space-y-3.5">
                @csrf

                <div>
                    <label class="label req" for="s-name">{{ __('booking.patient_name') }}</label>
                    <input class="input" id="s-name" name="name" required maxlength="100"
                           value="{{ old('name') }}"
                           placeholder="{{ app()->getLocale() === 'en' ? "Enter the patient's name" : 'রোগীর নাম লিখুন' }}">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label req" for="s-phone">{{ __('booking.phone') }}</label>
                    <input class="input" id="s-phone" name="phone" type="tel" required
                           inputmode="numeric" value="{{ old('phone') }}" placeholder="01XXXXXXXXX">
                    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn btn-primary w-full !py-3">
                    <x-icon name="book" class="w-4.5 h-4.5"/> {{ __('common.search') }}
                </button>
            </form>

            {{-- ⚠️ কোড ও নম্বর — দুটোই মিলতে হয়।
                 শুধু কোড দিয়ে দেখা গেলে কেউ কোড অনুমান করে অন্য
                 রোগীর নাম ও ফোন নম্বর দেখে ফেলতে পারত। --}}
            @if(($searched ?? false) && ! $appointment)
                <p class="mt-5 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3
                          text-sm text-amber-800">{{ __('status.not_found') }}</p>
            @endif

            @if($appointment)
                @php $tone = $appointment->statusTone(); @endphp
                <div class="mt-6 card p-6">
                    <div class="flex items-center justify-between gap-3 pb-4 border-b border-brand-100">
                        <p class="font-bold text-brand-900">
                            {{ __('booking.f_serial') }}: <span class="text-lg">{{ bn_number($appointment->serial_no) }}</span></p>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full ring-1
                                     {{ badge_classes($tone) }}">{{ $appointment->statusLabel() }}</span>
                    </div>

                    <dl class="divide-y divide-brand-100">
                        @foreach([
                            [__('booking.f_date'),    fmt_date($appointment->appointment_date) . ' (' . fmt_day($appointment->appointment_date) . ')'],
                            [__('booking.f_time'),    fmt_time($appointment->slotHm())],
                            [__('booking.f_patient'), $appointment->patient_name],
                            [__('booking.f_visit'),   $appointment->visitLabel()],
                            [__('booking.f_chamber'), $appointment->chamber->name],
                        ] as [$label, $value])
                            <div class="flex items-start justify-between gap-4 py-3">
                                <dt class="text-sm text-slate-500 shrink-0">{{ $label }}</dt>
                                <dd class="text-sm font-semibold text-brand-900 text-end">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    @if(! $appointment->isReleased())
                        <p class="mt-4 text-xs text-amber-700 bg-amber-50 border border-amber-200
                                  rounded-lg px-3 py-2.5 text-center">{{ __('booking.arrive_early') }}</p>
                    @endif
                </div>
            @endif

        </div>
    </div>
</section>
@endsection
