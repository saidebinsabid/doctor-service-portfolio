<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Qualification;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        /* ---------- পেশাগত অভিজ্ঞতা ----------
           ✅ প্রচারপত্রের ক্রম অনুযায়ী ৭টি।
           ২ নম্বরটি ভিজিটিং কার্ড অনুযায়ী "অধ্যক্ষ" (Principal),
           প্রচারপত্রে ছিল "বিভাগীয় প্রধান" — ক্লায়েন্টের সিদ্ধান্ত অনুযায়ী
           ভিজিটিং কার্ডকেই চূড়ান্ত ধরা হয়েছে। */
        $rows = [
            ['building', 'প্রতিষ্ঠাতা অধ্যক্ষ', 'Founder Principal',
                'হবিগঞ্জ মেডিকেল কলেজ, হবিগঞ্জ', 'Habiganj Medical College, Habiganj'],

            ['cap', 'সাবেক অধ্যাপক ও অধ্যক্ষ', 'Former Professor & Principal',
                'ব্রাহ্মণবাড়িয়া মেডিকেল কলেজ', 'Brahmanbaria Medical College'],

            ['stetho', 'সাবেক বিশেষজ্ঞ চিকিৎসক', 'Former Consultant',
                'বাংলাদেশ মেডিকেল বিশ্ববিদ্যালয় (সাবেক পিজি হাসপাতাল)',
                'Bangladesh Medical University (former PG Hospital)'],

            ['flask', 'সাবেক গবেষক', 'Former Researcher',
                'আইসিডিডিআর,বি (icddr,b)', 'icddr,b'],

            ['scalpel', 'সাবেক হাউস সার্জন', 'Former House Surgeon',
                'বঙ্গবন্ধু হাসপাতাল, মহাখালী, ঢাকা', 'Bangabandhu Hospital, Mohakhali, Dhaka'],

            ['book', 'সাবেক রেজিস্ট্রার', 'Former Registrar',
                'সিলেট এমএজি ওসমানী মেডিকেল কলেজ হাসপাতাল',
                'Sylhet MAG Osmani Medical College Hospital'],

            ['users', 'সাবেক লেকচারার', 'Former Lecturer',
                'স্যার সলিমুল্লাহ মেডিকেল কলেজ ও মিটফোর্ড হাসপাতাল',
                'Sir Salimullah Medical College & Mitford Hospital'],
        ];

        foreach ($rows as $i => [$icon, $posBn, $posEn, $orgBn, $orgEn]) {
            Experience::updateOrCreate(
                ['position_bn' => $posBn, 'organization_bn' => $orgBn],
                [
                    'position_en'     => $posEn,
                    'organization_en' => $orgEn,
                    'icon'            => $icon,
                    'sort_order'      => ($i + 1) * 10,
                    'is_active'       => true,
                ],
            );
        }

        /* ---------- শিক্ষাগত যোগ্যতা ----------
           ⚠️ ডিগ্রির নাম ✅ যাচাইকৃত, কিন্তু প্রতিষ্ঠান ও সাল এখনো পাওয়া যায়নি।
              খালি রাখা হয়েছে — ওয়েবসাইটে শুধু ডিগ্রির নাম দেখাবে,
              ফাঁকা ঘর বা "অজানা" লেখা দেখাবে না। */
        $quals = [
            ['এফসিপিএস (শিশুরোগ)', 'FCPS (Pediatrics)',
                'বাংলাদেশ কলেজ অব ফিজিশিয়ানস অ্যান্ড সার্জনস',
                'Bangladesh College of Physicians & Surgeons'],
            ['ডিসিএইচ (শিশু স্বাস্থ্য ডিপ্লোমা)', 'DCH (Diploma in Child Health)', null, null],
            ['এমবিবিএস', 'MBBS', null, null],
        ];

        foreach ($quals as $i => [$dBn, $dEn, $iBn, $iEn]) {
            Qualification::updateOrCreate(
                ['degree_bn' => $dBn],
                [
                    'degree_en'      => $dEn,
                    'institution_bn' => $iBn,
                    'institution_en' => $iEn,
                    'year'           => null,          // ⚠️ অজানা
                    'sort_order'     => ($i + 1) * 10,
                    'is_active'      => true,
                ],
            );
        }
    }
}
