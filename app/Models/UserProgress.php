<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgress extends Model
{
    use HasFactory;

    protected $table = 'user_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Progress entry belongs to a User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Progress entry belongs to a Lesson
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
