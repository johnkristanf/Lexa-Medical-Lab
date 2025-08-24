<?php

namespace App\Models;

use App\Models\MedicalSupplies;
use Illuminate\Database\Eloquent\Model;
use App\Models\Batch;



class archive_supplies extends Model
{
    protected $fillable = [
        'medical_supplies_id',
        'batch_id',
    ];
    protected $table = 'archive_supplies';

    public function medical_supply()
    {
        return $this->belongsTo(MedicalSupplies::class, 'medical_supplies_id')->withTrashed();
    }

    public function batches()
    {
        return $this->belongsTo(Batch::class, 'batch_id')->withTrashed();
    }
}
