<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestCategory extends Model
{
    protected $guarded = ['id'];

    protected $table = 'test_category';

    public function testTypes()
    {
        return $this->hasMany(TestType::class, 'test_category_id');
    }

    public function test()
    {
        return $this->hasMany(Test::class, 'category_id');
    }
}
