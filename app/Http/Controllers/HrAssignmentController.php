<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseAssignment;
use Illuminate\Http\Request;

class HrAssignmentController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'deadline' => 'nullable|date',
        ]);

        CourseAssignment::updateOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => $request->user_id,
            ],
            [
                'assigned_at' => now(),
                'deadline' => $request->deadline,
                'status' => 'not_started',
                'progress' => 0,
            ]
        );

       return back()->with('success', 'Курс назначен сотруднику');
    }
}