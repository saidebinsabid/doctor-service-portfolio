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
                {{-- ক্লায়েন্টের অনুরোধে হেডারে নাম/ডিগ্রির বদলে বড় লাল "শিশু ডাক্তার"
                     (পুরো নাম ও ডিগ্রি নিচে হিরোতেই আছে) --}}
                <span class="font-extrabold text-red-600 leading-tight truncate text-lg sm:text-xl">
                    {{ __('common.childDoctor') }}</span>
            </a>

            <nav class="hidden lg:flex items-center gap-1" aria-label="{{ __('nav.menu') }}">
                @foreach($nav as [$href, $label])
                    <a href="{{ $href }}" data-spy-link
                       class="nav-link px-3 py-2 rounded-lg text-sm font-medium transition">{{ $label }}</a>
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

    </div>
</header>

{{-- মোবাইল ডান-সাইড ড্রয়ার মেনু।
     ইচ্ছে করেই <header>-এর বাইরে — হেডারের backdrop-blur একটি stacking context
     তৈরি করে, ভেতরে রাখলে ড্রয়ার অন্য fixed উপাদানের নিচে চাপা পড়ত।
     ডান দিক থেকে soft-slide, full-height, প্রয়োজনমতো প্রস্থ। নিয়ন্ত্রণ: app.js --}}
<div id="mobile-nav-overlay"
     class="lg:hidden fixed inset-0 z-50 bg-brand-900/40 backdrop-blur-sm
            opacity-0 pointer-events-none transition-opacity duration-300"></div>

<aside id="mobile-nav" aria-hidden="true" aria-label="{{ __('nav.menu') }}"
       class="lg:hidden fixed top-0 right-0 z-50 h-dvh w-4/5 max-w-[18rem] bg-white shadow-2xl
              flex flex-col translate-x-full transition-transform duration-300 ease-out">

    {{-- ড্রয়ার শিরোনাম + বন্ধ বাটন --}}
    <div class="flex items-center justify-between px-4 h-[4.5rem] border-b border-brand-100 shrink-0">
        <span class="font-extrabold text-red-600 text-lg">{{ __('common.childDoctor') }}</span>
        <button type="button" id="menu-close" class="p-2 -mr-2 rounded-lg hover:bg-brand-50"
                aria-label="{{ __('common.close') }}">
            <svg class="w-6 h-6 text-brand-900" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </div>

    {{-- লিংকসমূহ (স্ক্রলযোগ্য) --}}
    <nav class="flex-1 overflow-y-auto px-3 py-3">
        @foreach($nav as [$href, $label])
            <a href="{{ $href }}"
               class="block px-3 py-3 rounded-lg text-[0.95rem] font-medium text-slate-700
                      hover:bg-brand-50 transition">{{ $label }}</a>
        @endforeach
        <a href="{{ route('booking.status') }}"
           class="block px-3 py-3 rounded-lg text-[0.95rem] font-medium text-slate-700
                  hover:bg-brand-50 transition">{{ __('nav.status') }}</a>
        <a href="{{ route('booking.create') }}"
           class="block px-3 py-3 rounded-lg text-[0.95rem] font-semibold text-sky2-700
                  hover:bg-sky2-50 transition">{{ __('nav.book') }}</a>
    </nav>

    {{-- নিচে অ্যাডমিন/ডক্টর লগইন বাটন --}}
    <div class="px-3 py-4 border-t border-brand-100 shrink-0">
        <a href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}"
           class="btn btn-outline w-full !py-3 justify-center">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
            {{ auth()->check() ? __('nav.dashboard') : __('nav.login') }}
        </a>
    </div>
</aside>
