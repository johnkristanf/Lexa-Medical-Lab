<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queues extends Model
{
    protected $guarded = ['id'];

    public function priority_types(): BelongsTo
    {
        return $this->belongsTo(PriorityTypes::class, 'priority_type_id');
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class, 'counter_id');
    }

    public function queue_status(): BelongsTo
    {
        return $this->belongsTo(QueueStatus::class, 'status_id');
    }
}
