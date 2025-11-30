<?php

namespace App\Services;

use Illuminate\Support\Facades\Queue;

class QueueService
{
    public function getNewQueueNumber($priorityTypeID)
    {

        $lastQueue = Queue::where(function ($query) use ($priorityTypeID) {
            $query->whereDate('created_at', now()->toDateString())
                ->where('priority_type_id', $priorityTypeID);
        })
            ->orderBy('created_at', 'desc')
            ->first();

        // DEFAULT NUMBER
        $nextNumber = 1;

        if ($lastQueue) {

            // FROM SC-01
            $splittedQueueNumber = explode('-', $lastQueue->queue_number);

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
}
