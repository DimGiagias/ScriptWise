<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

final class UserPreferenceController extends Controller
{
    /**
     * Update the authenticated user's learning preferences.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'preferred_learning_style' => [
                'nullable',
                'string',
                Rule::in(['reading', 'visual']),
            ],
            'learning_path_id' => [
                'nullable',
                'integer',
                Rule::exists('learning_paths', 'id')->where(function ($query) {
                    return $query->where('is_active', true);
                }),
            ],
        ]);

        $user->fill($validated);
        $user->save();

        return Redirect::route('dashboard')->with('success', 'Preferences updated successfully!');
    }
}
