<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestType extends Model
{
    protected $guarded = ['id'];
    protected $table = 'test_types';

    public function test_category(): BelongsTo
    {
        return $this->belongsTo(TestCategory::class, 'test_category_id');
    }
}
