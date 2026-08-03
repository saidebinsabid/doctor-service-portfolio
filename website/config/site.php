<?php

/*
|--------------------------------------------------------------------------
| সাইট কনফিগারেশন
|--------------------------------------------------------------------------
| এখানে শুধু কারিগরি নিয়মকানুন থাকে যা কোডের আচরণ ঠিক করে।
|
| ⚠️ ডাক্তারের নাম, ফোন, ঠিকানা, সেবা — এসব কনটেন্ট এখানে নেই।
|    সেগুলো ডাটাবেসের `settings` টেবিলে, অ্যাডমিন প্যানেল থেকে সম্পাদনাযোগ্য।
*/

return [

    /* প্রোডাকশন docroot (~/public_html) অ্যাপ ফোল্ডারের বাইরে থাকে।
       APP_PUBLIC_PATH সেট থাকলে public_path() ওখানে দেখায় (deploy ও CLI-র জন্য)।
       লোকালে খালি → স্বাভাবিক public/ ব্যবহার হয়। */
    'public_path' => env('APP_PUBLIC_PATH'),

    /* সমর্থিত ভাষা — বাংলা ডিফল্ট, ইংরেজি /en প্রিফিক্সে */
    'locales' => ['bn', 'en'],
    'default_locale' => 'bn',

    'booking' => [
        /* কত দিন পর্যন্ত আগাম বুকিং নেওয়া যাবে */
        'advance_days' => (int) env('BOOKING_ADVANCE_DAYS', 30),

        /* আজকের দিনে কোনো স্লট বন্ধ হবে তার কত মিনিট আগে।
           রোগী যেন চেম্বারে পৌঁছানোর সময় পান। */
        'cutoff_minutes' => (int) env('BOOKING_CUTOFF_MINUTES', 30),

        /* স্প্যাম ও ভুয়া বুকিং ঠেকাতে সীমা */
        'max_per_phone_per_day' => (int) env('BOOKING_MAX_PER_PHONE_PER_DAY', 2),
        'max_per_ip_per_hour'   => (int) env('BOOKING_MAX_PER_IP_PER_HOUR', 5),

        /* বুকিং কোডের উপসর্গ — ASF-260805-04 */
        'code_prefix' => 'ASF',
    ],

    'whatsapp' => [
        /*
         | চ্যানেল:
         |   wa_link   → Phase 1, ফ্রি। রোগীকে প্রস্তুত বার্তা দেখিয়ে Send চাপতে বলা হয়।
         |   cloud_api → Phase 2, পেইড। সার্ভার থেকেই স্বয়ংক্রিয় বার্তা যায়।
         |
         | ⚠️ cloud_api চালু করতে হলে আলাদা একটি ফোন নম্বর লাগবে —
         |    যে নম্বর সাধারণ WhatsApp অ্যাপে ব্যবহৃত হয় না।
         */
        'channel' => env('WHATSAPP_CHANNEL', 'wa_link'),

        'number'      => env('WHATSAPP_NUMBER', '8801327804433'),
        'phone_id'    => env('WHATSAPP_PHONE_ID'),
        'token'       => env('WHATSAPP_TOKEN'),
        'template_confirm'  => env('WHATSAPP_TEMPLATE_CONFIRM', 'appointment_confirmation'),
        'template_reminder' => env('WHATSAPP_TEMPLATE_REMINDER', 'appointment_reminder'),
    ],

    'sms' => [
        'enabled'   => (bool) env('SMS_ENABLED', false),
        'api_url'   => env('SMS_API_URL'),
        'api_key'   => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID'),
    ],

    'notify' => [
        'admin_email' => env('ADMIN_NOTIFY_EMAIL', 'appointment@drabusufian.com'),
    ],

    'analytics' => [
        'ga_id'             => env('GOOGLE_ANALYTICS_ID'),
        'site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    ],

    /* আপলোড করা ছবির সর্বোচ্চ প্রস্থ — এর চেয়ে বড় হলে ছোট করা হবে */
    'image' => [
        'max_width'  => 1600,
        'thumb_width' => 480,
        'quality'    => 82,
    ],
];
