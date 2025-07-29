<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalSupplies extends Model
{
    protected $guarded = [
        'id'
    ];
    public function supply_request(): BelongsToMany
    {
        return $this->belongsToMany(
            SupplyRequest::class,
            'medical_supply_request',
            'request_id',
            'supply_id'
        )
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'medical_supply_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class , 'medical_supply_id');
    }
}
