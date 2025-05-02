<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdate;
use App\Models\Queues;
use App\Models\QueueStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

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
}
