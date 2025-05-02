<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdate;
use App\Models\PriorityTypes;
use App\Models\Queues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class QueueController extends Controller
{
    public function create(Request $request): Response
    {
        $priorityTypes = PriorityTypes::select('id', 'name', 'code')->get();
        $newQueueNumber = null;

        if ($request->has('id')) {
            $priorityTypeId = $request->input('id');

            Log::info("Priority ID found in request: " . $priorityTypeId);

            $newQueueNumber = $this->getNewQueueNumber($priorityTypeId);

            Log::info("Generated queue number: " . $newQueueNumber);
        } else {
            Log::info("No priority ID in request, using default priority type");

            // If no ID is provided, use the first priority type as default
            $defaultPriorityType = $priorityTypes->first();

            if ($defaultPriorityType) {

                $newQueueNumber = $this->getNewQueueNumber($defaultPriorityType->id);
                Log::info("Default priority type ID: " . $defaultPriorityType->id);
                Log::info("Default queue number: " . $newQueueNumber);
            } else {
                // Fallback in case there are no priority types
                $newQueueNumber = "01";
                Log::warning("No priority types found, using fallback queue number");
            }
        }

        return Inertia::render('Queue/CreateQueue', [
            'priority_types' => $priorityTypes,
            'queue_number' => $newQueueNumber
        ]);
    }

    public function getNewQueueNumber($priorityTypeID)
    {

        // DONT GET CONNFUSED IF IT RETURNS NULL ON EARLY MORNING CAUSE IT FETCHES DATA 
        // IN DAILY 
        Log::info("ID: " . $priorityTypeID);

        $lastQueue = Queues::where(function ($query) use ($priorityTypeID) {
            $query->whereDate('created_at', now()->toDateString())
                ->where('priority_type_id', $priorityTypeID);
        })
            ->orderBy('created_at', 'desc')
            ->first();



        Log::info("Queue Data: ", [
            'lastQueue' => $lastQueue
        ]);


        // DEFAULT NUMBER
        $nextNumber = 1;

        if ($lastQueue) {

            // FROM SC-01
            $splittedQueueNumber = explode('-', $lastQueue->queue_number);


            Log::info("splittedQueueNumber", $splittedQueueNumber);
            if (count($splittedQueueNumber) > 1) {
                // Convert "01" to 1, then add 1 to get 2
                $currentNumber = (int) $splittedQueueNumber[1];
                $nextNumber = $currentNumber + 1;
            }
        }

        // Format number to always have 2 digits
        $newQueueNumber = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);


        return $newQueueNumber;
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
            'status_id' => 1 // DEFAULT TO WAITING
        ]);

        Log::info("queue", [
            'queue Data bago ni' => $queue
        ]);

        if ($queue) {
            broadcast(new QueueUpdate($queue->id));
        }

        $waitingCount = Queues::where('status_id', 1)->count();

        Log::info("queue->queue_number" . $queue->queue_number);
        Log::info("queue->created_at" . $queue->created_at);

        return redirect()->route('queue.create')->with([
            'success' => 'Successful Queue Insertion!',
            'queueData' => [
                'queue_number' => $queue->queue_number,
                'created_at' => $queue->created_at,
                'waiting_count' => $waitingCount
            ]
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
            ->sortBy(fn($queue) => $queue->priority_types->priority_level)
            ->values(); // Reset index


        return Inertia::render('Queue/DashboardQueue', [
            'queues' => $allQueues
        ]);
    }
}
