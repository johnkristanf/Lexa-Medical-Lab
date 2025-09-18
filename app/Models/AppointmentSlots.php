<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentSlots extends Model
{
    const AVAIALBLE = 'available';
    const UNAVAIALBLE = 'unavailable';
    const CANCELLED = 'cancelled';

    protected $guarded = ['id'];


    public function appoinment_schedules()
    {
        return $this->belongsTo(AppointmentSchedule::class, 'schedule_id');
    }
}
