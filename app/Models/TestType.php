<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TestType extends Model
{
    protected $guarded = ['id'];
    protected $table = 'test_types';

    public function test_category(): BelongsTo
    {
        return $this->belongsTo(TestCategory::class, 'test_category_id');
    }

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(
            Appointments::class,
            'appointment_test_type', // pivot table name
            'appointment_id',        // foreign key on pivot table for this model
            'type_id'           // foreign key on pivot table for related model
        );
    }


    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_test_type')
            ->withPivot(['test_id', 'results'])
            ->withTimestamps();
    }
}
