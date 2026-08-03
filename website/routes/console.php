<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| নির্ধারিত কাজ (Scheduled Tasks)
|--------------------------------------------------------------------------
| হোস্টিংয়ের cron-এ শুধু একটি এন্ট্রি লাগবে (প্রতি মিনিটে):
|
|   * * * * * cd /home/USER/drabusufian.com && php artisan schedule:run >> /dev/null 2>&1
|
| বিস্তারিত docs/DEPLOYMENT.md-এ।
*/

/* প্রতিদিন রাত ২টায় ডাটাবেস ব্যাকআপ */
Schedule::command('backup:database')
    ->dailyAt('02:00')
    ->timezone('Asia/Dhaka');

/* যেসব অ্যাপয়েন্টমেন্টের দিন পেরিয়ে গেছে অথচ স্ট্যাটাস বদলায়নি,
   সেগুলো স্বয়ংক্রিয়ভাবে "অনুপস্থিত" চিহ্নিত করা */
Schedule::command('appointments:close-past')
    ->dailyAt('23:30')
    ->timezone('Asia/Dhaka');
