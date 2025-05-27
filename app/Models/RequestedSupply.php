<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestedSupply extends Model
{
    protected $guarded = ['id'];

    public function supply_request(): BelongsTo
    {
        return $this->belongsTo(SupplyRequest::class, 'request_id');
    }
}
