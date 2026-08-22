@extends('layouts.public')

@php use App\Models\Setting; $en = app()->getLocale() === 'en'; @endphp

@section('title', ($en ? 'Book a Serial' : 'সিরিয়াল নিন') . ' — ' . Setting::get('doctor_short'))

@section('content')
{{-- মোবাইলে কমপ্যাক্ট (এক স্ক্রিনে), ডেস্কটপে খোলামেলা — sm: দিয়ে আলাদা।
     মোবাইলের মান (prefix ছাড়া) অপরিবর্তিত। --}}
<section class="pt-2 pb-8 sm:pt-10 sm:pb-16">
    <div class="container-x max-w-lg mx-auto">

        @if(session('error'))
            <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3
                        text-sm text-red-700">{{ session('error') }}</div>
        @endif

        @unless($enabled)
            {{-- অ্যাডমিন বুকিং বন্ধ রাখলে ফর্মই দেখানো হয় না --}}
            <div class="card p-8 text-center">
                <span class="icon-bubble bg-amber-50 text-amber-600 !w-14 !h-14 mx-auto">
                    <x-icon name="clock" class="w-7 h-7"/></span>
                <p class="mt-4 text-slate-600">
                    {{ Setting::bool('holiday_mode') ? __('booking.holiday_mode') : __('booking.disabled') }}
                </p>
                @if($hotline = ($chamber->hotline ?? Setting::get('hotline')))
                    <a href="tel:{{ $hotline }}" class="btn btn-primary mt-5">
                        <x-icon name="phone" class="w-4 h-4"/> {{ bn_number($hotline) }}
                    </a>
                @endif
            </div>
        @else

        <div class="card p-4 sm:p-8">

            {{-- কেন্দ্রীভূত শিরোনাম — ডেস্কটপে বড় --}}
            <h1 class="text-xl sm:text-3xl font-extrabold text-brand-900 text-center">
                {{ $en ? 'Appointment Form' : 'সিরিয়াল নিন' }}
            </h1>

            {{-- একাধিক চেম্বার হলে ছোট সুইচার --}}
            @if($chambers->count() > 1)
                <div class="grid grid-cols-2 gap-2 sm:gap-3 mt-2.5 sm:mt-5">
                    @foreach($chambers as $c)
                        <a href="{{ route('booking.create', ['chamber' => $c->id]) }}"
                           class="rounded-lg border-2 px-3 py-1.5 sm:py-3 text-center text-sm sm:text-base font-semibold leading-tight transition
                                  {{ $c->id === $chamber->id
                                        ? 'border-brand-500 bg-brand-50 text-brand-900'
                                        : 'border-brand-100 text-slate-500 hover:border-brand-300' }}">
                            {{ $c->shortLabel() }}
                        </a>
                    @endforeach
                </div>
            @endif

            @if(empty($dateOptions))
                {{-- সামনের ৩০ দিনে কোনো খোলা তারিখ নেই — বিরল, তবু নিরাপদ বার্তা --}}
                <p class="mt-5 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800">
                    {{ $en ? 'No open dates right now. Please call.' : 'এই মুহূর্তে কোনো খোলা তারিখ নেই। অনুগ্রহ করে ফোন করুন।' }}
                </p>
                @if($hotline = ($chamber->hotline ?? Setting::get('hotline')))
                    <a href="tel:{{ $hotline }}" class="btn btn-primary w-full mt-3">
                        <x-icon name="phone" class="w-4 h-4"/> {{ bn_number($hotline) }}</a>
                @endif
            @else

            <form method="POST" action="{{ route('booking.store') }}" id="booking-form" class="mt-3 sm:mt-6 space-y-2 sm:space-y-4"
                  novalidate
                  data-err-name="{{ __('validation_custom.name_required') }}"
                  data-err-phone="{{ __('validation_custom.phone_invalid') }}">
                @csrf
                <input type="hidden" name="chamber_id" value="{{ $chamber->id }}">

                {{-- ধাপ ১: তারিখ নির্বাচন — স্ট্যাক করা সারি।
                     নির্বাচিত = নীল + সাদা টিক, বাকিগুলো = হালকা সবুজ + ডট।
                     রেডিও বাটন, তাই জাভাস্ক্রিপ্ট ছাড়াও কাজ করে। --}}
                <div>
                    <label class="block text-sm sm:text-base font-semibold text-brand-900 mb-1 sm:mb-2">
                        {{ $en ? 'Select Date' : 'তারিখ নির্বাচন করুন' }}</label>

                    <div class="space-y-1 sm:space-y-2">
                        @foreach($dateOptions as $opt)
                            <label class="flex items-center gap-3 px-4 py-2 sm:py-3.5 rounded-lg cursor-pointer transition
                                          bg-green-50 text-slate-700
                                          has-[:checked]:bg-sky2-500 has-[:checked]:text-white
                                          has-[:checked]:shadow-sm">
                                <input type="radio" name="appointment_date" value="{{ $opt['date'] }}"
                                       class="peer sr-only" required
                                       {{ (old('appointment_date', $dateOptions[0]['date']) === $opt['date']) ? 'checked' : '' }}>
                                {{-- ডট (অনির্বাচিত) --}}
                                <span class="w-5 h-5 rounded-full border-2 border-slate-300 grid place-items-center
                                             shrink-0 peer-checked:hidden">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                </span>
                                {{-- টিক (নির্বাচিত) --}}
                                <svg class="w-5 h-5 shrink-0 hidden peer-checked:block" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="3"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/></svg>
                                {{-- আজ/কাল আগে (বোল্ড), তারপর সংক্ষিপ্ত তারিখ — এক লাইনে --}}
                                <span class="text-sm sm:text-base leading-tight">
                                    <span class="font-bold">{{ $opt['label'] }}</span><span class="font-normal opacity-80"> · {{ $opt['sub'] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ধাপ ২: রোগীর নাম (আইকনসহ) --}}
                <div class="relative">
                    <input class="input pe-11" id="f-name" name="patient_name" required maxlength="100"
                           value="{{ old('patient_name') }}"
                           aria-label="{{ __('booking.patient_name') }}"
                           placeholder="{{ __('booking.patient_name') }} *">
                    <span class="absolute end-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <x-icon name="users" class="w-5 h-5"/>
                    </span>
                    @error('patient_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- ধাপ ৩: মোবাইল নম্বর (আইকনসহ) --}}
                <div class="relative">
                    <input class="input pe-11" id="f-phone" name="patient_phone" type="tel" required
                           inputmode="numeric" value="{{ old('patient_phone') }}"
                           aria-label="{{ __('booking.phone') }}"
                           placeholder="{{ __('booking.phone') }} *">
                    <span class="absolute end-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <x-icon name="phone" class="w-5 h-5"/>
                    </span>
                    @error('patient_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- হানিপট: বট এই ঘরটি পূরণ করে, মানুষ দেখতেই পায় না --}}
                <input type="text" name="website" tabindex="-1" autocomplete="off"
                       class="hidden" aria-hidden="true">

                <p id="form-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-200
                                          rounded-lg px-3 py-2"></p>

                {{-- সাবমিট — ক্লিক করলেই অটো সিরিয়াল বসে যায় (সময় বাছতে হয় না) --}}
                <button type="submit" id="booking-submit" class="btn btn-primary w-full !py-3.5 !text-base">
                    {{ __('booking.submit') }}
                </button>

                {{-- সরাসরি ফোনে সিরিয়াল নিতে চাইলে।
                     whitespace-normal দিয়ে .btn-এর nowrap ওভাররাইড — নইলে লম্বা লেখা
                     র‍্যাপ না হয়ে কার্ডের বাইরে বেরিয়ে যায়। --}}
                @if($hotline = ($chamber->hotline ?? Setting::get('hotline')))
                    <a href="tel:{{ $hotline }}"
                       class="btn btn-outline w-full !py-2.5 justify-center whitespace-normal
                              text-center leading-snug !text-sm">
                        <x-icon name="phone" class="w-4 h-4 shrink-0"/>
                        <span>{{ $en ? 'Or call to book' : 'অথবা ফোনে সিরিয়াল নিন' }} · {{ bn_number($hotline) }}</span>
                    </a>
                @endif
            </form>

            @endif
        </div>

        @endunless

    </div>
</section>
@endsection

@push('scripts')
<script>
/* ছোট সুবিধা: দুইবার চাপ দিয়ে দুটি সিরিয়াল নেওয়া ঠেকানো + দ্রুত ভুল-বার্তা।
   জাভাস্ক্রিপ্ট বন্ধ থাকলেও ফর্ম সার্ভারে যাচাই হয়ে ঠিকই কাজ করে। */
(function () {
    const form = document.getElementById('booking-form');
    if (!form) return;
    const submit = document.getElementById('booking-submit');
    const errorBox = document.getElementById('form-error');

    form.addEventListener('submit', function (e) {
        const phone = (form.patient_phone.value || '').replace(/\D/g, '');
        let problem = '';
        if (!form.patient_name.value.trim()) problem = form.dataset.errName;
        else if (!/^01[3-9]\d{8}$/.test(phone)) problem = form.dataset.errPhone;

        if (problem) {
            e.preventDefault();
            errorBox.textContent = problem;
            errorBox.classList.remove('hidden');
            errorBox.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            return;
        }
        errorBox.classList.add('hidden');
        submit.disabled = true;
        submit.classList.add('opacity-70');
    });
})();
</script>
@endpush
