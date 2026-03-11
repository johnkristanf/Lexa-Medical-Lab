<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $guarded = ['id'];

    protected $table = 'test';

    public function test_purpose()
    {

        return $this->belongsTo(TestPurpose::class, 'purpose_id');
    }

    public function test_category()
    {
        return $this->belongsTo(TestCategory::class, 'category_id');
    }

    public function selected_categories()
    {
        return $this->hasMany(TestSelectedCategory::class, 'test_id');
    }

    public function patients()
    {
        return $this->belongsTo(Patient::class, 'category_id');
    }

    public function test_types()
    {
        return $this->belongsToMany(TestType::class, 'patient_test_type')
            ->withPivot(['patient_id', 'results'])
            ->withTimestamps();
    }

    public function patient_test()
    {
        return $this->belongsToMany(Patient::class, 'patient_test_type')
            ->withPivot(['test_type_id', 'results'])
            ->withTimestamps();
    }
}
