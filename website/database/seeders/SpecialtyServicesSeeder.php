<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * ক্লায়েন্টের দেওয়া বিশেষায়িত রোগের তালিকা — সেবাসমূহ সেকশনে যুক্ত হয়।
 *
 * idempotent: title_bn দিয়ে firstOrCreate, তাই একাধিকবার চালালেও ডুপ্লিকেট হয় না।
 */
class SpecialtyServicesSeeder extends Seeder
{
    public function run(): void
    {
        $base = (int) (Service::max('sort_order') ?? 0);

        foreach ($this->rows() as $i => $row) {
            Service::firstOrCreate(
                ['title_bn' => $row['bn']],
                [
                    'title_en'    => $row['en'],
                    'icon'        => $row['icon'],
                    'tone'        => $row['tone'],
                    'is_special'  => false,
                    'is_active'   => true,
                    'sort_order'  => $base + ($i + 1) * 10,
                ],
            );
        }
    }

    private function rows(): array
    {
        return [
            ['bn' => 'জেনেটিক রোগ',                          'en' => 'Genetic disorders',                    'icon' => 'flask',   'tone' => 'violet'],
            ['bn' => 'শিশুদের জন্মগত হার্টের রোগ',            'en' => 'Congenital heart disease in children',  'icon' => 'heart',   'tone' => 'rose'],
            ['bn' => 'পড়াশোনায় অমনোযোগী (ADHD)',           'en' => 'Inattentiveness in studies (ADHD)',     'icon' => 'book',    'tone' => 'amber'],
            ['bn' => 'অটিজম',                                'en' => 'Autism',                                'icon' => 'users',   'tone' => 'sky'],
            ['bn' => 'শিশুর মানসিক বিকাশজনিত সমস্যা',         'en' => "Child's mental development problems",    'icon' => 'growth',  'tone' => 'teal'],
            ['bn' => 'দেরিতে কথা বলা',                        'en' => 'Delayed speech',                        'icon' => 'baby',    'tone' => 'cyan'],
            ['bn' => 'জন্মগত অস্বাভাবিকতা',                   'en' => 'Congenital abnormalities',              'icon' => 'shield',  'tone' => 'indigo'],
            ['bn' => 'মানসিক বৃদ্ধি কম',                      'en' => 'Intellectual/developmental delay',      'icon' => 'pulse',   'tone' => 'blue'],
            ['bn' => 'সেরিব্রাল পালসি (Cerebral Palsy)',      'en' => 'Cerebral Palsy',                        'icon' => 'stetho',  'tone' => 'emerald'],
        ];
    }
}
