<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'preferred_learning_style',
        'learning_path_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // A User has many Quiz Attempts
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // User has many progress records.
    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    /**
     * Get the total number of quizzes this user has attempted.
     */
    public function getTotalQuizzesAttempted(): int
    {
        return $this->quizAttempts()->count();
    }

    /**
     * Calculate the user's current learning streak.
     */
    public function getLearningStreak(): int
    {
        $completionDates = $this->progress()
            ->selectRaw('DISTINCT DATE(completed_at) as completion_date')
            ->orderBy('completion_date', 'desc')
            ->pluck('completion_date')
            ->map(fn ($date): Carbon => Carbon::parse($date));

        if ($completionDates->isEmpty()) {
            return 0;
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        if ($completionDates->first()->isSameDay($today)) {
            $streak = 1;
        } elseif ($completionDates->first()->isSameDay($yesterday)) {
            $streak = 1;
        } else {
            return 0;
        }

        $currentExpectedDate = $completionDates->first()->isSameDay($today) ? $yesterday : $yesterday->copy()->subDay();

        for ($i = 1; $i < $completionDates->count(); $i++) {
            $completedDate = $completionDates[$i];
            if ($completedDate->isSameDay($currentExpectedDate)) {
                $streak++;
                $currentExpectedDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    public function getQuizScoreDistribution(): array
    {
        $scores = $this->quizAttempts()->pluck('score')->filter(fn ($score): bool => ! is_null($score));

        $distribution = [
            'green' => 0,
            'yellow' => 0,
            'red' => 0,
        ];

        $greenThreshold = 80;
        $yellowThreshold = 50;

        foreach ($scores as $score) {
            if ($score >= $greenThreshold) {
                $distribution['green']++;
            } elseif ($score >= $yellowThreshold) {
                $distribution['yellow']++;
            } else {
                $distribution['red']++;
            }
        }

        return $distribution;
    }

    /**
     * Get lesson completion data for a contribution graph.
     * Returns data for the last year by default.
     * Format: [['YYYY-MM-DD', count], ['YYYY-MM-DD', count], ...]
     */
    public function getLessonContributionData(int $days = 365): array
    {
        $startDate = Carbon::today()->subDays($days - 1); // Go back $days-1 to include today
        $endDate = Carbon::today();

        $completionsByDate = $this->progress()
            ->selectRaw('DATE(completed_at) as completion_date, COUNT(*) as count')
            ->where('completed_at', '>=', $startDate->copy()->startOfDay())
            ->where('completed_at', '<=', $endDate->copy()->endOfDay())
            ->groupBy('completion_date')
            ->orderBy('completion_date', 'asc')
            ->get()
            ->keyBy(fn ($item): string => Carbon::parse($item->completion_date)->toDateString());

        $completionData = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->toDateString();
            $completionData[] = [
                'date' => $dateString,
                'count' => $completionsByDate->get($dateString)?->count ?? 0,
            ];
            $currentDate->addDay();
        }

        return $completionData;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferred_learning_style' => 'string',
        ];
    }
}
