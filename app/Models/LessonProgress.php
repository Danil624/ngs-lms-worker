<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
   protected $fillable = [
    'lesson_id',
    'user_id',
    'status',
    'opened_at',
    'completed_at',
    'progress_percent',
];
}
