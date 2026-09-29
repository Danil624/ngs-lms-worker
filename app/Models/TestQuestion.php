<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    protected $fillable = [
        'test_id',
        'question',
        'type',
        'sort',
    ];

    public function answers()
    {
        return $this->hasMany(TestAnswer::class);
    }
}