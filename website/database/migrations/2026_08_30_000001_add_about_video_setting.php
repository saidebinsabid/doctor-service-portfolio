<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * "ডাক্তার সম্পর্কে" সেকশনে পরিচিতি ভিডিও (ক্লায়েন্টের অনুরোধ, ৩০ আগস্ট ২০২৬)
 * ------------------------------------------------------------------
 * ভিডিওর লিংক কোডে হার্ডকোড না করে সেটিং হিসেবে রাখা হলো, যাতে ক্লায়েন্ট
 * অ্যাডমিন → সেটিংস → ডাক্তারের পরিচয় থেকে যেকোনো সময় বদলাতে বা মুছে
 * ফেলতে পারেন। ঘরটি খালি করলেই ভিডিও ব্লকটি ওয়েবসাইট থেকে হাওয়া হয়ে যায়।
 *
 * ভাষাভেদে আলাদা মান লাগে না (একই ভিডিও), তাই শুধু value_bn — Setting::get()
 * ইংরেজি খালি দেখলে নিজেই বাংলাটায় নেমে আসে।
 *
 * updateOrInsert: আগে থেকে সারিটি থাকলে (বা অ্যাডমিন লিংক বদলে ফেললে)
 * বারবার চালালেও ক্ষতি নেই।
 */
return new class extends Migration
{
    private const URL = 'https://youtu.be/Bxb8w9A6xQ8';

    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'about_video'],
            [
                'group' => 'doctor', 'type' => 'url', 'sort_order' => 96,
                'label_bn' => 'পরিচিতি ভিডিও (ইউটিউব লিংক)',
                'label_en' => 'About-section video (YouTube link)',
                'hint_bn' => '"ডাক্তার সম্পর্কে" অংশে বিবরণের নিচে ভিডিওটি দেখানো হয়। '
                    . 'ইউটিউবের যেকোনো লিংক চলবে — youtu.be/... বা youtube.com/watch?v=...। '
                    . 'ঘরটি খালি রাখলে ভিডিও দেখানো হবে না।',
                'value_bn' => self::URL,
                'value_en' => null,
                'created_at' => now(), 'updated_at' => now(),
            ],
        );

        /* deploy.sh cache:clear চালায় না — Setting-এর rememberForever ক্যাশ
           এখানেই মুছতে হয়, নইলে পুরনো (খালি) মান ক্যাশে আটকে থাকত */
        Setting::flush();
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'about_video')->delete();
        Setting::flush();
    }
};
