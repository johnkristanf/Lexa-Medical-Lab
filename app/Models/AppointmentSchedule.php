<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppointmentSchedule extends Model
{
    protected $guarded = ['id'];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointments::class, 'schedule_id');
    }
}
