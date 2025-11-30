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
        $priorityTypes = PriorityTypes::select('id', 'name', 'code')->get();
        $newQueueNumber = null;

        if ($request->has('id')) {
            $priorityTypeId = $request->input('id');
            $newQueueNumber = $this->queueService->getNewQueueNumber($priorityTypeId);
        } else {

            // If no ID is provided, use the first priority type as default
            $defaultPriorityType = $priorityTypes->first();

            if ($defaultPriorityType) {

                $newQueueNumber = $this->queueService->getNewQueueNumber($defaultPriorityType->id);
            } else {
                // Fallback in case there are no priority types
                $newQueueNumber = '01';
            }
        }

        return Inertia::render('Queue/CreateQueue', [
            'priority_types' => $priorityTypes,
            'queue_number' => $newQueueNumber,
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
            ->sortBy(fn ($queue) => $queue->priority_types->priority_level)
            ->values(); // Reset index

        return Inertia::render('Queue/DashboardQueue', [
            'queues' => $allQueues,
        ]);
    }
}
