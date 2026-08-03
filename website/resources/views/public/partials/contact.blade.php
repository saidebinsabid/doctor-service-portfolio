@php
    use App\Models\Setting;

    $cards = array_values(array_filter([
        Setting::get('hotline') ? [
            'icon' => 'phone', 'tone' => 'brand', 'label' => __('chm.hotline'),
            'value' => bn_number(Setting::get('hotline')),
            'href' => 'tel:' . Setting::get('hotline'),
        ] : null,
        Setting::get('whatsapp') ? [
            'icon' => 'phone', 'tone' => 'green', 'label' => __('common.whatsapp'),
            'value' => bn_number(Setting::get('whatsapp')),
            'href' => 'https://wa.me/' . intl_bd_phone(Setting::get('whatsapp')),
        ] : null,
        Setting::get('email') ? [
            'icon' => 'mail', 'tone' => 'sky', 'label' => __('common.email'),
            'value' => Setting::get('email'),
            'href' => 'mailto:' . Setting::get('email'),
        ] : null,
        $chamber ? [
            'icon' => 'pin', 'tone' => 'rose', 'label' => __('chm.address'),
            'value' => $chamber->address,
            'href' => $chamber->mapDirectionsUrl(),
        ] : null,
    ]));
@endphp

<div class="container-x">
    <div class="section-head">
        <p class="eyebrow"><x-icon name="phone" class="w-3.5 h-3.5"/> {{ __('cnt.eyebrow') }}</p>
        <h2 class="section-title">{{ __('cnt.title') }}</h2>
        <p class="section-sub">{{ __('cnt.sub') }}</p>
    </div>

    <div class="grid-auto">
        @foreach($cards as $c)
            <a href="{{ $c['href'] }}"
               @if(Str::startsWith($c['href'], 'http')) target="_blank" rel="noopener" @endif
               class="card card-hover p-5 flex items-start gap-3.5">
                <span class="icon-bubble {{ tone_classes($c['tone']) }}">
                    <x-icon :name="$c['icon']" class="w-5 h-5"/></span>
                <span class="min-w-0">
                    <span class="block text-xs text-slate-500">{{ $c['label'] }}</span>
                    <span class="block font-semibold text-brand-900 text-[0.9rem] mt-0.5 break-words">
                        {{ $c['value'] }}</span>
                </span>
            </a>
        @endforeach
    </div>

    {{-- যোগাযোগ ফর্ম --}}
    <div class="mt-8 max-w-2xl mx-auto">
        @if(session('success'))
            <p class="mb-4 rounded-xl bg-wa-500/10 border border-wa-500/30 px-4 py-3
                      text-sm text-wa-700 flex items-center gap-2">
                <x-icon name="check" class="w-4 h-4 shrink-0"/> {{ session('success') }}
            </p>
        @endif

        <form method="POST" action="{{ route('contact.store') }}" class="card p-5 sm:p-6 space-y-3.5">
            @csrf

            <div class="grid sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="label req" for="c-name">{{ __('contact.name') }}</label>
                    <input class="input" id="c-name" name="name" required
                           value="{{ old('name') }}" maxlength="100">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="c-phone">{{ __('booking.phone') }}</label>
                    <input class="input" id="c-phone" name="phone" type="tel"
                           inputmode="numeric" value="{{ old('phone') }}" placeholder="01XXXXXXXXX">
                    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="label" for="c-subject">{{ __('contact.subject') }}
                    <span class="font-normal text-slate-400">({{ __('common.optional') }})</span></label>
                <input class="input" id="c-subject" name="subject" value="{{ old('subject') }}" maxlength="150">
            </div>

            <div>
                <label class="label req" for="c-message">{{ __('contact.message') }}</label>
                <textarea class="input" id="c-message" name="message" rows="4" required
                          maxlength="2000">{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- হানিপট: বট এই ঘরটি পূরণ করে, মানুষ দেখতেই পায় না --}}
            <input type="text" name="website" tabindex="-1" autocomplete="off"
                   class="hidden" aria-hidden="true">

            <button type="submit" class="btn btn-primary w-full !py-3">
                <x-icon name="mail" class="w-4.5 h-4.5"/> {{ __('common.submit') }}
            </button>
        </form>
    </div>
</div>
