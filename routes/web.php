<?php

declare(strict_types=1);

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserProgressController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');

    Route::post('/lessons/{lesson:id}/complete', [UserProgressController::class, 'store'])->name('lessons.complete');

});

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
