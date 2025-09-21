<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $table = 'stocks';

    public function medicalSupply()
    {
        return $this->belongsTo(MedicalSupplies::class, 'medical_supply_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
}
