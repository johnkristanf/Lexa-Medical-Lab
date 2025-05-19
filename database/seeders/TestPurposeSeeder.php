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
            ['test_requestname' => 'Personal'],
            ['test_requestname' => 'Work'],
            ['test_requestname' => 'School'],
            ['test_requestname' => 'Insurance'],
            ['test_requestname' => 'Follow-up'],
        ]);

        // foreach ($roles as $role) {
        //     TestPurpose::firstOrCreate($role);
        // }
    }
}
