<?php

namespace App\Providers;

use App\Services\Notifications\CloudApiChannel;
use App\Services\Notifications\WaLinkChannel;
use App\Services\Notifications\WhatsAppChannel;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /* প্রোডাকশনে docroot (~/public_html) অ্যাপ ফোল্ডারের বাইরে — public path
           config('site.public_path') (APP_PUBLIC_PATH) থেকে সেট করা হয়, যাতে
           CLI (artisan) কমান্ডও সঠিক public path পায়। লোকালে খালি → অপরিবর্তিত। */
        if ($publicPath = config('site.public_path')) {
            $this->app->usePublicPath($publicPath);
        }

        /*
        | WhatsApp চ্যানেল বেছে নেওয়া।
        |
        | .env-এ WHATSAPP_CHANNEL=cloud_api লিখলেই Phase 2 চালু হয়ে যায় —
        | অ্যাপ্লিকেশনের আর কোনো ফাইল ছুঁতে হয় না।
        */
        $this->app->singleton(WhatsAppChannel::class, function ($app) {
            return config('site.whatsapp.channel') === 'cloud_api'
                ? $app->make(CloudApiChannel::class)
                : $app->make(WaLinkChannel::class);
        });
    }

    public function boot(): void
    {
        /* প্রোডাকশনে সব লিংক https:// দিয়ে তৈরি হবে।
           শেয়ার্ড হোস্টিং প্রায়ই প্রক্সির পেছনে থাকে, ফলে Laravel
           ভুল করে http ভাবতে পারে — তখন CSS/JS ব্লক হয়ে যায়। */
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->rateLimiters();
    }

    /**
     * অনুরোধের সীমা।
     *
     * বুকিং ফর্মে সীমা না দিলে একটি স্ক্রিপ্ট মিনিটেই ২৫টি সিরিয়াল
     * দখল করে ফেলতে পারত। লগইনে সীমা না দিলে পাসওয়ার্ড অনুমান করা যেত।
     */
    protected function rateLimiters(): void
    {
        RateLimiter::for('booking', function (Request $request) {
            return [
                /* একই IP থেকে মিনিটে ৫টির বেশি চেষ্টা নয় */
                Limit::perMinute(5)->by($request->ip()),
                /* একই মোবাইল নম্বর থেকে ঘণ্টায় ৩টির বেশি নয় */
                Limit::perHour(3)->by(normalize_bd_phone($request->input('patient_phone'))),
            ];
        });

        RateLimiter::for('login', function (Request $request) {
            /* ৫ বার ভুল পাসওয়ার্ড দিলে ওই ইমেইল+IP ১৫ মিনিট আটকে যায় */
            return Limit::perMinutes(15, 5)
                ->by(mb_strtolower((string) $request->input('email')) . '|' . $request->ip());
        });
    }
}
