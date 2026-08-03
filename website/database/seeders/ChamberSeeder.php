<?php

namespace Database\Seeders;

use App\Models\Chamber;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ChamberSeeder extends Seeder
{
    public function run(): void
    {
        /* ✅ ভিজিটিং কার্ড থেকে */
        $chamber = Chamber::updateOrCreate(
            ['name_bn' => 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, বাড্ডা'],
            [
                'name_en'    => 'Ibn Sina Diagnostic & Consultation Center, Badda',
                'address_bn' => 'বাড়ি-চ-৭২/১, প্রগতি সরণি, উত্তর বাড্ডা, ঢাকা-১২১২',
                'address_en' => 'House-Cha-72/1, Progoti Sharani, North Badda, Dhaka-1212',
                'hotline'    => '09610009614',
                'map_query'  => 'Ibn Sina Diagnostic and Consultation Center, '
                    . 'Progoti Sharani, North Badda, Dhaka 1212',
                'is_active'  => true,
                'sort_order' => 10,
            ],
        );

        /*
        |----------------------------------------------------------------------
        | সময়সূচি — ✅ ভিজিটিং কার্ড অনুযায়ী (ক্লায়েন্টের সিদ্ধান্ত, ০১ আগস্ট ২০২৬)
        |----------------------------------------------------------------------
        |   শনিবার – বৃহস্পতিবার : ১০:৩০ AM – ২:০০ PM
        |   শুক্রবার             : ৫:০০ PM – ৮:০০ PM
        |
        | প্রতিদিন সর্বোচ্চ ২৫টি সিরিয়াল (✅ প্রচারপত্র)।
        |
        | স্লটের দৈর্ঘ্য সেখান থেকেই হিসাব করা:
        |   শনি–বৃহস্পতি : ২১০ মিনিট ÷ ২৫ ≈ ৮ মিনিট
        |   শুক্রবার     : ১৮০ মিনিট ÷ ২৫ ≈ ৭ মিনিট
        |
        | ⚠️ প্রতি রোগীর জন্য ৭–৮ মিনিট বেশ কম। ডাক্তার চাইলে অ্যাডমিন
        |    প্যানেল থেকে দিনভিত্তিক সিরিয়াল সংখ্যা কমাতে পারবেন —
        |    কোডে কিছু বদলাতে হবে না।
        */
        $schedules = [
            /* day_of_week: 0=রবি … 6=শনি */
            ['day_of_week' => 6, 'start_time' => '10:30', 'end_time' => '14:00', 'slot_minutes' => 8, 'max_serials' => 25], // শনি
            ['day_of_week' => 0, 'start_time' => '10:30', 'end_time' => '14:00', 'slot_minutes' => 8, 'max_serials' => 25], // রবি
            ['day_of_week' => 1, 'start_time' => '10:30', 'end_time' => '14:00', 'slot_minutes' => 8, 'max_serials' => 25], // সোম
            ['day_of_week' => 2, 'start_time' => '10:30', 'end_time' => '14:00', 'slot_minutes' => 8, 'max_serials' => 25], // মঙ্গল
            ['day_of_week' => 3, 'start_time' => '10:30', 'end_time' => '14:00', 'slot_minutes' => 8, 'max_serials' => 25], // বুধ
            ['day_of_week' => 4, 'start_time' => '10:30', 'end_time' => '14:00', 'slot_minutes' => 8, 'max_serials' => 25], // বৃহস্পতি
            ['day_of_week' => 5, 'start_time' => '17:00', 'end_time' => '20:00', 'slot_minutes' => 7, 'max_serials' => 25], // শুক্র
        ];

        foreach ($schedules as $row) {
            Schedule::updateOrCreate(
                ['chamber_id' => $chamber->id, 'day_of_week' => $row['day_of_week']],
                $row + ['is_active' => true],
            );
        }
    }
}
