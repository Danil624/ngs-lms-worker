<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Test;
use Illuminate\Http\Request;

class HrTestController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|max:255',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1',
        ]);

        Test::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'passing_score' => $request->passing_score,
            'max_attempts' => $request->max_attempts,
        ]);

        return back();
    }
}