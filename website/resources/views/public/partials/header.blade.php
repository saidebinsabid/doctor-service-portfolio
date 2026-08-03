@php
    use App\Models\Setting;

    $nav = [
        ['#about',    __('nav.about')],
        ['#services', __('nav.services')],
        ['#chamber',  __('nav.chamber')],
        ['#faq',      __('nav.faq')],
        ['#contact',  __('nav.contact')],
    ];

    /* ভাষা টগল — একই পাতার অন্য ভাষার ঠিকানা */
    $isEn = app()->getLocale() === 'en';
    $altUrl = $isEn
        ? preg_replace('~^' . preg_quote(url('/en'), '~') . '~', url('/'), url()->current())
        : url('/en' . (request()->path() === '/' ? '' : '/' . request()->path()));
@endphp

<header id="site-header"
        class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-brand-100 transition-shadow">
    <div class="container-x">
        <div class="flex items-center justify-between gap-4 h-[4.5rem]">

            <a href="{{ route('home') }}" class="flex items-center gap-2.5 min-w-0">
                <span class="grid place-items-center w-10 h-10 rounded-xl bg-brand-900 text-white shrink-0">
                    <x-icon name="stetho" class="w-5 h-5"/>
                </span>
                <span class="min-w-0">
                    <span class="block font-bold text-brand-900 leading-tight truncate text-[0.95rem]">
                        {{ Setting::get('doctor_short') }}</span>
                    <span class="block text-[0.7rem] text-slate-500 leading-tight truncate">
                        {{ Setting::get('degrees') }}</span>
                </span>
            </a>

            <nav class="hidden lg:flex items-center gap-1" aria-label="{{ __('nav.menu') }}">
                @foreach($nav as [$href, $label])
                    <a href="{{ $href }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600
                              hover:text-brand-900 hover:bg-brand-50 transition">{{ $label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ $altUrl }}" rel="alternate"
                   hreflang="{{ $isEn ? 'bn' : 'en' }}"
                   class="px-2.5 py-1.5 rounded-lg border border-brand-100 text-xs font-bold
                          text-brand-900 hover:bg-brand-50 transition">
                    {{ $isEn ? 'বাং' : 'EN' }}
                </a>

                <a href="{{ route('booking.create') }}"
                   class="btn btn-primary hidden sm:inline-flex !px-4 !py-2.5 !text-sm">
                    <x-icon name="clock" class="w-4 h-4"/> {{ __('nav.book') }}
                </a>

                {{-- ডক্টর/অ্যাডমিন লগইন — লগইন করা থাকলে ড্যাশবোর্ড --}}
                <a href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl border border-brand-100
                          text-sm font-semibold text-brand-900 hover:bg-brand-50 transition"
                   title="{{ auth()->check() ? 'ড্যাশবোর্ড' : 'ডক্টর লগইন' }}">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                    {{ auth()->check() ? 'ড্যাশবোর্ড' : 'লগইন' }}
                </a>

                <button type="button" id="menu-toggle" class="lg:hidden p-2 rounded-lg hover:bg-brand-50"
                        aria-label="{{ __('nav.menu') }}" aria-expanded="false" aria-controls="mobile-nav">
                    <svg class="w-6 h-6 text-brand-900" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M3 6h18M3 12h18M3 18h18"/></svg>
                </button>
            </div>
        </div>

        <nav id="mobile-nav" class="lg:hidden hidden border-t border-brand-100 py-2">
            @foreach($nav as [$href, $label])
                <a href="{{ $href }}"
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700
                          hover:bg-brand-50">{{ $label }}</a>
            @endforeach
            <a href="{{ route('booking.status') }}"
               class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-brand-50">
                {{ __('nav.status') }}</a>
        </nav>
    </div>
</header>
