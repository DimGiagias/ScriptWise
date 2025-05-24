<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class UserStatsController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            // This is handled by auth middleware, but as a fallback check
            return redirect()->route('login');
        }

        $totalQuizzesAttempted = $user->getTotalQuizzesAttempted();
        $learningStreak = $user->getLearningStreak();
        $quizScoreDistribution = $user->getQuizScoreDistribution();
        $lessonContributionData = $user->getLessonContributionData();

        return Inertia::render('stats/Show', [
            'totalQuizzesAttempted' => $totalQuizzesAttempted,
            'learningStreak' => $learningStreak,
            'quizScoreDistribution' => $quizScoreDistribution,
            'lessonContributionData' => $lessonContributionData,
        ]);
    }
}
