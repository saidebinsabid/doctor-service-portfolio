<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * চেম্বারের SMS-এর জন্য বিশেষ short venue নাম (যেমন "IbnSina Badda, Bhaban-1")।
 * short_name-এর চেয়ে আলাদা কারণ: SMS-এ ভবন/রুম-জাতীয় ছোট তথ্য দরকার,
 * অথচ চেম্বার কার্ডে ওইটুকু বসালে ডিজাইন অসামঞ্জস্য হতো।
 * ফাঁকা থাকলে shortLabel()-এই ফিরে যায় (কোড fallback)।
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chambers', function (Blueprint $table) {
            if (! Schema::hasColumn('chambers', 'sms_venue')) {
                $table->string('sms_venue')->nullable();
            }
        });

        DB::table('chambers')->where('hotline', '09610009614')
            ->update(['sms_venue' => 'IbnSina Badda, Bhaban-1']);

        DB::table('chambers')->where('hotline', '09610987121')
            ->update(['sms_venue' => 'Apon Healthcare, Bashundhara']);
    }

    public function down(): void
    {
        Schema::table('chambers', function (Blueprint $table) {
            $table->dropColumn('sms_venue');
        });
    }
};
