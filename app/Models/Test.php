<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
   protected $fillable = [
    'course_id',
    'title',
    'passing_score',
    'max_attempts',
    'time_limit',
];
public function questions()
    {
    return $this->hasMany(TestQuestion::class)->orderBy('sort');
    }

public function course()
    {
    return $this->belongsTo(Course::class);
    }

}
