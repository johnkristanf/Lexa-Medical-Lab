<?php

namespace Database\Seeders;

use App\Models\QueueStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QueueStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Waiting',
                'tag' => 'waiting',
            ],
            [
                'name' => 'Serving',
                'tag' => 'serving',
            ],
            [
                'name' => 'Served',
                'tag' => 'served',
            ],
        ];

        foreach ($statuses as $status) {
            QueueStatus::firstOrCreate($status);
        }
    }
}
