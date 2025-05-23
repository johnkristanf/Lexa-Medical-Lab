<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $guarded = ['id'];
    protected $table = 'test';

    public function test_request()
    {
        return $this->belongsTo(TestRequest::class, 'request_id');
    }

    public function test_purpose()
    {

        return $this->belongsTo(TestPurpose::class, 'purpose_id');
    }

    public function test_category()
    {
        return $this->belongsTo(TestCategory::class, 'category_id');
    }

    public function patients()
    {
        return $this->belongsTo(Patient::class, 'category_id');
    }
}
