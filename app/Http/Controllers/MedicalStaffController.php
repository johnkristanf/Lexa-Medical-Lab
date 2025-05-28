<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdate;
use App\Models\Queues;
use App\Models\QueueStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\TestCategory;
use App\Models\TestType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Test;
use App\Models\Patient;
use App\Models\TestPurpose;
use Barryvdh\DomPDF\Facade\Pdf;




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

    //Patient Details
    public function patientDetailscreate(Request $request)
    {
        $patientsDetails = Patient::all();
        $testTypesPurpose = TestPurpose::all();
        // $testTypesRequest = TestRequest::all();
        $testCategory = TestCategory::with('testTypes')->get();


        return Inertia::render('Patient/PatientDetails', [
            'patients' => $patientsDetails,
            'testTypesPurpose' => $testTypesPurpose,
            // 'testTypesRequest' => $testTypesRequest,
            'testCategory' => $testCategory,
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
            'reason_for_test' => 'required|string|max:255',
            'test_schedule' => 'required|date',
            'total_price' => 'required|string',
            'purpose_id' => 'required|integer',
            'patient_id' => 'required|integer',
            'category_id' => 'required|integer',
            'selected_test_types' => 'required|array',
        ]);


        Test::create([
            'referer_fullname'     => $validated['referer_fullname'],
            'doctor_license_no'    => $validated['doctor_license_no'],
            'reason_for_test'      => $validated['reason_for_test'],
            'test_schedule'        => $validated['test_schedule'],
            'total_price'          => $validated['total_price'],
            'purpose_id'           => $validated['purpose_id'],
            'patient_id'           => $validated['patient_id'],
            'category_id'          => $validated['category_id'],
            'selected_test_types'  => json_encode($validated['selected_test_types']), // manual JSON conversion
        ]);
    }

    public function testDetailsCreate(Request $request)
    {
        $testDetails = Test::all();
        return Inertia::render('Test/TestDetails', [
            'testDetails' => $testDetails
        ]);
    }

    public function print($id)
    {
        $testDetail = Test::findOrFail($id);
        $patientDetails = Patient::findOrFail($testDetail->patient_id);
        $testTypesPurpose = TestPurpose::findOrFail($testDetail->purpose_id);
        $testCategory = TestCategory::findOrFail($testDetail->category_id);

        // Fix: Decode JSON and fetch all test types
        $testTypeIds = json_decode($testDetail->selected_test_types, true);
        $testTypes = TestType::whereIn('id', $testTypeIds)->get();

        return Pdf::loadView('pdf.test-detail', compact(
            'patientDetails',
            'testTypesPurpose',
            'testCategory',
            'testDetail',
            'testTypes' // Use plural here
        ))->setPaper('A4', 'portrait')
            ->setOptions(['defaultFont' => 'DejaVu Sans'])
            ->stream('combined-details.pdf');
    }
}
