<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'is_published',
        'assessment_quiz_id',
        'final_review_quiz_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Use the 'slug' column for route model binding instead of the 'id'.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * A Course has many Modules.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function finalReviewQuiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'final_review_quiz_id');
    }
}
