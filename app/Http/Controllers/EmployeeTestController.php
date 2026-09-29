<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CourseAssignment;
use App\Models\TestAttempt;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class EmployeeTestController extends Controller
{
    private function currentUser()
    {
        return User::where(
            'email',
            'ivan@test.local'
        )->firstOrFail();
    }

    public function show(CourseAssignment $assignment)
    {
        $user = $this->currentUser();

        abort_unless(
            $assignment->user_id === $user->id,
            403
        );

        $assignment->load(
            'course.test.questions.answers'
        );

        $test = $assignment->course->test;

        abort_unless($test, 404);

        $attemptsCount = TestAttempt::where(
                'test_id',
                $test->id
            )
            ->where('user_id', $user->id)
            ->count();

        if ($attemptsCount >= $test->max_attempts) {
            return redirect()
                ->route('my.courses.show', $assignment)
                ->with(
                    'error',
                    'Попытки закончились'
                );
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'course_id' => $assignment->course_id,
            'lesson_id' => null,
            'event' => 'TEST_STARTED',
            'data' => [
                'test_id' => $test->id,
            ],
        ]);

        return view(
            'employee.test',
            compact(
                'assignment',
                'test',
                'attemptsCount'
            )
        );
    }

    public function submit(
        Request $request,
        CourseAssignment $assignment
    ) {
        $user = $this->currentUser();

        abort_unless(
            $assignment->user_id === $user->id,
            403
        );

        $assignment->load(
            'course.test.questions.answers'
        );

        $test = $assignment->course->test;

        abort_unless($test, 404);

        $attemptsCount = TestAttempt::where(
                'test_id',
                $test->id
            )
            ->where('user_id', $user->id)
            ->count();

        if ($attemptsCount >= $test->max_attempts) {
            return redirect()
                ->route('my.courses.show', $assignment)
                ->with(
                    'error',
                    'Попытки закончились'
                );
        }

        $questions = $test->questions;

        $correct = 0;

        foreach ($questions as $question) {

            $selectedAnswerId =
                $request->input(
                    'answers.' . $question->id
                );

            $correctAnswer =
                $question->answers
                    ->firstWhere(
                        'is_correct',
                        true
                    );

            if (
                $correctAnswer &&
                (int)$selectedAnswerId
                    === (int)$correctAnswer->id
            ) {
                $correct++;
            }
        }

        $total = $questions->count();

        $percent = $total > 0
            ? round(($correct / $total) * 100)
            : 0;

        $passed =
            $percent >= $test->passing_score;

        $attempt = TestAttempt::create([
            'test_id' => $test->id,
            'user_id' => $user->id,
            'attempt_number' =>
                $attemptsCount + 1,
            'score' => $correct,
            'percent' => $percent,
            'passed' => $passed,
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'course_id' => $assignment->course_id,
            'lesson_id' => null,
            'event' => 'TEST_FINISHED',
            'data' => [
                'test_id' => $test->id,
                'attempt' =>
                    $attempt->attempt_number,
                'percent' => $percent,
                'passed' => $passed,
            ],
        ]);

        if ($passed) {

            $assignment->update([
                'status' => 'completed',
                'progress' => 100,
                'completed_at' => now(),
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'course_id' =>
                    $assignment->course_id,
                'lesson_id' => null,
                'event' => 'COURSE_COMPLETED',
                'data' => null,
            ]);
        }

        return redirect()
            ->route(
                'my.courses.show',
                $assignment
            )
            ->with(
                'test_result',
                [
                    'correct' => $correct,
                    'total' => $total,
                    'percent' => $percent,
                    'passed' => $passed,
                    'attempt' =>
                        $attempt->attempt_number,
                ]
            );
    }
}