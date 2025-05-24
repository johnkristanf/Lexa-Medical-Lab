<?php

namespace App\Http\Controllers;

use App\Models\TestCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index()
    {
        $testCategories = TestCategory::with('testTypes')->get();

        return Inertia::render('Appointment/Index', [
            'test_categories' => $testCategories
        ]);
    }
}
