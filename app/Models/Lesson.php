<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Lesson extends Model
{
    /** @use HasFactory<LessonFactory> */
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'content',
        'order',
    ];

    //Add 'is_completed' attribute dynamically for a logged-in user
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

}
