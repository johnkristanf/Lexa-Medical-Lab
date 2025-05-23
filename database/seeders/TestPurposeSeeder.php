<?php

namespace Database\Seeders;

use App\Models\TestPurpose;
use App\Models\TestRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TestPurpose::insert([
            ['test_purposename' => 'Personal'],
            ['test_purposename' => 'Work'],
            ['test_purposename' => 'School'],
            ['test_purposename' => 'Insurance'],
            ['test_purposename' => 'Follow-up'],
        ]);

        // foreach ($roles as $role) {
        //     TestPurpose::firstOrCreate($role);
        // }
    }
}
