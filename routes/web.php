<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HrCourseController;
use App\Http\Controllers\HrLessonController;
use App\Http\Controllers\HrTestController;
use App\Http\Controllers\HrTestQuestionController;
use App\Http\Controllers\HrAssignmentController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\EmployeeLessonController;
use App\Http\Controllers\EmployeeTestController;
use App\Http\Controllers\HrResultsController;
use App\Http\Controllers\HrEmployeeController;

Route::get('/hr/results', [HrResultsController::class, 'index'])
    ->name('hr.results');
Route::get('/my-courses', [MyCourseController::class, 'index'])
    ->name('my.courses');
Route::post('/hr/courses/{course}/assignments', [HrAssignmentController::class, 'store'])
    ->name('hr.assignments.store');
Route::post('/hr/tests/{test}/questions', [HrTestQuestionController::class, 'store'])
    ->name('hr.questions.store');
Route::post('/hr/courses/{course}/test', [HrTestController::class, 'store'])
    ->name('hr.tests.store');
Route::get('/', function () { return view('home');});
Route::post('/hr/courses/{course}/lessons', [HrLessonController::class, 'store'])
    ->name('hr.lessons.store');
Route::get('/hr', [HrCourseController::class, 'index'])->name('hr.index');
Route::get('/my-courses/{assignment}', [MyCourseController::class, 'show'])
    ->name('my.courses.show');
Route::post('/hr/courses', [HrCourseController::class, 'store'])
    ->name('hr.courses.store');
Route::get('/hr/courses/{course}', [HrCourseController::class, 'show'])
    ->name('hr.courses.show');
Route::get(
    '/my-courses/{assignment}/lessons/{lesson}',
    [EmployeeLessonController::class, 'show']
)->name('my.lessons.show');
Route::post(
    '/my-courses/{assignment}/lessons/{lesson}/complete',
    [EmployeeLessonController::class, 'complete']
)->name('my.lessons.complete');
Route::get(
    '/my-courses/{assignment}/test',
    [EmployeeTestController::class, 'show']
)->name('my.test.show');

Route::post(
    '/my-courses/{assignment}/test',
    [EmployeeTestController::class, 'submit']
)->name('my.test.submit');
Route::get('/hr/employees/{user}', [HrEmployeeController::class, 'show'])
    ->name('hr.employee.show');