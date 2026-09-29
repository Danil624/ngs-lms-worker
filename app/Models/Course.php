<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function lessons()
{
    return $this->hasMany(Lesson::class)->orderBy('sort');
}
public function test()
{
    return $this->hasOne(Test::class);
}
public function assignments()
{
    return $this->hasMany(CourseAssignment::class);
}
}
