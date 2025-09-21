<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $guarded = ['id'];

    protected $table = 'patients';

    public function test()
    {
        return $this->hasOne(Test::class, 'patient_id');
    }

    public function test_types()
    {
        return $this->belongsToMany(TestType::class, 'patient_test_type')
            ->withPivot(['test_id', 'results'])
            ->withTimestamps();
    }
}
