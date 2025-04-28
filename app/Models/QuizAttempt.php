<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\QuizAttemptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class QuizAttempt extends Model
{
    /** @use HasFactory<QuizAttemptFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Attempt belongs to one User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Attempt belongs to one Quiz
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    // Attempt has many recorded Answers
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
