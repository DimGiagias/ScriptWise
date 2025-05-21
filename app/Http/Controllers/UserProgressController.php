<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\UserProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class UserProgressController extends Controller
{
    /**
     * Mark the given lesson as completed for the authenticated user.
     * Route model binding automatically finds the Lesson by its ID (default key).
     */
    public function store(Request $request, Lesson $lesson): RedirectResponse
    {
        $user = Auth::user();

        UserProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'completed_at' => now(),
            ]
        );

        return Redirect::back()->with('success', 'Lesson marked as complete!');
    }
}
