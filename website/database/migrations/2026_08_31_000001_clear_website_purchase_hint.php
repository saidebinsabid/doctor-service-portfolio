<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * সেটিংস → যোগাযোগ → "ওয়েবসাইট" ঘরের নিচের সতর্কতা মুছে ফেলা
 * (ক্লায়েন্টের অনুরোধ, ৩১ আগস্ট ২০২৬)
 * ------------------------------------------------------------------
 * প্রজেক্টের শুরুতে drabusufian.com ডোমেইনটি কেনা হয়েছিল কি না অনিশ্চিত
 * ছিল বলে সেটিংয়ের hint-এ "⚠️ ডোমেইনটি এখনো কেনা হয়নি" লেখা ছিল।
 * এটি শুধু অ্যাডমিন প্যানেলে দেখাত (পাবলিক সাইটে নয়), তবু ক্লায়েন্টকে
 * বিব্রত করছিল। ডোমেইন এখন চূড়ান্ত — hint মুছে ফেলা হলো।
 *
 * শুধু hint বদলাচ্ছি, value বা label নয় — লিংক আগের মতোই "drabusufian.com"
 * থাকবে; পরে ক্লায়েন্ট চাইলে অ্যাডমিন থেকেই বদলাতে পারবেন।
 *
 * WHERE hint_bn LIKE: অ্যাডমিন যদি ইতিমধ্যে hint বদলে ফেলেন সেটা যেন
 * ওভাররাইট না হয় (rollback-নিরাপদ, একবারই বসে)।
 */
return new class extends Migration
{
    private const OLD_HINT = '⚠️ ডোমেইনটি এখনো কেনা হয়নি';

    public function up(): void
    {
        DB::table('settings')
            ->where('key', 'website')
            ->where('hint_bn', self::OLD_HINT)
            ->update(['hint_bn' => null, 'updated_at' => now()]);

        Setting::flush();
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('key', 'website')
            ->whereNull('hint_bn')
            ->update(['hint_bn' => self::OLD_HINT, 'updated_at' => now()]);

        Setting::flush();
    }
};
