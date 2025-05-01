<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLogs extends Model
{
    protected $guarded = ['id'];

    public function medical_supplies(): BelongsTo
    {
        return $this->belongsTo(MedicalSupplies::class, 'supply_id');
    }
}
