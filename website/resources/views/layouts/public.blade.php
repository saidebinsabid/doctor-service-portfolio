@php
    use App\Models\Setting;
    $isEn = app()->getLocale() === 'en';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#1b3a6b">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', Setting::get('meta_title'))</title>
<meta name="description" content="@yield('description', Setting::get('meta_description'))">
<link rel="canonical" href="{{ url()->current() }}">

{{-- দুই ভাষার জন্য hreflang — গুগল যেন সঠিক ভার্সন দেখায় --}}
<link rel="alternate" hreflang="bn" href="{{ route('home', ['locale' => null]) }}">
<link rel="alternate" hreflang="en" href="{{ route('home', ['locale' => 'en']) }}">
<link rel="alternate" hreflang="x-default" href="{{ route('home', ['locale' => null]) }}">

@if($v = config('site.analytics.site_verification'))
<meta name="google-site-verification" content="{{ $v }}">
@endif

{{-- সোশ্যাল মিডিয়ায় শেয়ার করলে যা দেখাবে --}}
<meta property="og:type" content="website">
<meta property="og:locale" content="{{ $isEn ? 'en_US' : 'bn_BD' }}">
<meta property="og:site_name" content="{{ Setting::get('doctor_name') }}">
<meta property="og:title" content="@yield('title', Setting::get('meta_title'))">
<meta property="og:description" content="@yield('description', Setting::get('meta_description'))">
<meta property="og:url" content="{{ url()->current() }}">
@if($og = Setting::get('og_image'))
<meta property="og:image" content="{{ Storage::url($og) }}">
@endif
<meta name="twitter:card" content="summary_large_image">

{{-- বাংলা ফন্ট।
     ⚠️ প্রোডাকশনে সেলফ-হোস্টেড ও সাবসেট করলে বাংলাদেশ থেকে ~৪০০ms দ্রুত হবে --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])

@stack('head')
</head>

<body id="top">

<a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-3
   focus:start-3 focus:bg-white focus:px-4 focus:py-2 focus:rounded-lg focus:shadow-lg
   focus:text-brand-900 focus:font-semibold">
    {{ $isEn ? 'Skip to content' : 'মূল কনটেন্টে যান' }}
</a>

@include('public.partials.notice-bar')
@include('public.partials.header')
@include('public.partials.closed-banner')

<main id="main">
    @yield('content')
</main>

@include('public.partials.footer')
@include('public.partials.mobile-bar')

@stack('scripts')
</body>
</html>
