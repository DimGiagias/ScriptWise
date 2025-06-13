<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LearningPath;
use Exception;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        if (! $user) {
            return Inertia::render('Dashboard', ['quizAttempts' => []]);
        }

        $learningPaths = LearningPath::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'description']);

        $nextSuggestedCourse = null;
        if ($user->learning_path_id && $user->learningPath) {
            $pathCourses = $user->learningPath->courses;

            foreach ($pathCourses as $pathCourse) {
                $allLessonsInCourse = $pathCourse->modules->pluck('lessons')->flatten();
                $completedLessonsInCourse = $user->progress()
                    ->whereIn('lesson_id', $allLessonsInCourse->pluck('id'))
                    ->count();

                if ($completedLessonsInCourse < $allLessonsInCourse->count() || $allLessonsInCourse->isEmpty()) {
                    $nextSuggestedCourse = $pathCourse;
                    break;
                }
            }
        }

        try {
            $quizAttempts = $user->quizAttempts()
                ->with('quiz:id,title')
                ->latest('completed_at')
                ->take(10)
                ->get(['id', 'quiz_id', 'score', 'completed_at']);
        } catch (Exception) {
            $quizAttempts = collect();
        }

        return Inertia::render('Dashboard', [
            'quizAttempts' => $quizAttempts,
            'learningPaths' => $learningPaths,
            'nextSuggestedCourse' => $nextSuggestedCourse ? $nextSuggestedCourse->only('id', 'title', 'slug', 'description') : null,
        ]);
    }
}
