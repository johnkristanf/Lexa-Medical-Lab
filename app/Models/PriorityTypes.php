<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriorityTypes extends Model
{
    protected $fillable = [
        'name',
        'priority_level',
        'code',
    ];

    public function queues(): HasMany
    {
        return $this->hasMany(Queues::class, 'priority_type_id');
    }
}
