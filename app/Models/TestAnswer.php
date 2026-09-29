<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAnswer extends Model
{
    protected $fillable = [
    'test_question_id',
    'answer',
    'is_correct',
];
}
