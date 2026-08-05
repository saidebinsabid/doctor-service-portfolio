<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * ✅ সবগুলো ডাক্তারের ছাপানো প্রচারপত্র থেকে হুবহু নেওয়া।
 *
 * সংখ্যা নির্দিষ্ট নয় — অ্যাডমিন প্যানেল থেকে যত ইচ্ছা যোগ/বিয়োগ করা যাবে,
 * ওয়েবসাইটের গ্রিড নিজে থেকেই মানিয়ে নেবে।
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        /* ---------- সাধারণ সেবা (১৪টি) ---------- */
        $general = [
            ['baby',     'sky',     'নবজাতকের সকল সমস্যা ও পরিচর্যা',                      'Newborn care & neonatal problems'],
            ['thermo',   'rose',    'জ্বর, সর্দি-কাশি, নিউমোনিয়া ও শ্বাসকষ্ট',              'Fever, cough, pneumonia & respiratory distress'],
            ['wind',     'cyan',    'হাঁপানি (অ্যাজমা) ও অ্যালার্জিজনিত রোগ',                'Asthma & allergic diseases'],
            ['droplet',  'amber',   'ডায়রিয়া, আমাশয়, বমি ও পানিশূন্যতা',                  'Diarrhoea, dysentery, vomiting & dehydration'],
            ['apple',    'green',   'অপুষ্টি, খাওয়ায় অরুচি ও বৃদ্ধি-সংক্রান্ত সমস্যা',        'Malnutrition, poor appetite & growth problems'],
            ['utensils', 'lime',    'শিশুদের পুষ্টি ও খাদ্যাভ্যাস বিষয়ক পরামর্শ',            'Child nutrition & dietary counselling'],
            ['pulse',    'violet',  'খিঁচুনি, জ্বরজনিত খিঁচুনি ও অন্যান্য স্নায়ুরোগ',          'Seizure, febrile convulsion & neurological disorders'],
            ['growth',   'indigo',  'জন্মগত রোগ ও বিকাশগত বিলম্বের মূল্যায়ন',              'Congenital disorders & developmental delay assessment'],
            ['gland',    'teal',    'থাইরয়েড, হরমোন ও শিশুদের অন্যান্য জটিল রোগ',          'Thyroid, hormonal & other complex disorders'],
            ['droplets', 'blue',    'প্রস্রাবের সংক্রমণ (ইউটিআই), কিডনি ও মূত্রনালির সমস্যা', 'UTI, kidney & urinary tract problems'],
            ['shield',   'orange',  'লিভার, জন্ডিস ও পরিপাকতন্ত্রের রোগ',                   'Liver, jaundice & gastrointestinal diseases'],
            ['heart',    'red',     'হৃদরোগ, রক্তস্বল্পতা ও রক্তের বিভিন্ন রোগ',              'Cardiac disease, anaemia & blood disorders'],
            ['syringe',  'emerald', 'টিকাদান, বৃদ্ধি ও বিকাশের নিয়মিত ফলো-আপ',            'Immunisation, growth & development follow-up'],
            ['stetho',   'brand',   'শিশুদের সকল সাধারণ ও জটিল রোগের আধুনিক চিকিৎসা',      'Modern treatment of all common & complex child diseases'],
        ];

        foreach ($general as $i => [$icon, $tone, $bn, $en]) {
            Service::updateOrCreate(
                ['title_bn' => $bn],
                [
                    'title_en'   => $en,
                    'icon'       => $icon,
                    'tone'       => $tone,
                    'is_special' => false,
                    'sort_order' => ($i + 1) * 10,
                    'is_active'  => true,
                ],
            );
        }

        /* ---------- বিশেষ চিকিৎসা (৪টি) ----------
           প্রচারপত্রে এগুলো আলাদা হাইলাইট বক্সে ছিল — ডাক্তারের
           ইউনিক সেলিং পয়েন্ট, তাই ওয়েবসাইটেও আলাদা সেকশনে। */
        $special = [
            ['wind',  'sky',    'দীর্ঘমেয়াদি কাশি', 'Chronic Cough',
                'দীর্ঘদিন ধরে না সারা কাশির কারণ নির্ণয় ও চিকিৎসা',
                'Diagnosis and treatment of persistent, long-standing cough'],
            ['pulse', 'violet', 'অ্যাডিনয়েড', 'Adenoid',
                'নাক বন্ধ, মুখ খুলে ঘুমানো ও নাক ডাকার আধুনিক চিকিৎসা। ইনশাআল্লাহ ভাল হবেই, অপারেশন লাগবে না।',
                'Modern care for blocked nose, mouth breathing and snoring. In sha Allah it will get better, no operation needed.'],
            ['gland', 'rose',   'টনসিলাইটিস', 'Tonsillitis',
                'বারবার গলাব্যথা ও টনসিল ফোলার চিকিৎসা ও পরামর্শ',
                'Treatment and advice for recurrent sore throat and swollen tonsils'],
            ['cloud', 'teal',   'সাইনুসাইটিস', 'Sinusitis',
                'মাথাব্যথা, নাক দিয়ে পানি পড়া ও সাইনাস সংক্রমণের চিকিৎসা',
                'Care for headache, runny nose and sinus infection'],
        ];

        foreach ($special as $i => [$icon, $tone, $bn, $en, $dbn, $den]) {
            Service::updateOrCreate(
                ['title_bn' => $bn],
                [
                    'title_en'       => $en,
                    'description_bn' => $dbn,
                    'description_en' => $den,
                    'icon'           => $icon,
                    'tone'           => $tone,
                    'is_special'     => true,
                    'sort_order'     => ($i + 1) * 10,
                    'is_active'      => true,
                ],
            );
        }
    }
}
