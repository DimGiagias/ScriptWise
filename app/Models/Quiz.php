<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\QuizFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Quiz extends Model
{
    /** @use HasFactory<QuizFactory> */
    use HasFactory;

    protected $fillable = [
        'module_id',
        'course_id',
        'title',
        'description',
        'order',
    ];

    // Quiz belongs to a Module (or Course)
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Quiz has many Questions
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    // Quiz has many Attempts by users
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Quiz covers many Lessons
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_quiz');
    }
}
