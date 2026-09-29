<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseAssignment extends Model
{
    protected $fillable = [
    'course_id',
    'user_id',
    'assigned_at',
    'deadline',
    'status',
    'progress',
    'started_at',
    'completed_at',
];

public function user()
{
    return $this->belongsTo(User::class);
}
public function course()
{
    return $this->belongsTo(Course::class);
}
}
