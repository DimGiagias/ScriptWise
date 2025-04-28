<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * Use the 'slug' column for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Define the relationship: A Lesson belongs to one Module.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    // A Lesson can be covered by many Quizzes
    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(Quiz::class, 'lesson_quiz');
    }
}
