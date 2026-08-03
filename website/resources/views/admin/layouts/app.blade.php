@php
    use App\Models\Setting;
    $u = auth()->user();
    // নেভিগেশন: [route-name, লেবেল, svg-path]. active হাইলাইট route প্যাটার্ন দিয়ে।
    $content = [
        ['admin.services.index',       'services*',       'সেবাসমূহ'],
        ['admin.chambers.index',       'chambers*',       'চেম্বার'],
        ['admin.experiences.index',    'experiences*',    'কর্ম-অভিজ্ঞতা'],
        ['admin.qualifications.index', 'qualifications*', 'শিক্ষাগত যোগ্যতা'],
        ['admin.testimonials.index',   'testimonials*',   'রোগীদের মতামত'],
        ['admin.gallery.index',        'gallery*',        'গ্যালারি'],
        ['admin.faqs.index',           'faqs*',           'প্রশ্নোত্তর (FAQ)'],
        ['admin.notices.index',        'notices*',        'নোটিশ বার'],
    ];
@endphp
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'অ্যাডমিন') — {{ Setting::get('doctor_short', 'ডা. আবু সুফিয়ান') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen">

<div class="flex min-h-screen">

    {{-- ব্যাকড্রপ (মোবাইল) --}}
    <div id="sidebar-backdrop" class="hidden fixed inset-0 z-30 bg-brand-950/50 lg:hidden"></div>

    {{-- সাইডবার --}}
    <aside id="admin-sidebar"
           class="fixed lg:sticky top-0 z-40 h-screen w-64 shrink-0 -translate-x-full lg:translate-x-0
                  bg-brand-900 text-white flex flex-col transition-transform duration-200">
        <div class="flex items-center justify-between gap-2 px-4 h-16 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-bold">
                <span class="grid place-items-center w-9 h-9 rounded-lg bg-white/15">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4.8 2.3A.3.3 0 105.4 2a.3.3 0 00-.6.3zM8 15v-4a4 4 0 00-4-4V2M8 15a4 4 0 004 4 4 4 0 004-4v-3a5 5 0 015-5V2M8 15v3a4 4 0 108 0v-1"/></svg>
                </span>
                <span class="text-sm leading-tight">অ্যাডমিন প্যানেল</span>
            </a>
            <button id="sidebar-close" class="lg:hidden p-1.5 rounded-lg hover:bg-white/10" aria-label="বন্ধ">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5">
            <a href="{{ route('admin.dashboard') }}" class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
                ড্যাশবোর্ড
            </a>
            <a href="{{ route('admin.appointments.index') }}" class="side-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                অ্যাপয়েন্টমেন্ট
            </a>

            <div class="side-head">হোমপেজ কনটেন্ট</div>
            @foreach($content as [$route, $pattern, $label])
                <a href="{{ route($route) }}" class="side-link {{ request()->routeIs('admin.'.$pattern) ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                    {{ $label }}
                </a>
            @endforeach

            @if($u?->isAdmin())
                <div class="side-head">প্রশাসন</div>
                <a href="{{ route('admin.settings.index') }}" class="side-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                    সাইট সেটিংস
                </a>
            @endif
        </nav>

        <div class="border-t border-white/10 p-3">
            <a href="{{ route('admin.profile.edit') }}" class="side-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/></svg>
                <span class="min-w-0"><span class="block truncate">{{ $u?->name }}</span>
                <span class="block text-[.68rem] text-brand-300">{{ $u?->roleLabel() }}</span></span>
            </a>
            <form method="POST" action="{{ route('admin.logout') }}" class="mt-1">
                @csrf
                <button class="side-link w-full text-start"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg> লগআউট</button>
            </form>
        </div>
    </aside>

    {{-- মূল অংশ --}}
    <div class="flex-1 min-w-0 flex flex-col">
        <header class="sticky top-0 z-20 bg-white/95 backdrop-blur border-b border-brand-100 h-16 flex items-center gap-3 px-4 lg:px-6">
            <button id="sidebar-open" class="lg:hidden p-2 -ms-2 rounded-lg hover:bg-brand-50" aria-label="মেনু">
                <svg class="w-6 h-6 text-brand-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
            <h1 class="text-base lg:text-lg font-bold text-brand-900 truncate">@yield('heading', 'ড্যাশবোর্ড')</h1>
            <a href="{{ route('home') }}" target="_blank" class="ms-auto a-btn a-btn-light a-btn-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/></svg>
                <span class="hidden sm:inline">সাইট দেখুন</span>
            </a>
        </header>

        <main class="flex-1 p-4 lg:p-6">
            @if(session('success'))
                <div data-flash class="flash flash-success mb-4">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div data-flash class="flash flash-error mb-4">⚠️ {{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
