<?php

declare(strict_types=1);

use App\Http\Controllers\CourseAssessmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\RandomQuizController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\UserProgressController;
use App\Http\Controllers\UserStatsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');

    Route::post('/lessons/{lesson:id}/complete', [UserProgressController::class, 'store'])->name('lessons.complete');

    Route::get('/random-quiz', [RandomQuizController::class, 'generate'])->name('random-quiz.generate');
    Route::post('/random-quiz/submit', [RandomQuizController::class, 'submit'])->name('random-quiz.submit');

    Route::get('/courses/{course}/assessment', [CourseAssessmentController::class, 'show'])->name('course.assessment.show');
    Route::post('/courses/{course}/assessment/submit', [CourseAssessmentController::class, 'submit'])->name('course.assessment.submit');
    Route::get('/courses/{course}/review', [CourseReviewController::class, 'generate'])->name('course.review.generate');
    Route::post('/courses/{course}/review/submit', [CourseReviewController::class, 'submit'])->name('course.review.submit');

    Route::patch('/user/preferences', [UserPreferenceController::class, 'update'])->name('user.preferences.update');

    Route::get('/my-stats', [UserStatsController::class, 'show'])->name('my-stats.show');
});

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
