<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Inertia\Inertia;
use Inertia\Response;

final class LessonController extends Controller
{
    /**
     * Display a single lesson.
     */
    public function show(Course $course, Lesson $lesson): Response
    {
        if ($lesson->module->course_id !== $course->id) {
            abort(404);
        }

        // Ensure the parent course is published
        if (! $course->is_published) {
            abort(404);
        }

        return Inertia::render('lessons/Show', [
            'lesson' => $lesson->only(
                'id', 'title', 'slug', 'content'
            ),
            'course' => $course->only('id', 'title', 'slug'),
        ]);
    }
}
