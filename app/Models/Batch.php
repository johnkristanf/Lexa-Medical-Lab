<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Batch extends Model
{
    protected $guarded = ['id'];

    public function medicalSupply()
    {
        return $this->belongsTo(MedicalSupplies::class, 'medical_supply_id');
    }
}
