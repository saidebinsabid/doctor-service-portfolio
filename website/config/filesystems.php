<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ডিফল্ট ফাইলসিস্টেম ডিস্ক
    |--------------------------------------------------------------------------
    */
    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app/private'),
            'serve'  => true,
            'throw'  => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',

            /*
            | প্রোডাকশনে (cPanel + LiteSpeed) `public/storage` symlink সার্ভ হয় না —
            | আপলোড করা ছবি আপলোড হলেও ব্রাউজারে 404 দেয়। তাই public disk সরাসরি
            | docroot-এর ভেতরে লেখে: APP_PUBLIC_PATH সেট থাকলে ~/public_html/storage,
            | নইলে (লোকালে) স্বাভাবিক storage/app/public — যেখানে storage:link কাজ করে।
            */
            'root' => env('APP_PUBLIC_PATH')
                ? env('APP_PUBLIC_PATH') . '/storage'
                : storage_path('app/public'),

            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw'      => false,
            'report'     => false,
        ],

        's3' => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION'),
            'bucket'                  => env('AWS_BUCKET'),
            'url'                     => env('AWS_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw'                   => false,
            'report'                  => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | সিমলিংক (লোকাল ডেভেলপমেন্টে `php artisan storage:link`)
    |--------------------------------------------------------------------------
    */
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
