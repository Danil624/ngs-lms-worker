<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\CourseAssignment;
use App\Models\ActivityLog;

class EmployeeLessonController extends Controller
{
    private function currentUser()
    {
    if (session('lms_user_id')) {
        return User::findOrFail(
            session('lms_user_id')
        );
    }    
    return User::where(
            'email',
            'ivan@test.local'
        )->firstOrFail();
    }

    public function show(
        CourseAssignment $assignment,
        Lesson $lesson
    ) {
        $user = $this->currentUser();

        abort_unless(
            $assignment->user_id === $user->id,
            403
        );

        abort_unless(
            $lesson->course_id === $assignment->course_id,
            404
        );

        $progress = LessonProgress::firstOrCreate(
            [
                'lesson_id' => $lesson->id,
                'user_id' => $user->id,
            ],
            [
                'status' => 'in_progress',
                'opened_at' => now(),
                'progress_percent' => 0,
            ]
        );

        if (!$progress->opened_at) {
            $progress->update([
                'opened_at' => now(),
                'status' => 'in_progress',
            ]);
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'course_id' => $assignment->course_id,
            'lesson_id' => $lesson->id,
            'event' => 'LESSON_OPENED',
            'data' => null,
        ]);

        return view('employee.lesson', compact(
            'assignment',
            'lesson',
            'progress'
        ));
    }

    public function complete(
        CourseAssignment $assignment,
        Lesson $lesson
    ) {
        $user = $this->currentUser();

        abort_unless(
            $assignment->user_id === $user->id,
            403
        );

        abort_unless(
            $lesson->course_id === $assignment->course_id,
            404
        );

        LessonProgress::updateOrCreate(
            [
                'lesson_id' => $lesson->id,
                'user_id' => $user->id,
            ],
            [
                'status' => 'completed',
                'opened_at' => now(),
                'completed_at' => now(),
                'progress_percent' => 100,
            ]
        );

        ActivityLog::create([
            'user_id' => $user->id,
            'course_id' => $assignment->course_id,
            'lesson_id' => $lesson->id,
            'event' => 'LESSON_COMPLETED',
            'data' => null,
        ]);

        $assignment->load('course.lessons');

        $lessonIds = $assignment
            ->course
            ->lessons
            ->pluck('id');

        $totalLessons = $lessonIds->count();

        $completedLessons = LessonProgress::where(
                'user_id',
                $user->id
            )
            ->whereIn('lesson_id', $lessonIds)
            ->where('status', 'completed')
            ->count();

        $percent = $totalLessons > 0
            ? round(
                ($completedLessons / $totalLessons) * 100
            )
            : 0;

        $assignment->update([
            'progress' => $percent,
            'status' => 'in_progress',
        ]);

        return redirect()
            ->route('my.courses.show', $assignment)
            ->with('success', 'Урок завершён');
    }
}

