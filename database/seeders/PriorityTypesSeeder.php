<?php

namespace Database\Seeders;

use App\Models\PriorityTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriorityTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PriorityTypes::firstOrCreate([
            'name' => 'Person with Disability',
            'priority_level' => 1,
            'code' => 'PWD'
        ]);
        
        PriorityTypes::firstOrCreate([
            'name' => 'Senior Citizen',
            'priority_level' => 1,
            'code' => 'SC'
        ]);
        
        PriorityTypes::firstOrCreate([
            'name' => 'Pregnant Woman',
            'priority_level' => 1,
            'code' => 'PW'
        ]);
        
        PriorityTypes::firstOrCreate([
            'name' => 'Regular Patient',
            'priority_level' => 2,
            'code' => 'RP'
        ]);
    }
}
