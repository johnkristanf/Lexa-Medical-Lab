<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Counter extends Model
{
    protected $guarded = ['id'];

    public function queue(): HasOne 
    {
        return $this->hasOne(Queues::class, 'counter_id');
    }
}
