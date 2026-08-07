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
                'address_bn' => 'উত্তর বাড্ডা, ঢাকা · ১ নং বিল্ডিং, রুম ৭০৫',
                'address_en' => 'North Badda, Dhaka · Building 1, Room 705',
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

        /* ---------- দ্বিতীয় চেম্বার — APON Healthcare, বসুন্ধরা ----------
           ক্লায়েন্টের অনুরোধে ইবনে সিনার পরে। প্রতিদিন বিকাল ৫টা – রাত ৮টা।
           (অনলাইন বুকিং প্রাইমারি চেম্বার থেকে; এখানে ফোনে যোগাযোগ।) */
        $apon = Chamber::updateOrCreate(
            ['name_bn' => 'অ্যাপন হেলথকেয়ার লিমিটেড, বসুন্ধরা'],
            [
                'name_en'    => 'APON Healthcare Ltd., Bashundhara',
                'address_bn' => 'বসুন্ধরা, ঢাকা (এভারকেয়ার এর পাশে)',
                'address_en' => 'Bashundhara, Dhaka (beside Evercare)',
                'hotline'    => '09610987121',
                'map_query'  => 'APON Healthcare Bashundhara Dhaka',
                'is_active'  => true,
                'sort_order' => 20,
            ],
        );

        /* APON — প্রতি ৫ মিনিট পর পর স্লট (ক্লায়েন্টের অনুরোধে) */
        foreach (range(0, 6) as $dow) {
            Schedule::updateOrCreate(
                ['chamber_id' => $apon->id, 'day_of_week' => $dow],
                ['start_time' => '17:00', 'end_time' => '20:00', 'slot_minutes' => 5, 'max_serials' => 25, 'is_active' => true],
            );
        }
    }
}
