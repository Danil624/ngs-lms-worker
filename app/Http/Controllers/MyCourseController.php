<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CourseAssignment;
use App\Models\LessonProgress;
use App\Models\ActivityLog;

class MyCourseController extends Controller
{
    private function currentUser()
    {
        // Пока используем тестового Ивана.
        // Потом здесь будет текущий пользователь Bitrix24.
        return User::where('email', 'ivan@test.local')->firstOrFail();
    }

    public function index()
    {
        $user = $this->currentUser();

        $assignments = $user->courseAssignments()
            ->with('course')
            ->latest('assigned_at')
            ->get();

        return view('employee.courses', compact(
            'user',
            'assignments'
        ));
    }

    public function show(CourseAssignment $assignment)
    {
        $user = $this->currentUser();

        abort_unless(
            $assignment->user_id === $user->id,
            403
        );

        // При первом открытии курс считается начатым
        if ($assignment->status === 'not_started') {
            $assignment->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

       $assignment->load([
    'course.lessons',
    'course.test.questions.answers'
]);

        $lessonProgress = LessonProgress::where(
                'user_id',
                $user->id
            )
            ->whereIn(
                'lesson_id',
                $assignment->course->lessons->pluck('id')
            )
            ->get()
            ->keyBy('lesson_id');

        ActivityLog::create([
            'user_id' => $user->id,
            'course_id' => $assignment->course_id,
            'lesson_id' => null,
            'event' => 'COURSE_OPENED',
            'data' => null,
        ]);

        return view('employee.course', compact(
            'user',
            'assignment',
            'lessonProgress'
        ));
    }
}