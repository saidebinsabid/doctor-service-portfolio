<?php

namespace Database\Seeders;

use App\Models\Notice;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    public function run(): void
    {
        Notice::updateOrCreate(
            ['title_bn' => 'অনলাইনে সিরিয়াল নেওয়া যাচ্ছে'],
            [
                'title_en' => 'Online serial booking is now available',
                'body_bn'  => 'ঘরে বসেই তারিখ ও সময় বেছে নিয়ে সিরিয়াল নিন। '
                    . 'প্রতিদিন সর্বোচ্চ ২৫টি সিরিয়াল।',
                'body_en'  => 'Pick your date and time from home. Maximum 25 serials per day.',
                'severity' => 'info',
                'is_active' => true,
                'sort_order' => 10,
            ],
        );
    }
}
