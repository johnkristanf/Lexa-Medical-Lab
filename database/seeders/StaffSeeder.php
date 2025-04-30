<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->createOne([
            'name' => 'Ian Pal',
            'email' => 'inventory@officer',
            'password' => bcrypt('officer123'),
            'role_id' => 3,
        ]);


        User::factory()->createOne([
            'name' => 'Jake the Medicator',
            'email' => 'medical@staff',
            'password' => bcrypt('medical123'),
            'role_id' => 2,
        ]);
    }
}
