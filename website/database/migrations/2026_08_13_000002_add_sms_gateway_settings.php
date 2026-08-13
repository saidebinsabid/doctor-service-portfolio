<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

/**
 * রোগীর ফোনে অটো-SMS চালু করার জন্য দুটি সেটিং —
 * অ্যাডমিন প্যানেল → Settings → বুকিং সেকশনে দেখাবে ও সম্পাদনযোগ্য।
 *
 * খালি থাকলে ফিচার বন্ধ; কোনো কোড-চেঞ্জ ছাড়াই ক্লায়েন্ট নিজে key
 * বসিয়ে ফিচারটি চালু বা বন্ধ করতে পারবেন।
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            [
                'key' => 'sms_api_key', 'group' => 'booking', 'type' => 'text', 'sort_order' => 70,
                'label_bn' => 'রোগীর ফোনে অটো SMS — sms.net.bd API key',
                'label_en' => 'Patient auto-SMS — sms.net.bd API key',
                'hint_bn' => 'sms.net.bd → API পেজ থেকে "Generate Key" চেপে key নিন এবং এখানে বসান। খালি থাকলে ফিচারটি বন্ধ, বুকিং স্বাভাবিক চলবে।',
                'value_bn' => null, 'value_en' => null,
            ],
            [
                'key' => 'sms_sender_id', 'group' => 'booking', 'type' => 'text', 'sort_order' => 80,
                'label_bn' => 'SMS Sender ID (মাস্কিং — ঐচ্ছিক)',
                'label_en' => 'SMS Sender ID (mask — optional)',
                'hint_bn' => 'sms.net.bd থেকে অনুমোদিত আপনার Sender ID (যেমন: DrAbuSufian)। না থাকলে খালি রাখুন — নন-মাস্ক নম্বর থেকে যাবে।',
                'value_bn' => null, 'value_en' => null,
            ],
        ];

        foreach ($rows as $row) {
            DB::table('settings')->updateOrInsert(
                ['key' => $row['key']],
                $row + ['created_at' => now(), 'updated_at' => now()],
            );
        }

        Setting::flush();
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['sms_api_key', 'sms_sender_id'])->delete();
        Setting::flush();
    }
};
