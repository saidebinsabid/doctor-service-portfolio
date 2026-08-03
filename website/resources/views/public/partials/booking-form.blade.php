@php use App\Models\Setting; @endphp

<div class="card p-4 sm:p-5">
    <p class="font-bold text-brand-900 flex items-center gap-2 mb-4">
        <span class="grid place-items-center w-7 h-7 rounded-lg text-xs font-bold
                     {{ $selected ? 'bg-brand-900 text-white' : 'bg-slate-100 text-slate-400' }}">
            {{ bn_number(3) }}</span>
        {{ __('booking.step3') }}
    </p>

    {{-- নির্বাচিত তারিখ ও সময়ের সারাংশ — সময় বাছাই করলে JS ভরে দেয় --}}
    <div id="booking-summary"
         class="rounded-xl bg-brand-50 border border-brand-100 p-4 mb-5 {{ $selected ? '' : 'hidden' }}">
        <p class="text-xs font-semibold text-brand-700 mb-2.5">{{ __('booking.summary') }}</p>
        <dl class="grid grid-cols-3 gap-3 text-center">
            <div>
                <dt class="text-[0.68rem] text-slate-500">{{ __('booking.f_date') }}</dt>
                <dd class="text-sm font-bold text-brand-900 mt-0.5" data-summary="date">
                    {{ $selected ? bn_number($selected->format('j')) . ' ' .
                        (app()->getLocale() === 'en' ? $selected->format('M') : bn_months()[$selected->month - 1]) : '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-[0.68rem] text-slate-500">{{ __('booking.f_time') }}</dt>
                <dd class="text-sm font-bold text-brand-900 mt-0.5" data-summary="time">—</dd>
            </div>
            <div>
                <dt class="text-[0.68rem] text-slate-500">{{ __('booking.f_serial') }}</dt>
                <dd class="text-sm font-bold text-brand-900 mt-0.5" data-summary="serial">—</dd>
            </div>
        </dl>
    </div>

    <form method="POST" action="{{ route('booking.store') }}" id="booking-form"
          class="space-y-3.5" novalidate
          data-err-name="{{ __('validation_custom.name_required') }}"
          data-err-phone="{{ __('validation_custom.phone_invalid') }}"
          data-err-age="{{ __('validation_custom.age_required') }}"
          data-err-time="{{ __('validation_custom.time_required') }}">
        @csrf
        <input type="hidden" name="chamber_id" value="{{ $chamber->id }}">
        <input type="hidden" name="appointment_date" id="f-date" value="{{ old('appointment_date', $selected?->toDateString()) }}">
        <input type="hidden" name="slot_time" id="f-time" value="{{ old('slot_time') }}">

        <div class="grid sm:grid-cols-2 gap-3.5">
            <div>
                <label class="label req" for="f-name">{{ __('booking.patient_name') }}</label>
                <input class="input" id="f-name" name="patient_name" required maxlength="100"
                       value="{{ old('patient_name') }}"
                       placeholder="{{ app()->getLocale() === 'en' ? 'Full name of the child' : 'শিশুর পূর্ণ নাম' }}">
                @error('patient_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label req" for="f-phone">{{ __('booking.phone') }}</label>
                <input class="input" id="f-phone" name="patient_phone" type="tel" required
                       inputmode="numeric" placeholder="01XXXXXXXXX" value="{{ old('patient_phone') }}">
                <p class="mt-1 text-[0.7rem] text-slate-400">{{ __('booking.phone_hint') }}</p>
                @error('patient_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-3.5">
            <div>
                <label class="label req" for="f-age">{{ __('booking.age') }}</label>
                <div class="flex gap-2">
                    <input class="input flex-1" id="f-age" name="patient_age" type="number"
                           min="0" max="200" required inputmode="numeric" value="{{ old('patient_age') }}">
                    <select class="input w-28" name="patient_age_unit" aria-label="{{ __('booking.age') }}">
                        @foreach(['year', 'month', 'day'] as $unit)
                            <option value="{{ $unit }}" @selected(old('patient_age_unit') === $unit)>
                                {{ \App\Models\Appointment::AGE_UNITS[$unit][app()->getLocale()]
                                    ?? \App\Models\Appointment::AGE_UNITS[$unit]['bn'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('patient_age')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="f-gender">{{ __('booking.gender') }}</label>
                <select class="input" id="f-gender" name="gender">
                    <option value="male" @selected(old('gender') === 'male')>{{ __('booking.male') }}</option>
                    <option value="female" @selected(old('gender') === 'female')>{{ __('booking.female') }}</option>
                </select>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-3.5">
            <div>
                <label class="label" for="f-guardian">{{ __('booking.guardian_name') }}
                    <span class="font-normal text-slate-400">({{ __('common.optional') }})</span></label>
                <input class="input" id="f-guardian" name="guardian_name" maxlength="100"
                       value="{{ old('guardian_name') }}">
            </div>
            <div>
                <label class="label" for="f-visit">{{ __('booking.visit_type') }}</label>
                <select class="input" id="f-visit" name="visit_type">
                    @foreach(['new', 'followup', 'report'] as $type)
                        <option value="{{ $type }}" @selected(old('visit_type') === $type)>
                            {{ \App\Models\Appointment::VISIT_LABELS[$type][app()->getLocale()]
                                ?? \App\Models\Appointment::VISIT_LABELS[$type]['bn'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="label" for="f-problem">{{ __('booking.problem') }}
                <span class="font-normal text-slate-400">({{ __('common.optional') }})</span></label>
            <textarea class="input" id="f-problem" name="problem" rows="2" maxlength="500"
                      placeholder="{{ __('booking.problem_hint') }}">{{ old('problem') }}</textarea>
        </div>

        {{-- হানিপট: বট এই ঘরটি পূরণ করে, মানুষ দেখতেই পায় না --}}
        <input type="text" name="website" tabindex="-1" autocomplete="off"
               class="hidden" aria-hidden="true">

        <p id="form-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-200
                                  rounded-lg px-3 py-2"></p>

        <button type="submit" id="booking-submit" class="btn btn-primary w-full !py-3.5"
                @disabled(! $selected)>
            <x-icon name="clock" class="w-4.5 h-4.5"/> {{ __('booking.submit') }}
        </button>

        @if($note = Setting::get('booking_note'))
            <p class="text-[0.7rem] text-slate-400 text-center leading-relaxed">{{ $note }}</p>
        @endif
    </form>
</div>
