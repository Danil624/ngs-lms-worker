<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;

class HrCourseController extends Controller
{
    public function index()
    {
        $courses = Course::latest()->get();

        return view('hr.index', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
        ]);

        $course = new Course();
        $course->title = $request->title;
        $course->description = $request->description;
        $course->status = 'draft';
        $course->passing_score = 80;
        $course->max_attempts = 3;
        $course->save();

        return redirect()->route('hr.index');
    }

 public function show(Course $course)
{
    $course->load([
        'lessons',
        'test.questions.answers',
        'assignments.user'
    ]);

    $users = User::where('role', 'employee')
        ->orderBy('name')
        ->get();

    return view('hr.course', compact('course', 'users'));
}
}