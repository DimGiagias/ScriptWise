<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

final class Lesson extends Model
{
    /** @use HasFactory<LessonFactory> */
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'content',
        'video_embed_html',
        'assignment',
        'initial_code',
        'expected_output',
        'order',
    ];

    // Add 'is_completed' attribute dynamically for a logged-in user
    protected $appends = ['is_completed'];

    /**
     * Use the 'slug' column for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // A Lesson belongs to one Module.
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    // A Lesson can be covered by many Quizzes
    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(Quiz::class, 'lesson_quiz');
    }

    // A Lesson has many progress records
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * Accessor: Dynamically check if the *currently authenticated* user
     * has completed this lesson.
     */
    public function getIsCompletedAttribute(): bool
    {
        // If no user is logged in, it's not completed by them
        if (! Auth::check()) {
            return false;
        }

        // Check if a progress record exists for the logged-in user and this lesson
        return $this->userProgress()->where('user_id', Auth::id())->exists();
    }

    // A Lesson has many external resources.
    public function externalResources(): HasMany
    {
        return $this->hasMany(ExternalResource::class);
    }
}
