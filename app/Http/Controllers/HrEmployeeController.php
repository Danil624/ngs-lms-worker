<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\TestAttempt;

class HrEmployeeController extends Controller
{
    public function show(User $user)
    {
        $assignments = $user->courseAssignments()
            ->with('course')
            ->latest('assigned_at')
            ->get();

        $attempts = TestAttempt::with('test.course')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $logs = ActivityLog::with([
            'course',
            'lesson'
        ])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('hr.employee', compact(
            'user',
            'assignments',
            'attempts',
            'logs'
        ));
    }
}