<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QueueStatus extends Model
{
    protected $guarded = ['id'];

    public function queue(): HasMany
    {
        return $this->hasMany(Queues::class, 'status_id');
    }
}
