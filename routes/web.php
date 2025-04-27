<?php

declare(strict_types=1);

use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
