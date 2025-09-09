<?php

namespace App\Http\Controllers;

use App\Logs;
use DateTime;
use App\Models\Test;
use Inertia\Inertia;
use App\Models\Queues;
use App\Models\Patient;
use App\Models\TestType;
use App\Events\QueueUpdate;
use App\Models\QueueStatus;
use App\Models\TestPurpose;
use App\Models\Appointments;
use App\Models\TestCategory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\ResultEmailReminder;
use Illuminate\Support\Facades\DB;
use App\Models\AppointmentSchedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class MedicalStaffController extends Controller
{
    public function queuePage(Request $request)
    {
        $statusID = $request->input('status_id', '1');

        // FETCH ALL STATUS FOR TABLE FILTER
        $queueStatuses = QueueStatus::select('id', 'name', 'tag')
            ->where('tag', '!=', 'no_show')
            ->get();

        // FETCH ALL QUEUE DATA ALONG WITH PRIORITY TYPE RELATIONSHIP
        $queues = Queues::with(['priority_types' => function ($query) {
            $query->select('id', 'name', 'code', 'priority_level');
        }])
            ->where('status_id', $statusID)
            ->get()
            ->sortBy(fn($queue) => $queue->priority_types->priority_level)
            ->values(); // Reset index


        return Inertia::render('Medical/Queue', [
            'queue_statuses' => $queueStatuses,
            'queues' => $queues
        ]);
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'queue_id' => 'required|exists:queues,id',
            'status_id' => 'required|exists:queue_statuses,id',
        ]);

        $queue = Queues::findOrFail($request->queue_id);
        $queue->status_id = $request->status_id;
        $queue->save();

        broadcast(new QueueUpdate($queue->id));
        return back();
    }

    //Dashboard Data items For Medical staff

    public function medicalAppointmentPage(Request $request)
    {
        $appointments = Appointments::with(['schedule', 'test_types'])
            ->latest()
            ->get();

        $schedules = AppointmentSchedule::select('id', 'schedule', 'status')
            ->latest()
            ->get();

        return Inertia::render('Patient/Appointments', [
            'appointments' => $appointments,
            'schedules' => $schedules,
        ]);
    }

    //Patient Details
    public function patientDetailscreate(Request $request)
    {
        $patientsDetails = Patient::all();
        $testTypesPurpose = TestPurpose::all();
        $patientUpdate = Patient::find($request->input('id'));
        // $testTypesRequest = TestRequest::all();
        $testCategory = TestCategory::with('testTypes')->get();


        return Inertia::render('Patient/PatientDetails', [
            'patients' => $patientsDetails,
            'testTypesPurpose' => $testTypesPurpose,
            // 'testTypesRequest' => $testTypesRequest,
            'testCategory' => $testCategory,
            'patientUpdate' => $patientUpdate,
        ]);
    }

    public function patientDetailsStore(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string|max:10',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'email' => 'required|email|max:255|unique:patients,email',
        ]);
        $patient = Patient::create($validated);

        return redirect()->back()->with('success', 'Patient details added successfully.');
    }

    public function testCategoryCreate(Request $request)
    {
        // dd($request->all());
        $testCategory = TestCategory::with('testTypes')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10);
        return Inertia::render('TestCategory/TestCategory', [
            'test_category' => $testCategory
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
            'contact_number' => 'required|string|max:15',
            'email' => 'required|email|max:255|unique:patients,email,' . $patient->id,
        ]);

        $patient->update($validated);

        return redirect()->back()->with('success', 'Patient details updated successfully.');
    }

    public function testCategoryStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reference_range' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
            'price' => 'required|integer',
            'test_category_id' => 'required|exists:test_category,id',
        ]);

        TestType::create($validated);

        // dd($request->all());

        // DB::table('test_types')->insert($request->all());

        return redirect()->back()->with('success', 'Test Type created successfully.');
    }

    public function testStore(Request $request)
    {

        $validated = $request->validate([
            'referer_fullname' => 'required|string|max:255',
            'doctor_license_no' => 'required|string|max:255',
            'test_schedule' => 'required|date',
            'total_price' => 'required|string',
            'purpose_id' => 'required|integer',
            'patient_id' => 'required|integer',
            'category_id' => 'required|integer',
            'selected_test_types' => 'required|array',
        ]);

        $orNumber = (string) random_int(10000, 99999);

        $test = Test::create([
            'referer_fullname'     => $validated['referer_fullname'],
            'doctor_license_no'    => $validated['doctor_license_no'],
            'test_schedule'        => $validated['test_schedule'],
            'total_price'          => $validated['total_price'],
            'or_number'            => $orNumber,
            'purpose_id'           => $validated['purpose_id'],
            'patient_id'           => $validated['patient_id'],
            'category_id'          => $validated['category_id'],
            'selected_test_types'  => json_encode($validated['selected_test_types']),
        ]);

        $testID = $test->id;


        // INSERT PATIENT TEST TO THE PIVOT TABLE
        $patient = Patient::find($validated['patient_id']);
        $patient->test_types()->attach($validated['selected_test_types'], [
            'test_id' => $testID,
            'results' => null,
        ]);
    }

    public function testDetailsCreate(Request $request)
    {
        $testDetails = Test::all();
        return Inertia::render('Test/TestDetails', [
            'testDetails' => $testDetails
        ]);
    }


    public function testDetailsByID(string $patientID, string $testID)
    {
        $patient = Patient::where('id', $patientID)
            ->whereHas('test_types', function ($query) use ($testID) {
                $query->where('patient_test_type.test_id', $testID);
            })
            ->with(['test_types' => function ($query) use ($testID) {
                $query->wherePivot('test_id', $testID);
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

        Log::info("CHECK DATA: ", [
            'patientID' => $patientID,
            '$request' => $request
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

        return back()->with('success', 'Test Results Updated.');
    }


    public function print($testID)
    {
        $testDetail = Test::findOrFail($testID);
        $patientDetails = Patient::findOrFail($testDetail->patient_id);
        $testCategory = TestCategory::findOrFail($testDetail->category_id);

        $testPatient = $this->testDetailsByID($testDetail->patient_id, $testID);
        $testTypes = $testPatient->test_types ?? collect();


        $dob = new DateTime($patientDetails->date_of_birth);
        $today = new DateTime(); 
        $age = $dob->diff($today)->y;

        return Pdf::loadView('pdf.test-detail', compact(
            'patientDetails',
            'testCategory',
            'age',
            'testDetail',
            'testTypes'
        ))->setPaper('A4', 'portrait')
            ->setOptions(['defaultFont' => 'DejaVu Sans'])
            ->stream('combined-details.pdf');
    }

    public function sendEmailResultReminder(Request $request)
    {

        Mail::to('patient@gmail.com')->send(new ResultEmailReminder());

        return back()->with('success', 'Reminder email sent.');
    }
}
