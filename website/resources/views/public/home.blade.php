@extends('layouts.public')

@push('head')
    {{-- Schema.org — গুগলে ডাক্তারের নাম, ঠিকানা ও চেম্বারের সময়
         রিচ রেজাল্ট হিসেবে দেখানোর জন্য --}}
    @include('public.partials.schema')
@endpush

@section('content')

    @include('public.partials.hero')

    <section id="about" class="section">
        @include('public.partials.about')
    </section>

    @if($services->isNotEmpty())
        <section id="services" class="section section-tinted">
            @include('public.partials.services')
        </section>
    @endif

    @if($specials->isNotEmpty())
        <section id="special" class="section">
            @include('public.partials.special')
        </section>
    @endif

    @if($chamber)
        <section id="chamber" class="section section-tinted">
            @include('public.partials.chamber')
        </section>
    @endif

    <section id="booking" class="section">
        @include('public.partials.booking-cta')
    </section>

    {{-- ছবি না থাকলে সেকশনটি সম্পূর্ণ অদৃশ্য --}}
    @if($gallery->isNotEmpty())
        <section id="gallery" class="section section-tinted">
            @include('public.partials.gallery')
        </section>
    @endif

    {{-- প্রকৃত রোগীর মতামত না পাওয়া পর্যন্ত এই সেকশনও দেখাবে না।
         চিকিৎসা পেশায় বানানো রিভিউ লেখা হয়নি। --}}
    @if($testimonials->isNotEmpty())
        <section id="testimonials" class="section">
            @include('public.partials.testimonials')
        </section>
    @endif

    @if($faqs->isNotEmpty())
        <section id="faq" class="section section-tinted" data-faq>
            @include('public.partials.faq')
        </section>
    @endif

    <section id="contact" class="section">
        @include('public.partials.contact')
    </section>

@endsection
