<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function renderAdminPatients(Request $request)
    {
        $searchQuery = $request->query('search');
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
        ])->when($searchQuery, function ($query) use ($searchQuery) {
            $query->whereRaw(
                "LOWER(CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)) LIKE ?",
                ['%'.strtolower($searchQuery).'%']
            );
        })
            ->get();

        Log::info('Patient: ', [
            'patients' => $patients,
        ]);

        return Inertia::render('Admin/Patients', [
            'patients' => $patients,
        ]);
    }
}
