<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * ইবনে সিনার SMS venue-তে রুম নম্বর যোগ (ক্লায়েন্টের অনুরোধ, ২২ আগস্ট ২০২৬)
 * ------------------------------------------------------------------
 * রোগীকে পাঠানো SMS-এ চেম্বারের নাম থাকে কিন্তু রুম নম্বর ছিল না।
 * ইবনে সিনার ক্ষেত্রে রুম ৭০৫ জরুরি, তাই chambers.sms_venue-তে বসানো হলো —
 * এতে বুকিং SMS-এ "Ibn Sina, Badda, Room 705" যাবে।
 *
 * whereNull: অ্যাডমিন প্যানেল থেকে আগে থেকে sms_venue সেট করা থাকলে সেটি
 * ওভাররাইড করা হয় না (রোলব্যাক-নিরাপদ, একবারই বসে)।
 */
return new class extends Migration
{
    private const VENUE = 'Ibn Sina, Badda, Room 705';

    public function up(): void
    {
        DB::table('chambers')
            ->where('name_en', 'like', '%Ibn Sina%')
            ->whereNull('sms_venue')
            ->update(['sms_venue' => self::VENUE]);
    }

    public function down(): void
    {
        DB::table('chambers')
            ->where('sms_venue', self::VENUE)
            ->update(['sms_venue' => null]);
    }
};
