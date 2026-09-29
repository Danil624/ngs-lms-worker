<?php

namespace App\Http\Controllers;

use App\Models\CourseAssignment;
use App\Models\TestAttempt;

class HrResultsController extends Controller
{
    public function index()
    {
        $assignments = CourseAssignment::with([
            'user',
            'course.test'
        ])
        ->latest('assigned_at')
        ->get();

        foreach ($assignments as $assignment) {

            $assignment->test_attempts_count = 0;
            $assignment->last_test_attempt = null;

            $test = $assignment->course->test;

            if ($test) {

                $attempts = TestAttempt::where(
                    'test_id',
                    $test->id
                )
                ->where(
                    'user_id',
                    $assignment->user_id
                )
                ->orderByDesc('attempt_number')
                ->get();

                $assignment->test_attempts_count =
                    $attempts->count();

                $assignment->last_test_attempt =
                    $attempts->first();
            }
        }

        return view(
            'hr.results',
            compact('assignments')
        );
    }
}