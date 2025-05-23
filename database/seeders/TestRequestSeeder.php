<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TestRequest;


class TestRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TestRequest::insert([
            ['test_requestname' => 'Internal Doctor'],
            ['test_requestname' => 'External Referral'],
            ['test_requestname' => 'Patient Request'],
        ]);

        // foreach ($roles as $role) {
        //     TestRequest::firstOrCreate($role);
        // }
    }
}
