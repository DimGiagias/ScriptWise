<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use Inertia\Inertia;
use Inertia\Response;

final class CourseController extends Controller
{
    /**
     * Display a list of published courses.
     */
    public function index(): Response
    {
        $courses = Course::where('is_published', true)
            ->select('id', 'title', 'slug', 'description')
            ->get();

        return Inertia::render('courses/Index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Display a single course with its modules and lessons.
     */
    public function show(Course $course): Response
    {
        if (! $course->is_published) {
            abort(404);
        }

        $course->load([
            'modules.lessons' => function ($query): void {
                $query->select('id', 'module_id', 'title', 'slug', 'order');
            }, 'modules.quizzes' => function ($query): void {
                $query->select('id', 'module_id', 'title', 'description', 'order')
                    ->orderBy('order');
            },
            'modules' => function ($query): void {
                $query->select('id', 'course_id', 'title', 'order');
            }]);

        return Inertia::render('courses/Show', [
            'course' => $course,
        ]);
    }
}
