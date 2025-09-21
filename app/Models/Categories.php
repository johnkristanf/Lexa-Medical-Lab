<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $guarded = ['id'];

    protected $table = 'categories';

    public function medical_supplies()
    {
        return $this->hasmany(MedicalSupplies::class, 'category_id');
    }
}
