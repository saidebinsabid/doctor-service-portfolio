@php
    use App\Models\Setting;

    $social = array_filter([
        ['Facebook', Setting::get('facebook'),
            '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>'],
        ['YouTube', Setting::get('youtube'),
            '<path d="M22.5 6.4a2.8 2.8 0 0 0-2-2C18.8 4 12 4 12 4s-6.8 0-8.5.4a2.8 2.8 0 0 0-2 2A29 29 0 0 0 1 12a29 29 0 0 0 .5 5.6 2.8 2.8 0 0 0 2 2C5.2 20 12 20 12 20s6.8 0 8.5-.4a2.8 2.8 0 0 0 2-2A29 29 0 0 0 23 12a29 29 0 0 0-.5-5.6z"/><path d="m10 15 5-3-5-3z"/>'],
        ['WhatsApp', 'https://wa.me/' . intl_bd_phone(Setting::get('whatsapp')),
            '<path d="M21 11.5a8.4 8.4 0 0 1-12.6 7.3L3 20.5l1.8-5.3A8.5 8.5 0 1 1 21 11.5z"/>'],
    ], fn ($s) => filled($s[1]));

    $links = [
        [route('home') . '#about',    __('nav.about')],
        [route('home') . '#services', __('nav.services')],
        [route('home') . '#chamber',  __('nav.chamber')],
        [route('booking.create'),     __('nav.book')],
        [route('booking.status'),     __('nav.status')],
    ];
@endphp

<footer class="bg-brand-900 text-white/75">
    <div class="container-x py-12">
        <div class="grid md:grid-cols-3 gap-9">

            <div>
                <div class="flex items-center gap-2.5">
                    <span class="grid place-items-center w-10 h-10 rounded-xl bg-white/10 text-white">
                        <x-icon name="stetho" class="w-5 h-5"/></span>
                    <span>
                        <span class="block font-bold text-white text-[0.95rem]">
                            {{ Setting::get('doctor_name') }}</span>
                        <span class="block text-xs text-white/60">{{ Setting::get('degrees') }}</span>
                    </span>
                </div>
                <p class="mt-4 text-sm leading-relaxed">{{ Setting::get('specialty') }}</p>
                @if($bmdc = Setting::get('bmdc'))
                    <p class="mt-2 text-xs text-white/50">{{ __('hero.bmdc') }} {{ $bmdc }}</p>
                @endif
            </div>

            <div>
                <p class="font-bold text-white text-sm mb-3">{{ __('ft.quickLinks') }}</p>
                <ul class="space-y-2 text-sm">
                    @foreach($links as [$href, $label])
                        <li><a href="{{ $href }}" class="hover:text-white transition">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <p class="font-bold text-white text-sm mb-3">{{ __('ft.contact') }}</p>
                <ul class="space-y-2 text-sm">
                    @if($h = Setting::get('hotline'))
                        <li><a href="tel:{{ $h }}" class="hover:text-white transition flex items-center gap-2">
                            <x-icon name="phone" class="w-4 h-4"/> {{ $h }}</a></li>
                    @endif
                    @if($w = Setting::get('whatsapp'))
                        <li><a href="https://wa.me/{{ intl_bd_phone($w) }}" target="_blank" rel="noopener"
                               class="hover:text-white transition flex items-center gap-2">
                            <x-icon name="phone" class="w-4 h-4"/> {{ $w }}</a></li>
                    @endif
                    @if($e = Setting::get('email'))
                        <li><a href="mailto:{{ $e }}" class="hover:text-white transition flex items-center gap-2">
                            <x-icon name="mail" class="w-4 h-4"/> {{ $e }}</a></li>
                    @endif
                </ul>

                @if($social)
                    <p class="font-bold text-white text-sm mt-6 mb-3">{{ __('ft.follow') }}</p>
                    <div class="flex gap-2">
                        @foreach($social as [$name, $href, $path])
                            <a href="{{ $href }}" target="_blank" rel="noopener" aria-label="{{ $name }}"
                               class="grid place-items-center w-9 h-9 rounded-lg bg-white/10
                                      hover:bg-white/20 text-white transition">
                                <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.75"
                                     stroke-linejoin="round">{!! $path !!}</svg>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-white/10">
            {{-- চিকিৎসা-বিষয়ক সাইটে দায়মুক্তি বিবৃতি থাকা জরুরি --}}
            <p class="text-[0.72rem] leading-relaxed text-white/45">{{ __('ft.disclaimer') }}</p>

            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-white/50">
                <p>© {{ bn_number(now()->year) }} {{ Setting::get('doctor_name') }}. {{ __('ft.rights') }}</p>
                <a href="{{ route('privacy') }}" class="hover:text-white/80">{{ __('ft.privacy') }}</a>
                <a href="{{ route('terms') }}" class="hover:text-white/80">{{ __('ft.terms') }}</a>
            </div>
        </div>
    </div>
</footer>
