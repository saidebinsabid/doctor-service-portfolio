<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

/**
 * "ডাক্তার সম্পর্কে" সেকশনের ছবি সেটিং তৈরি + লাইভে ছবিটির পাথ বসানো।
 *
 * ছবিটি deploy-এর বাইরে সরাসরি public_html/storage/site/about-poster.jpg-এ
 * আপলোড করা হয়েছে (Setting-এর 'image' টাইপের ফাইল আপলোড ফ্লো অনুযায়ী পাথ)।
 * এই migration শুধু DB রো-টা তৈরি করে যাতে অ্যাডমিন প্যানেলেও এটি সম্পাদনযোগ্য হয়।
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'about_photo'],
            [
                'group' => 'doctor', 'type' => 'image', 'sort_order' => 95,
                'label_bn' => 'পরিচিতি বিভাগের ছবি (ফ্লায়ার/পোস্টার)',
                'label_en' => 'About-section image (flyer/poster)',
                'hint_bn' => '"ডাক্তার সম্পর্কে" অংশের বিবরণের নিচে দেখানো হয় — পোর্ট্রেট আকৃতি ভালো মানায়।',
                'value_bn' => 'site/about-poster.jpg',
                'value_en' => null,
                'created_at' => now(), 'updated_at' => now(),
            ],
        );

        /* deploy.sh cache:clear চালায় না (প্রথম রানে টেবিল না থাকার ঝুঁকি এড়াতে) —
           তাই Setting-এর নিজস্ব rememberForever ক্যাশ এখানেই মুছে দেওয়া, নইলে
           পুরনো (খালি) মান ক্যাশে আটকে থাকবে। */
        Setting::flush();
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'about_photo')->delete();
        Setting::flush();
    }
};
