<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Appointments extends Model
{
    protected $guarded = ['id'];

    public function test_types(): BelongsToMany
    {
        return $this->belongsToMany(
            TestType::class,
            'appointment_test_type', // pivot table name
            'appointment_id',        // foreign key on pivot table for this model
            'type_id'           // foreign key on pivot table for related model
        );
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(AppointmentSchedule::class, 'schedule_id');
    }

    public function time_slot(): BelongsTo
    {
        return $this->belongsTo(AppointmentSlots::class, 'time_slot_id');
    }
}
