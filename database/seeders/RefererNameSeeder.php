<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Roles;
use Illuminate\Database\Seeder;

class RefererNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Patient::insert([
            ['Referer_name' => 'Sharmlane Faith Patches,RMT'],
            ['Referer_name' => 'Jane R. Moldez, RMT'],
            ['Referer_name' => 'Jill R. Albino, RMT'],
        ]);

        // foreach ($roles as $role) {
        //     Roles::firstOrCreate($role);
        // }
    }
}
