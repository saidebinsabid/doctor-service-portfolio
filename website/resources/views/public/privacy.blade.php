@extends('layouts.public')

@section('title', __('ft.privacy') . ' — ' . \App\Models\Setting::get('doctor_short'))

@section('content')
{{--
| গোপনীয়তা নীতি।
|
| ⚠️ দুটি কারণে এই পাতাটি বাদ দেওয়া যায় না:
|    ১. রোগীর স্বাস্থ্য-সংক্রান্ত তথ্য সংগ্রহ করা হচ্ছে
|    ২. WhatsApp Cloud API (Phase 2) নিতে হলে Meta এই পাতা দেখতে চায়
|
| ⚠️ এটি একটি খসড়া। প্রকাশের আগে ডাক্তারের অনুমোদন নেওয়া দরকার।
--}}
<section class="section">
    <div class="container-x max-w-3xl">
        <h1 class="section-title">{{ __('ft.privacy') }}</h1>
        <p class="mt-2 text-sm text-slate-500">
            {{ app()->getLocale() === 'en' ? 'Last updated' : 'সর্বশেষ হালনাগাদ' }}:
            {{ fmt_date(now()) }}
        </p>

        <div class="mt-8 space-y-6 text-slate-600 leading-relaxed text-[0.95rem]">

            @if(app()->getLocale() === 'en')
                <div>
                    <h2 class="text-lg mb-2">Information we collect</h2>
                    <p>When you book a serial we collect the child's name and age, the guardian's name,
                       a mobile number, and optionally an address and a short description of the problem.
                       We also record the IP address of the request to prevent spam bookings.</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">How it is used</h2>
                    <p>This information is used only to manage your appointment — to reserve your serial,
                       to contact you about it, and to prepare the doctor's daily patient list.
                       It is never sold, rented, or shared for marketing.</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">WhatsApp and SMS</h2>
                    <p>If you tap the WhatsApp confirmation button, a message containing your booking
                       details is prepared and you choose whether to send it. Messages sent through
                       WhatsApp are subject to WhatsApp's own privacy policy.</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">Who can see it</h2>
                    <p>Only the doctor and authorised chamber staff can view appointment records
                       through a password-protected admin panel.</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">Your choices</h2>
                    <p>You may ask us to cancel your appointment or remove your details at any time
                       by contacting the chamber on the numbers listed on this website.</p>
                </div>
            @else
                <div>
                    <h2 class="text-lg mb-2">কী তথ্য নেওয়া হয়</h2>
                    <p>সিরিয়াল নেওয়ার সময় শিশুর নাম ও বয়স, অভিভাবকের নাম, মোবাইল নম্বর এবং
                       ঐচ্ছিকভাবে ঠিকানা ও সমস্যার সংক্ষিপ্ত বিবরণ সংগ্রহ করা হয়। ভুয়া বুকিং
                       ঠেকাতে অনুরোধের আইপি ঠিকানাও রাখা হয়।</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">কীভাবে ব্যবহার করা হয়</h2>
                    <p>এই তথ্য শুধুমাত্র আপনার অ্যাপয়েন্টমেন্ট পরিচালনার জন্য ব্যবহৃত হয় —
                       সিরিয়াল সংরক্ষণ, আপনার সাথে যোগাযোগ এবং ডাক্তারের দৈনিক রোগীর তালিকা
                       প্রস্তুত করা। কোনো অবস্থাতেই এই তথ্য বিক্রি বা বিজ্ঞাপনের কাজে
                       ব্যবহার করা হয় না।</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">হোয়াটসঅ্যাপ ও এসএমএস</h2>
                    <p>হোয়াটসঅ্যাপ কনফার্মেশন বাটনে চাপ দিলে আপনার সিরিয়ালের তথ্যসহ একটি বার্তা
                       তৈরি হয়; সেটি পাঠাবেন কি না তা আপনার সিদ্ধান্ত। হোয়াটসঅ্যাপের মাধ্যমে
                       পাঠানো বার্তা হোয়াটসঅ্যাপের নিজস্ব গোপনীয়তা নীতির আওতাভুক্ত।</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">কে দেখতে পারেন</h2>
                    <p>পাসওয়ার্ড সুরক্ষিত অ্যাডমিন প্যানেলের মাধ্যমে শুধু ডাক্তার ও চেম্বারের
                       অনুমোদিত কর্মী অ্যাপয়েন্টমেন্টের তথ্য দেখতে পারেন।</p>
                </div>
                <div>
                    <h2 class="text-lg mb-2">আপনার অধিকার</h2>
                    <p>যেকোনো সময় ওয়েবসাইটে দেওয়া নম্বরে যোগাযোগ করে আপনি সিরিয়াল বাতিল
                       করতে বা আপনার তথ্য মুছে ফেলার অনুরোধ করতে পারেন।</p>
                </div>
            @endif

        </div>
    </div>
</section>
@endsection
