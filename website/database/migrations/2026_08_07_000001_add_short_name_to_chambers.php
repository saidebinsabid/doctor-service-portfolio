<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * চেম্বারের সংক্ষিপ্ত নাম (মোবাইল বার / ছোট বাটনে ব্যবহারের জন্য)।
 *
 * পুরো নাম দীর্ঘ হওয়ায় ছোট জায়গায় বসে না — তাই একটি ছোট নাম রাখা হলো।
 * খালি থাকলে কোডে পুরো নামেই ফিরে যায় (Chamber::shortLabel())।
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chambers', function (Blueprint $table) {
            if (! Schema::hasColumn('chambers', 'short_name_bn')) {
                $table->string('short_name_bn')->nullable();
            }
            if (! Schema::hasColumn('chambers', 'short_name_en')) {
                $table->string('short_name_en')->nullable();
            }
        });

        /* বিদ্যমান দুই চেম্বারের সংক্ষিপ্ত নাম বসিয়ে দেওয়া (একবারই) */
        DB::table('chambers')
            ->where('name_bn', 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, বাড্ডা')
            ->update(['short_name_bn' => 'ইবনে সিনা, বাড্ডা', 'short_name_en' => 'Ibn Sina, Badda']);

        DB::table('chambers')
            ->where('name_bn', 'অ্যাপন হেলথকেয়ার লিমিটেড, বসুন্ধরা')
            ->update(['short_name_bn' => 'আপন হেলথকেয়ার, বসুন্ধরা', 'short_name_en' => 'APON Healthcare, Bashundhara']);
    }

    public function down(): void
    {
        Schema::table('chambers', function (Blueprint $table) {
            $table->dropColumn(['short_name_bn', 'short_name_en']);
        });
    }
};
