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
use App\Models\TestPurpose;
use App\Models\TestRequest;




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
        // Log::error($request);

        $validated = $request->validate([
            'referer_fullname' => 'required|string|max:255',
            'doctor_license_no' => 'required|string|max:255',
            'reason_for_test' => 'required|string|max:255',
            'test_schedule' => 'required|date',
            'total_price' => 'required|integer',
            'request_id' => 'required|integer',
            'purpose_id' => 'required|integer',
            'patient_id' => 'required|integer',
            'category_id' => 'required|integer',
            'selected_test_types'   => 'required|array',
        ]);

        // dd($validated);

        Test::create([
            'referer_fullname'     => $validated['referer_fullname'],
            'doctor_license_no'    => $validated['doctor_license_no'],
            'reason_for_test'      => $validated['reason_for_test'],
            'test_schedule'        => $validated['test_schedule'],
            'total_price'          => $validated['total_price'],
            'request_id'           => $validated['request_id'],
            'purpose_id'           => $validated['purpose_id'],
            'patient_id'           => $validated['patient_id'],
            'category_id'          => $validated['category_id'],
            'selected_test_types'  => json_encode($validated['selected_test_types']), // manual JSON conversion
        ]);
    }
}
