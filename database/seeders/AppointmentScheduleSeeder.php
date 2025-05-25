<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('appointment_schedules')->insert([
            [
                'schedule' => Carbon::parse('2025-06-01 09:00:00'),
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule' => Carbon::parse('2025-06-01 10:30:00'),
                'status' => 'unavailable',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule' => Carbon::parse('2025-06-02 10:30:00'),
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
