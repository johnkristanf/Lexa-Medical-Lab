<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class Batch extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'batches';

    public function medicalSupply()
    {
        return $this->belongsTo(MedicalSupplies::class, 'medical_supply_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'batch_id'); // uses batch_id
    }
}
