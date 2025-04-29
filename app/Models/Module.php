<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ModuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Module extends Model
{
    /** @use HasFactory<ModuleFactory> */
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'order',
    ];

    /**
     * Define the relationship: A Module belongs to one Course.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Define the relationship: A Module has many Lessons.
     * Orders lessons by the 'order' column.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    // A Module can have many Quizzes
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->orderBy('order');
    }
}
