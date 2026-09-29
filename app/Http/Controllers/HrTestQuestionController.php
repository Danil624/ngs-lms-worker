<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestAnswer;
use Illuminate\Http\Request;

class HrTestQuestionController extends Controller
{
   public function store(Request $request, Test $test)
{
    $request->validate([
        'question' => 'required|string',
        'answers' => 'required|array|min:2',
        'answers.*' => 'required|string',
        'correct_answer' => 'required|integer',
    ]);

    $question = TestQuestion::create([
        'test_id' => $test->id,
        'question' => $request->question,
        'type' => 'single',
        'sort' => 100,
    ]);

    foreach ($request->answers as $index => $answer) {
        TestAnswer::create([
            'test_question_id' => $question->id,
            'answer' => $answer,
            'is_correct' => $index == $request->correct_answer,
        ]);
    }

    return back();
}
}