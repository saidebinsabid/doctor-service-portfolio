@extends('layouts.public')

@section('title', __('ft.terms') . ' — ' . \App\Models\Setting::get('doctor_short'))

@section('content')
{{-- ⚠️ খসড়া — প্রকাশের আগে ডাক্তারের অনুমোদন নেওয়া দরকার। --}}
<section class="section">
    <div class="container-x max-w-3xl">
        <h1 class="section-title">{{ __('ft.terms') }}</h1>
        <p class="mt-2 text-sm text-slate-500">
            {{ app()->getLocale() === 'en' ? 'Last updated' : 'সর্বশেষ হালনাগাদ' }}:
            {{ fmt_date(now()) }}
        </p>

        <div class="mt-8 space-y-6 text-slate-600 leading-relaxed text-[0.95rem]">

            @if(app()->getLocale() === 'en')
                <div>
                    <h2 class="text-lg mb-2">Not medical advice</h2>
                    <p>The information on this website is general in nature and is not a substitute for
                       consulting a physician. In an emergency, please go to your nearest hospital.</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">About serials</h2>
                    <p>The time shown against a serial is approximate. Depending on how long each
                       patient needs, your turn may come slightly earlier or later. Please arrive
                       at least 15 minutes before your scheduled time.</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">Cancellation</h2>
                    <p>If you cannot attend, please inform the chamber in advance so that another
                       patient can use the slot. Repeated failure to attend booked serials may
                       result in online booking being restricted for that number.</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">Changes to the schedule</h2>
                    <p>Chamber hours may change without prior notice due to unavoidable
                       circumstances. Any such change will be announced on this website.</p>
                </div>
            @else
                <div>
                    <h2 class="text-lg mb-2">চিকিৎসা পরামর্শ নয়</h2>
                    <p>এই ওয়েবসাইটের তথ্য সাধারণ জ্ঞাতার্থে দেওয়া, এটি চিকিৎসকের সরাসরি
                       পরামর্শের বিকল্প নয়। জরুরি অবস্থায় নিকটস্থ হাসপাতালে যোগাযোগ করুন।</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">সিরিয়াল সম্পর্কে</h2>
                    <p>সিরিয়ালের বিপরীতে দেখানো সময়টি আনুমানিক। প্রতিটি রোগীর প্রয়োজন অনুযায়ী
                       সময় কম-বেশি লাগতে পারে, ফলে আপনার পালা কিছুটা আগে বা পরে আসতে পারে।
                       নির্ধারিত সময়ের অন্তত ১৫ মিনিট আগে উপস্থিত থাকার অনুরোধ করা হচ্ছে।</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">সিরিয়াল বাতিল</h2>
                    <p>আসতে না পারলে আগেভাগে চেম্বারে জানানোর অনুরোধ করা হচ্ছে, যাতে অন্য একজন
                       রোগী সেই সিরিয়ালটি পেতে পারেন। বারবার সিরিয়াল নিয়ে অনুপস্থিত থাকলে
                       ওই নম্বর থেকে অনলাইন বুকিং সীমিত করা হতে পারে।</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">সময়সূচির পরিবর্তন</h2>
                    <p>অনিবার্য কারণে চেম্বারের সময়সূচি পূর্বঘোষণা ছাড়াই পরিবর্তিত হতে পারে।
                       এ ধরনের পরিবর্তন এই ওয়েবসাইটে জানিয়ে দেওয়া হবে।</p>
                </div>
            @endif

        </div>
    </div>
</section>
@endsection
