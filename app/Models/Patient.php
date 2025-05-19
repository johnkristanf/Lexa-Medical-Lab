<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected  $guarded = ['id'];
    protected  $table = 'patients';

    public function test()
    {

        return $this->hasOne(Test::class, 'patient_id');
    }
}
