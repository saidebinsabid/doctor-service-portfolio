<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * মিটিং সিদ্ধান্ত অনুযায়ী সময়সূচি হালনাগাদ (২২ আগস্ট ২০২৬)
 * ------------------------------------------------------------------
 *   • সকালের সময়সূচির শুরু ১০:৩০ → ১০:০০
 *   • প্রতি স্লট ৫ মিনিট অন্তর
 *   • প্রতিদিন সর্বোচ্চ ৪০ সিরিয়াল
 *
 * কেন সিডার নয়, মাইগ্রেশন:
 *   সিডার (ChamberSeeder) শুধু নতুন সেটআপে চলে; লাইভ ডাটাবেসে re-seed
 *   করলে settings টেবিলের গোপন মান (যেমন SMS API key) মুছে যায়।
 *   এই ডেটা-মাইগ্রেশন ডিপ্লয়ে `php artisan migrate`-এ একবার চলে,
 *   শুধু schedules টেবিল হালনাগাদ করে — বাকি সব ডেটা অক্ষত থাকে।
 *
 * ক্রস-ডাটাবেস: start_time TIME কলামের তুলনা '12:00:00'-এর সাথে করা হয়েছে,
 * যা SQLite (টেক্সট) ও MySQL (TIME) — দুটোতেই সঠিকভাবে সকাল/সন্ধ্যা আলাদা করে।
 */
return new class extends Migration
{
    public function up(): void
    {
        // ১) সকালের সময়সূচি (দুপুরের আগে শুরু) → সকাল ১০:০০
        DB::table('schedules')
            ->where('start_time', '<', '12:00:00')
            ->update(['start_time' => '10:00']);

        // ২) সব সময়সূচি → ৫ মিনিট স্লট, সর্বোচ্চ ৪০ সিরিয়াল
        DB::table('schedules')->update([
            'slot_minutes' => 5,
            'max_serials'  => 40,
        ]);
    }

    public function down(): void
    {
        /* পূর্বাবস্থা আনুমানিকভাবে ফেরানো (আগের সিডার মান):
           সকাল শুরু ১০:৩০, স্লট ৮ মিনিট, সর্বোচ্চ ২৫ সিরিয়াল।
           (শুক্র/দ্বিতীয় চেম্বারের মূল স্লট ৭/৫ ছিল — রোলব্যাক আনুমানিক।) */
        DB::table('schedules')
            ->where('start_time', '<', '12:00:00')
            ->update(['start_time' => '10:30']);

        DB::table('schedules')->update([
            'slot_minutes' => 8,
            'max_serials'  => 25,
        ]);
    }
};
