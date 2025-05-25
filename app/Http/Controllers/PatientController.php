<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function renderAdminPatients()
    {
        $patients = Patient::select([
            'id',
            'patient_id',
            'first_name',
            'middle_name',
            'last_name',
            'gender',
            'date_of_birth',
            'address',
            'contact_number',
            'email',
        ])->get();

        Log::info("Patient: ", [
            'patients' => $patients
        ]);

        return Inertia::render('Admin/Patients', [
            'patients' => $patients
        ]);
    }
}
