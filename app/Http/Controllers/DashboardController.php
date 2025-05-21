<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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

        $quizAttempts = $user->quizAttempts()
            ->with('quiz:id,title')
            ->latest('completed_at')
            ->take(10)
            ->get(['id', 'quiz_id', 'score', 'completed_at']);

        return Inertia::render('Dashboard', [
            'quizAttempts' => $quizAttempts,
        ]);
    }
}
