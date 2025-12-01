<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdate;
use App\Models\PriorityTypes;
use App\Models\Queues;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class QueueController extends Controller
{


    public function __construct(protected QueueService $queueService){}

   
    public function create(Request $request): Response
    {
        $defaultRegularPatientID = 3;
        $priorityTypeId = $request->input('id', $defaultRegularPatientID);
        $priorityType = PriorityTypes::findOrFail($priorityTypeId);

        $priorityTypes = PriorityTypes::select('id', 'name', 'code')->get();
        $queueNumber = $this->queueService->getNewQueueNumber($priorityType->id);

        if ($queueNumber === null) {
            // If there are no existing queues, default to "01" using the priority type's code
            $queueNumber = '01';
        }
        return Inertia::render('Queue/CreateQueue', [
            'priority_types' => $priorityTypes,
            'queue_number' => $queueNumber,
        ]);
    }

   

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'priority_type.id' => 'required|integer',
            'priority_type.name' => 'required|string',
            'queue_number' => 'required',
        ]);

        $queue = Queues::create([
            'name' => $validated['patient_name'],
            'priority_type_id' => $validated['priority_type']['id'],
            'queue_number' => $validated['queue_number'],
            'status_id' => 1, // DEFAULT TO WAITING
        ]);

        if ($queue) {
            broadcast(new QueueUpdate($queue->id));
        }

        $waitingCount = Queues::where('status_id', 1)->count();

        return redirect()->route('queue.create')->with([
            'success' => 'Successful Queue Insertion!',
            'queueData' => [
                'queue_number' => $queue->queue_number,
                'created_at' => $queue->created_at,
                'waiting_count' => $waitingCount,
            ],
        ]);
    }

    public function dashboard(): Response
    {
        $allQueues = Queues::with([
            'priority_types' => function ($query) {
                $query->select('id', 'name', 'priority_level', 'code');
            },
            'queue_status' => function ($query) {
                $query->select('id', 'name', 'tag');
            },
        ])
            ->whereDate('created_at', now()->toDateString())
            ->where('status_id', '!=', 3)
            ->get()
            ->sortBy(function ($queue) {
                return [
                    $queue->priority_types->priority_level,            // 1st: Lower priority_level first
                    $queue->is_appointment ? 0 : 1,                    // 2nd: Appointments first within same priority_level
                    $queue->created_at,                                // 3rd: First-come within same group
                ];
            })
            ->values(); // Reset index

            Log::info("allQueues: ", [$allQueues]);
        return Inertia::render('Queue/DashboardQueue', [
            'queues' => $allQueues,
        ]);
    }
}
