<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@drabusufian.com');

        /* পাসওয়ার্ড .env থেকে নেওয়া হয়। না থাকলে এলোমেলোভাবে তৈরি করে
           টার্মিনালে একবার দেখানো হয় — যাতে ভুল করেও দুর্বল ডিফল্ট
           পাসওয়ার্ড নিয়ে সাইট লাইভে না যায়। */
        $password = env('ADMIN_PASSWORD');
        $generated = false;

        if (blank($password)) {
            $password = Str::password(14, symbols: false);
            $generated = true;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'      => 'অ্যাডমিন',
                'password'  => $password,
                'role'      => 'admin',
                'is_active' => true,
            ],
        );

        $this->command->newLine();
        $this->command->info('👤 অ্যাডমিন অ্যাকাউন্ট তৈরি হয়েছে');
        $this->command->line('   ইমেইল:     ' . $user->email);

        if ($generated) {
            $this->command->line('   পাসওয়ার্ড:  ' . $password);
            $this->command->warn('   ⚠️  পাসওয়ার্ডটি এখনই কোথাও লিখে রাখুন — আর দেখানো হবে না।');
            $this->command->warn('   ⚠️  লগইন করে সাথে সাথে বদলে নিন।');
        } else {
            $this->command->line('   পাসওয়ার্ড:  (.env এর ADMIN_PASSWORD থেকে নেওয়া)');
        }
    }
}
