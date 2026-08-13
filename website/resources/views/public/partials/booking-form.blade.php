@php use App\Models\Setting; $en = app()->getLocale() === 'en'; @endphp

<div class="card p-4 sm:p-5">
    <p class="font-bold text-brand-900 flex items-center gap-2 mb-3">
        <span class="grid place-items-center w-7 h-7 rounded-lg text-xs font-bold
                     {{ $selected ? 'bg-brand-900 text-white' : 'bg-slate-100 text-slate-400' }}">
            {{ bn_number(3) }}</span>
        {{ __('booking.step3') }}
    </p>

    {{-- ছোট নির্দেশনা: শুধু নাম ও ফোন দিলেই হবে --}}
    <p class="text-sm text-brand-800 bg-brand-50 border border-brand-100 rounded-lg px-3 py-2.5 mb-4">
        {{ $en
            ? 'Just your name and mobile number are enough to get a serial.'
            : 'শুধু নাম ও মোবাইল নম্বর দিলেই সিরিয়াল পাবেন।' }}
    </p>

    <form method="POST" action="{{ route('booking.store') }}" id="booking-form"
          class="space-y-3.5" novalidate
          data-err-name="{{ __('validation_custom.name_required') }}"
          data-err-phone="{{ __('validation_custom.phone_invalid') }}"
          data-err-time="{{ __('validation_custom.time_required') }}">
        @csrf
        <input type="hidden" name="chamber_id" value="{{ $chamber->id }}">
        <input type="hidden" name="appointment_date" id="f-date" value="{{ old('appointment_date', $selected?->toDateString()) }}">
        <input type="hidden" name="slot_time" id="f-time" value="{{ old('slot_time') }}">

        <div class="grid sm:grid-cols-2 gap-3.5">
            <div>
                <label class="label req !text-red-600" for="f-name">{{ __('booking.patient_name') }}</label>
                <input class="input" id="f-name" name="patient_name" required maxlength="100"
                       value="{{ old('patient_name') }}"
                       placeholder="{{ $en ? "Enter the child's name" : 'শিশুর নাম লিখুন' }}">
                @error('patient_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label req !text-red-600" for="f-phone">{{ __('booking.phone') }}</label>
                <input class="input" id="f-phone" name="patient_phone" type="tel" required
                       inputmode="numeric" value="{{ old('patient_phone') }}"
                       placeholder="{{ $en ? 'Enter mobile number' : 'মোবাইল নম্বর লিখুন' }}">
                <p class="mt-1 text-[0.7rem] text-slate-400">{{ __('booking.phone_hint') }}</p>
                @error('patient_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- হানিপট: বট এই ঘরটি পূরণ করে, মানুষ দেখতেই পায় না --}}
        <input type="text" name="website" tabindex="-1" autocomplete="off"
               class="hidden" aria-hidden="true">

        <p id="form-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-200
                                  rounded-lg px-3 py-2"></p>

        {{-- বাটন নির্দেশনা --}}
        <p class="text-xs text-slate-500 text-center">
            {{ $en ? 'Press the button to confirm your serial.' : 'সিরিয়াল নিশ্চিত করতে বাটনটি চাপুন।' }}
            {{ $en
                ? "If you don't pick a time, we'll assign the next open slot that day."
                : 'সময় না বাছলে ওই দিনের পরের খালি সময়টি দিয়ে দেব।' }}
        </p>

        <button type="submit" id="booking-submit" class="btn btn-primary w-full !py-3.5"
                @disabled(! $selected)>
            <x-icon name="clock" class="w-4.5 h-4.5"/> {{ __('booking.submit') }}
        </button>

        {{-- সরাসরি ফোনে সিরিয়াল — যে চেম্বার নির্বাচিত, তার নিজস্ব হটলাইন ও নাম --}}
        @if($hotline = ($chamber->hotline ?: Setting::get('hotline')))
            <a href="tel:{{ $hotline }}" class="btn btn-primary w-full !py-3 justify-center whitespace-normal text-center leading-snug">
                <x-icon name="phone" class="w-4.5 h-4.5 shrink-0"/>
                {{ __('chm.callOperator', ['name' => $chamber->shortLabel()]) }}
            </a>
        @endif

        @if($note = Setting::get('booking_note'))
            <p class="text-[0.7rem] text-slate-400 text-center leading-relaxed">{{ $note }}</p>
        @endif
    </form>
</div>
