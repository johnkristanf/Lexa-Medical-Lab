<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MedicalSupplies extends Model
{
    protected $guarded = [
        'id'
    ];

    public function supply_request(): BelongsToMany
    {
        return $this->belongsToMany(
            SupplyRequest::class,  // related model
            'medical_supply_request',      // pivot table name
            'request_id',            // foreign key on pivot table for this model
            'supply_id'              // foreign key on pivot table for related model
        )
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
