<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{

    const ADMIN = 1;
    const MEDICAL = 2;
    const INVENTORY = 3;
    
    protected $table = 'roles';

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
