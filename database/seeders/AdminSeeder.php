<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate([  
            'email' => 'admin@admin',
        ], [
            'name' => 'Administrator',
            'password' => bcrypt('admin123'),
            'role_id' => Roles::ADMIN,
        ]);  
    }
}
