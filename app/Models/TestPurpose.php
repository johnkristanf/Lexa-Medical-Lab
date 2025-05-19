<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestPurpose extends Model
{
    protected $guarded = ['id'];
    protected $table = 'test_purpose';

    public function test()
    {
        return $this->hasOne(Test::class, 'purpose_id');
    }
}
