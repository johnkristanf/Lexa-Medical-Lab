<?php

namespace Database\Seeders;

use App\Models\Roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin'],
            ['name' => 'medical_staff'],
            ['name' => 'inventory_officer'],
        ];

        foreach ($roles as $role) {
            Roles::firstOrCreate($role);
        }
    }
}
