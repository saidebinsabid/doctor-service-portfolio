<?php

namespace Database\Seeders;

use App\Models\Chamber;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

/**
 * চেম্বারের সময়সূচি — ক্লায়েন্টের দেওয়া মান।
 *   শনি – বৃহস্পতিবার : সকাল ১০টা – বিকাল ৩টা
 *   শুক্রবার          : বিকাল ৩টা – রাত ৯টা
 *
 * idempotent: (chamber_id, day_of_week) দিয়ে updateOrCreate।
 */
class ChamberScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $chamber = Chamber::query()->first();

        if (! $chamber) {
            return;
        }

        // day_of_week (0=রবি .. 6=শনি) => [start, end, slot_minutes]
        $rows = [
            6 => ['10:00', '15:00', 8],  // শনিবার
            0 => ['10:00', '15:00', 8],  // রবিবার
            1 => ['10:00', '15:00', 8],  // সোমবার
            2 => ['10:00', '15:00', 8],  // মঙ্গলবার
            3 => ['10:00', '15:00', 8],  // বুধবার
            4 => ['10:00', '15:00', 8],  // বৃহস্পতিবার
            5 => ['15:00', '21:00', 7],  // শুক্রবার
        ];

        foreach ($rows as $dow => [$start, $end, $min]) {
            Schedule::updateOrCreate(
                ['chamber_id' => $chamber->id, 'day_of_week' => $dow],
                [
                    'start_time'   => $start . ':00',
                    'end_time'     => $end . ':00',
                    'slot_minutes' => $min,
                    'max_serials'  => 25,
                    'is_active'    => true,
                ],
            );
        }
    }
}
