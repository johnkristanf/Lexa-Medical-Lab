<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdate;
use App\Mail\ResultEmailReminder;
use App\Models\Appointments;
use App\Models\AppointmentSchedule;
use App\Models\Patient;
use App\Models\PriorityTypes;
use App\Models\Queues;
use App\Models\QueueStatus;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestPurpose;
use App\Models\TestType;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MedicalStaffController extends Controller
{

    public function queuePage(Request $request)
    {
        $statusID = $request->input('status_id', '1');

        $queueStatuses = QueueStatus::select('id', 'name', 'tag')
            ->where('tag', '!=', 'no_show')
            ->get();

        $queues = Queues::with(['priority_types' => function ($query) {
            $query->select('id', 'name', 'code', 'priority_level');
        }])
            ->where('status_id', $statusID)
            ->get()
            ->sortBy(fn ($queue) => $queue->priority_types->priority_level)
            ->values();

        return Inertia::render('Medical/Queue', [
            'queue_statuses' => $queueStatuses,
            'queues' => $queues,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'queue_id' => 'required',
            'status_id' => 'required|exists:queue_statuses,id',
        ]);

        $queueIds = is_array($request->queue_id) ? $request->queue_id : [$request->queue_id];

        foreach ($queueIds as $id) {
            $queue = Queues::find($id);
            if ($queue) {
                $queue->status_id = $request->status_id;
                $queue->save();

                broadcast(new QueueUpdate($queue->id));
            }
        }

        return back();
    }

    // Dashboard Data items For Medical staff

    public function medicalAppointmentPage(Request $request)
    {
        $searchQuery = $request->query('search');
        Log::info('searchQuery medical: ', [$searchQuery]);
        $appointments = Appointments::with(['schedule', 'time_slot', 'test_types.test_category'])
            ->when($searchQuery, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('appointment_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere(
                            DB::raw("CONCAT(first_name, ' ', middle_name, ' ', last_name)"),
                            'like',
                            "%{$search}%"
                        );
                });
            })
            ->latest()
            ->get();

        $schedules = AppointmentSchedule::select('id', 'date')
            ->with(['appointment_slots'])
            ->latest()
            ->get();

        return Inertia::render('Patient/Appointments', [
            'appointments' => $appointments,
            'schedules' => $schedules,
        ]);
    }

    // Patient Details
    public function patientDetailscreate(Request $request)
    {
        $searchQuery = $request->query('search');
        $patientsDetails = Patient::with('priority_type')
            ->when($searchQuery, function ($query, $search) {
                $query->where('patient_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere(
                        DB::raw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)"),
                        'like',
                        "%{$search}%"
                    );
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $testTypesPurpose = TestPurpose::all();
        $patientUpdate = Patient::find($request->input('id'));
        // $testTypesRequest = TestRequest::all();
        $testCategory = TestCategory::with('testTypes')->get();
        $priorityTypes = PriorityTypes::select('id', 'name', 'code')->get();

        return Inertia::render('Patient/PatientDetails', [
            'patients' => $patientsDetails,
            'testTypesPurpose' => $testTypesPurpose,
            // 'testTypesRequest' => $testTypesRequest,
            'testCategory' => $testCategory,
            'patientUpdate' => $patientUpdate,
            'priority_types' => $priorityTypes
        ]);
    }

   public function patientDetailsStore(Request $request)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'gender' => 'required|string|max:10',
        'date_of_birth' => 'required|date',
        'address' => 'required|string|max:255',
        'contact_number' => 'nullable|string|max:11',
        'email' => 'nullable|email|max:255|unique:patients,email',
        'priority_type.id' => 'required|exists:priority_types,id',
    ]);

    DB::transaction(function () use ($validated) {

        $year = now()->year;

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // In PostgreSQL, use ::INTEGER for casting
            $orderExpr = "SUBSTRING(patient_id FROM 6)::INTEGER";
        } else {
            // In MySQL/MariaDB, use CAST AS UNSIGNED
            $orderExpr = "CAST(SUBSTRING(patient_id, 6) AS UNSIGNED)";
        }

        $lastPatient = Patient::where('patient_id', 'like', $year . '-%')
            ->lockForUpdate()
            ->orderByRaw("$orderExpr DESC")
            ->first();

        $nextNumber = $lastPatient
            ? ((int) substr($lastPatient->patient_id, 5)) + 1
            : 1;

        $patientId = $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        Patient::create([
            'patient_id' => $patientId,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'],
            'priority_id' => $validated['priority_type']['id'],
        ]);
    });

    return redirect()->back()->with('success', 'Patient details added successfully.');
 }

    public function testCategoryCreate(Request $request)
    {
        $searchQuery = $request->query('search');
        Log::info('searchQuery: ', [$searchQuery]);
        $testCategory = TestCategory::with('testTypes')
            ->when($searchQuery, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        Log::info('testCategory: ', [$testCategory]);

        return Inertia::render('TestCategory/TestCategory', [
            'test_category' => $testCategory,
        ]);
    }

    public function updatePatientDetails(Request $request, Patient $patient)
    {

        $validated = $request->validate([
            'patient_id' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string|max:10',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:11',
            'email' => 'nullable|email|max:255|unique:patients,email,'.$patient->id,
        ]);

        $patient->update($validated);

        return redirect()->back()->with('success', 'Patient details updated successfully.');
    }

    public function deletePatientDetails(Patient $patient)
    {
        $patient->delete();

        return redirect()->back()->with('success', 'Patient deleted successfully.');
    }

    public function testCategoryStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer',
        ]);

        TestCategory::create($validated);

        return redirect()->back()->with('success', 'Test Category created successfully.');
    }

    public function testCategoryDelete($id)
    {
        $testCategory = TestCategory::findOrFail($id);
        $testCategory->delete();

        return redirect()->back()->with('success', 'Test Category deleted successfully.');
    }

    public function testTypeStore(Request $request)
    {
        // Check if test_types is present (batch insert from modal) or single insert
        if ($request->has('test_types') && is_array($request->test_types)) {
            // Batch mode (validate each item in test_types array)
            $validated = $request->validate([
                'test_types' => 'required|array|min:1',
                'test_category_id' => 'required|exists:test_category,id',
                'test_types.*.name' => 'required|string|max:255',
                'test_types.*.reference_range' => 'nullable|string|max:255',
                'test_types.*.unit' => 'nullable|string|max:255',
            ]);

        Log::info("TEST TYPES: ", [$validated]);

            $testTypes = [];
            foreach ($request->input('test_types') as $testType) {
                // Prepare each test type with the test_category_id from request
                $testTypes[] = [
                    'name' => $testType['name'],
                    'reference_range' => $testType['reference_range'],
                    'unit' => $testType['unit'] ?? null,
                    'test_category_id' => $request->input('test_category_id'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            TestType::insert($testTypes);

            return redirect()->back()->with('success', count($testTypes).' Test Type(s) created successfully.');
        } else {
            // Single test type mode (e.g. manual entry)
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'reference_range' => 'required|string|max:255',
                'unit' => 'nullable|string|max:255',
                'price' => 'required|integer',
                'test_category_id' => 'required|exists:test_category,id',
            ]);

            \App\Models\TestType::create($validated);

            return redirect()->back()->with('success', 'Test Type created successfully.');
        }
    }


    public function testStore(Request $request)
    {

        $validated = $request->validate([
            'referer_fullname' => 'required|string|max:255',
            'doctor_license_no' => 'nullable|string|max:255',
            'test_schedule' => 'required|date',
            'test_schedule_time' => 'nullable|date_format:H:i',
            'total_price' => 'required|string',
            'purpose_id' => 'required|integer',
            'patient_id' => 'required|integer',
            'category_ids' => 'required|array',
            'category_ids.*' => 'integer',
            'selected_test_types' => 'required|array',
        ]);
        Log::info("validated: ", [$validated]);

        $orNumber = (string) random_int(10000, 99999);

        $totalPriceFloat = (float) preg_replace('/[^\d.]/', '', $validated['total_price']);
        Log::info("totalPriceFloat: ", [$totalPriceFloat]);

        $test = Test::create([
            'referer_fullname' => $validated['referer_fullname'],
            'doctor_license_no' => $validated['doctor_license_no'],
            'test_schedule' => $validated['test_schedule'],
            'test_schedule_time' => $validated['test_schedule_time'] ?? null,
            'total_price' => $totalPriceFloat,
            'or_number' => $orNumber,
            'purpose_id' => $validated['purpose_id'],
            'patient_id' => $validated['patient_id'],
            'selected_test_types' => json_encode($validated['selected_test_types']),
        ]);

        $testID = $test->id;

        if (!empty($validated['category_ids'])) {
            $categoriesToInsert = [];
            foreach ($validated['category_ids'] as $categoryId) {
                $categoriesToInsert[] = [
                    'test_id' => $testID,
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            \App\Models\TestSelectedCategory::insert($categoriesToInsert);
        }

        // INSERT PATIENT TEST TO THE PIVOT TABLE 123123123213123
        $patient = Patient::find($validated['patient_id']);
        $patient->test_types()->attach($validated['selected_test_types'], [
            'test_id' => $testID,
            'results' => null,
        ]);
    }

    public function testDetailsCreate(Request $request)
    {
        $searchQuery = $request->query('search');
        $testDetails = Test::when($searchQuery, function ($query, $search) {
            $query->where('referer_fullname', 'like', "%{$search}%");
        })
        ->latest()
        ->get();

        return Inertia::render('Test/TestDetails', [
            'testDetails' => $testDetails,
        ]);
    }

    public function testDetailsByID(string $patientID, string $testID)
    {
        $patient = Patient::where('id', $patientID)
            ->whereHas('test_types', function ($query) use ($testID) {
                $query->where('patient_test_type.test_id', $testID);
            })
            ->with(['test_types' => function ($query) use ($testID) {
                $query->wherePivot('test_id', $testID)->with('test_category'); // Load category to group on frontend
            }])
            ->first();

        return $patient;
    }

    public function updateTestResults(Request $request, string $patientID, string $testID)
    {
        $request->validate([
            'test_results' => 'required|array',
            'test_results.*.test_type_id' => 'required|exists:test_types,id',
            'test_results.*.result' => 'nullable|string|max:255',
        ]);

        Log::info('CHECK DATA: ', [
            'patientID' => $patientID,
            '$request' => $request,
        ]);

        foreach ($request->test_results as $item) {
            DB::table('patient_test_type')
                ->where('patient_id', $patientID)
                ->where('test_type_id', $item['test_type_id'])
                ->where('test_id', $testID)
                ->update([
                    'results' => $item['result'],
                    'updated_at' => now(),
                ]);
        }

        // Update test status to completed after saving results
        $testDetail = Test::findOrFail($testID);
        if ($testDetail->status !== 'completed') {
            $testDetail->status = 'completed';
            $testDetail->save();
        }

        return back()->with('success', 'Test Results Updated.');
    }

    public function print($testID)
    {
        // Wrap everything in a DB transaction
        return DB::transaction(function () use ($testID) {
            $testDetail = Test::with('selected_categories.test_category')->findOrFail($testID);
            $patientDetails = Patient::findOrFail($testDetail->patient_id);

            $testPatient = $this->testDetailsByID($testDetail->patient_id, $testID);
            $testTypes = $testPatient->test_types ?? collect();

            // Group tests by category for separate pages
            $categoriesData = [];
            if ($testDetail->selected_categories->isNotEmpty()) {
                foreach ($testDetail->selected_categories as $selected) {
                    $cat = $selected->test_category;
                    $categoriesData[] = [
                        'name' => $cat ? $cat->name : 'N/A',
                        'tests' => $testTypes->where('test_category_id', $selected->category_id)
                    ];
                }
            } else {
                $legacyCategory = TestCategory::where('id', $testDetail->category_id)->first();
                $categoriesData[] = [
                    'name' => $legacyCategory ? $legacyCategory->name : 'N/A',
                    'tests' => $testTypes
                ];
            }

            $dob = new \DateTime($patientDetails->date_of_birth);
            $today = new \DateTime;
            $age = $dob->diff($today)->y;

            $logoBase64 = $this->getLogoAsBase64();

            return Pdf::loadView('pdf.test-detail', compact(
                'patientDetails',
                'categoriesData',
                'age',
                'testDetail',
                'logoBase64'
            ))->setPaper('A4', 'portrait')
                ->setOptions(['defaultFont' => 'DejaVu Sans'])
                ->stream('combined-details.pdf');
        });
    }

    private function getLogoAsBase64()
    {
        $logoPath = public_path('img/lexa-logo-removedbg.png');

        if (file_exists($logoPath)) {
            $imageData = base64_encode(file_get_contents($logoPath));
            $mimeType = mime_content_type($logoPath);

            return 'data:'.$mimeType.';base64,'.$imageData;
        }

        return null;
    }

    public function sendEmailResultReminder(Request $request)
    {

        Mail::to($request->get('email'))->send(new ResultEmailReminder);

        return back()->with('success', 'Reminder email sent.');
    }

    public function printPatientReport(){

        $patients_report = Patient::all();
        $logoLexa = $this->logoLexa();

        $pdf = Pdf::loadView('pdf.patient_report', [
            'patients'=> $patients_report,
            'logoLexa'=> $logoLexa
        ])->setPaper('a4', 'landscape');


        return $pdf->stream('patient_report.pdf');

    }

     private function logoLexa()
    {
        $logoPath = public_path('img/lexa-logo-removedbg.png');

        if (file_exists($logoPath)) {
            $imageData = base64_encode(file_get_contents($logoPath));
            $mimeType = mime_content_type($logoPath);
            return 'data:' . $mimeType . ';base64,' . $imageData;
        }

        return null;
    }
}
