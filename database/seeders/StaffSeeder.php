<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->createOne([
            'name' => 'Ailine M. Torrentira',
            'email' => 'laboratory@technician',
            'password' => bcrypt('lab123'),
            'role_id' => 3,
        ]);

        User::factory()->createOne([
            'name' => 'Jake the Medicator',
            'email' => 'medical@technician',
            'password' => bcrypt('medical123'),
            'role_id' => 2,
        ]);
    }
}
