<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class HrLessonController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|max:255',
            'type' => 'required',
            'content' => 'nullable',
        ]);

        Lesson::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'type' => $request->type,
            'content' => $request->content,
            'sort' => 100,
            'required' => true,
        ]);

        return back();
    }
}