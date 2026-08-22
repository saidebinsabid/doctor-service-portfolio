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
                'short_name_bn' => 'ইবনে সিনা, বাড্ডা',
                'short_name_en' => 'Ibn Sina, Badda',
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
        | সময়সূচি — মিটিং আপডেট অনুযায়ী (ক্লায়েন্টের সিদ্ধান্ত, ২২ আগস্ট ২০২৬)
        |----------------------------------------------------------------------
        |   শনিবার – বৃহস্পতিবার : ১০:০০ AM – ২:০০ PM
        |   শুক্রবার             : ৫:০০ PM – ৮:০০ PM
        |
        | ✅ সিরিয়াল সময় সকাল ১০:০০ থেকে শুরু, ৫ মিনিট অন্তর
        |    (১ম ১০:০০, ২য় ১০:০৫, ৩য় ১০:১০ …)
        | ✅ প্রতিদিন সর্বোচ্চ ৪০টি সিরিয়াল
        |
        | রোগী সময় বাছে না — সাবমিট করলে পরের খালি সিরিয়াল অটোমেটিক বসে।
        | সবই অ্যাডমিন প্যানেল থেকে বদলানো যায়।
        */
        $schedules = [
            /* day_of_week: 0=রবি … 6=শনি */
            ['day_of_week' => 6, 'start_time' => '10:00', 'end_time' => '14:00', 'slot_minutes' => 5, 'max_serials' => 40], // শনি
            ['day_of_week' => 0, 'start_time' => '10:00', 'end_time' => '14:00', 'slot_minutes' => 5, 'max_serials' => 40], // রবি
            ['day_of_week' => 1, 'start_time' => '10:00', 'end_time' => '14:00', 'slot_minutes' => 5, 'max_serials' => 40], // সোম
            ['day_of_week' => 2, 'start_time' => '10:00', 'end_time' => '14:00', 'slot_minutes' => 5, 'max_serials' => 40], // মঙ্গল
            ['day_of_week' => 3, 'start_time' => '10:00', 'end_time' => '14:00', 'slot_minutes' => 5, 'max_serials' => 40], // বুধ
            ['day_of_week' => 4, 'start_time' => '10:00', 'end_time' => '14:00', 'slot_minutes' => 5, 'max_serials' => 40], // বৃহস্পতি
            ['day_of_week' => 5, 'start_time' => '17:00', 'end_time' => '20:00', 'slot_minutes' => 5, 'max_serials' => 40], // শুক্র
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
                'short_name_bn' => 'আপন হেলথকেয়ার, বসুন্ধরা',
                'short_name_en' => 'APON Healthcare, Bashundhara',
                'address_bn' => 'বসুন্ধরা, ঢাকা (এভারকেয়ার এর পাশে)',
                'address_en' => 'Bashundhara, Dhaka (beside Evercare)',
                'hotline'    => '09610987121',
                'map_query'  => 'APON Healthcare Bashundhara Dhaka',
                'is_active'  => true,
                'sort_order' => 20,
            ],
        );

        /* APON — বিকাল ৫টা – রাত ৮টা, ৫ মিনিট অন্তর, সর্বোচ্চ ৪০ সিরিয়াল।
           (৩ ঘণ্টায় ৫ মিনিট অন্তর = বাস্তবে ৩৬টি আঁটে; সময় বাড়ালে ৪০ হবে।) */
        foreach (range(0, 6) as $dow) {
            Schedule::updateOrCreate(
                ['chamber_id' => $apon->id, 'day_of_week' => $dow],
                ['start_time' => '17:00', 'end_time' => '20:00', 'slot_minutes' => 5, 'max_serials' => 40, 'is_active' => true],
            );
        }
    }
}
