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
        User::updateOrCreate(
            ['email' => 'laboratory@technician'],
            [
                'name' => 'Ailine M. Torrentira',
                'password' => bcrypt('lab123'),
                'role_id' => 3,
            ]
        );

        User::updateOrCreate(
            ['email' => 'medical@technician'],
            [
                'name' => 'Jake the Medicator',
                'password' => bcrypt('medical123'),
                'role_id' => 2,
            ]
        );
    }
}
