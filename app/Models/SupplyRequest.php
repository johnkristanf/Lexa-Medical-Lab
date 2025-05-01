<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SupplyRequest extends Model
{
    protected $guarded = ['id'];

    public function medical_supplies(): BelongsToMany
    {
        return $this->belongsToMany(
            MedicalSupplies::class,  // related model
            'medical_supply_request',      // pivot table name
            'request_id',            // foreign key on pivot table for this model
            'supply_id'              // foreign key on pivot table for related model
        )
        ->withPivot('quantity')
        ->withTimestamps();
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
