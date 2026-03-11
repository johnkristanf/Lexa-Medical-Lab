<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSelectedCategory extends Model
{
    protected $table = 'test_selected_categories';
    
    protected $fillable = [
        'test_id',
        'category_id'
    ];

    public function test_category()
    {
        return $this->belongsTo(TestCategory::class, 'category_id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id');
    }
}
