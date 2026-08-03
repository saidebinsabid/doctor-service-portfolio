<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SettingsSeeder::class,
            ChamberSeeder::class,
            ServiceSeeder::class,
            ExperienceSeeder::class,
            FaqSeeder::class,
            NoticeSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ ডাটাবেস প্রস্তুত।');
        $this->command->line('   অ্যাডমিন প্যানেল:  /admin');
        $this->command->newLine();
        $this->command->warn('⚠️  যেসব তথ্য এখনো ডেমো — অ্যাডমিন প্যানেল থেকে বদলাতে হবে:');
        $this->command->line('   • ভিজিট ফি (এখন "ফি দেখান" বন্ধ আছে, তাই সাইটে দেখাচ্ছে না)');
        $this->command->line('   • ডাক্তারের ছবি');
        $this->command->line('   • শিক্ষাগত যোগ্যতার প্রতিষ্ঠান ও সাল');
        $this->command->line('   • চেম্বারের ছবি (গ্যালারি এখন লুকানো)');
        $this->command->line('   • রোগীদের মতামত (সেকশন এখন লুকানো)');
        $this->command->newLine();
    }
}
