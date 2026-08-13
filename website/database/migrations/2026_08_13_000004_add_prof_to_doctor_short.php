<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

/**
 * ক্লায়েন্টের অনুরোধে ডাক্তারের সংক্ষিপ্ত নামের আগে "Prof" যোগ —
 * SMS/WhatsApp বার্তায় "Prof Dr Abu Sufian" / "প্রফেসর ডা. আবু সুফিয়ান" দেখাবে।
 * অ্যাডমিন ইতিমধ্যে বদলে থাকলে (Prof ইতিমধ্যে আছে) সেটাকে টাচ করা হয় না।
 */
return new class extends Migration
{
    public function up(): void
    {
        $row = DB::table('settings')->where('key', 'doctor_short')->first();
        if (! $row) {
            return;
        }

        $updates = [];

        if ($row->value_bn && ! str_contains($row->value_bn, 'প্রফেসর')) {
            $updates['value_bn'] = 'প্রফেসর ' . $row->value_bn;
        }

        if ($row->value_en && stripos($row->value_en, 'prof') === false) {
            $updates['value_en'] = 'Prof ' . $row->value_en;
        }

        if ($updates) {
            $updates['updated_at'] = now();
            DB::table('settings')->where('key', 'doctor_short')->update($updates);
            Setting::flush();
        }
    }

    public function down(): void
    {
        /* একদিকযোগ — ইচ্ছাকৃতভাবে reversal নেই যাতে অ্যাডমিন পরে বদলালে না হারায় */
    }
};
