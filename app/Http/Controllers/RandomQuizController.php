<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

final class RandomQuizController extends Controller
{
    public const int DEFAULT_QUESTION_COUNT = 10;

    /**
     * Generate and display the random quiz page.
     */
    public function generate(Request $request): Response|RedirectResponse
    {
        $user = Auth::user();
        $questionCount = $request->input('count', self::DEFAULT_QUESTION_COUNT);

        $completedLessonIds = $user->progress()->pluck('lesson_id')->unique();

        if ($completedLessonIds->isEmpty()) {
            return Inertia::render('RandomQuiz/Show', [
                'quizData' => null,
                'message' => 'You need to complete some lessons before taking a review quiz!',
            ]);
        }

        $questions = Question::whereIn('lesson_id', $completedLessonIds)
            ->select(['id', 'quiz_id', 'lesson_id', 'type', 'text', 'options', 'correct_answer', 'explanation', 'order'])
            ->inRandomOrder()
            ->take($questionCount)
            ->get();

        if ($questions->isEmpty()) {
            return Inertia::render('RandomQuiz/Show', [
                'quizData' => null,
                'message' => 'No review questions available for the lessons you\'ve completed yet.',
            ]);
        }

        $displayQuestions = $questions->map(function ($question): array {
            $optionsArray = [];
            if (! empty($question->options)) {
                $optionsArray = is_array($question->options)
                    ? $question->options
                    : (json_decode($question->options, true) ?? []);
            }

            return [
                'id' => $question->id,
                'lesson_id' => $question->lesson_id,
                'type' => $question->type,
                'text' => $question->text,
                'options' => $optionsArray,
            ];
        });

        $quizData = [
            'id' => 'random_'.uniqid(),
            'title' => 'Random Review Quiz',
            'description' => 'Test your knowledge on lessons you\'ve completed.',
            'questions' => $displayQuestions,
            'allQuestionsData' => $questions->keyBy('id')->toArray(),
        ];

        session(['_randomQuizQuestions' => $quizData['allQuestionsData']]);

        return Inertia::render('RandomQuiz/Show', [
            'quizData' => $quizData,
            'message' => null,
        ]);
    }

    public function submit(Request $request): Response|RedirectResponse
    {
        $user = Auth::user();
        $submittedAnswers = $request->input('answers', []);

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        $submittedQuestionIds = array_keys($submittedAnswers);

        if ($submittedQuestionIds === []) {
            return Redirect::route('random-quiz.generate')
                ->with('error', 'No answers were submitted.');
        }

        $questions = Question::whereIn('id', $submittedQuestionIds)
            ->select(['id', 'lesson_id', 'options', 'correct_answer', 'explanation', 'text'])
            ->get()
            ->keyBy('id');

        $attempt = null;
        $resultsData = [];
        $correctCount = 0;
        $totalQuestions = $questions->count();

        try {
            DB::transaction(function () use (
                $user, $submittedAnswers, $questions, &$attempt,
                &$correctCount, &$totalQuestions, &$resultsData
            ): void {
                $attempt = QuizAttempt::create([
                    'user_id' => $user->id,
                    'quiz_id' => null,
                    'type' => QuizAttempt::TYPE_RANDOM,
                    'started_at' => now(),
                    'completed_at' => now(),
                ]);

                foreach ($submittedAnswers as $questionId => $userAnswer) {
                    if (! $questions->has($questionId)) {
                        Log::warning('RandomQuizSubmit: Grading skipped for unknown/missing question ID.', ['qId' => $questionId]);

                        continue;
                    }

                    $question = $questions->get($questionId); // Get the Eloquent model

                    $optionsArray = $question->options ?? []; // Rely on model cast

                    $isCorrect = false;
                    $correctAnswer = $question->correct_answer;

                    if (is_string($userAnswer) && is_string($correctAnswer)) {
                        $isCorrect = mb_strtolower(mb_trim($userAnswer)) === mb_strtolower(mb_trim($correctAnswer));
                    } elseif (! is_null($correctAnswer)) {
                        $isCorrect = $userAnswer === $correctAnswer;
                    }

                    if ($isCorrect) {
                        $correctCount++;
                    }

                    QuizAnswer::create([
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'user_answer' => $userAnswer,
                        'is_correct' => $isCorrect,
                    ]);

                    $resultsData[] = [
                        'question_id' => $question->id,
                        'question_text' => $question->text ?? 'N/A',
                        'options' => $optionsArray,
                        'user_answer' => $userAnswer,
                        'correct_answer' => $correctAnswer ?? 'N/A',
                        'is_correct' => $isCorrect,
                        'explanation' => $question->explanation ?? '',
                        'lesson_id' => $question->lesson_id,
                    ];
                }

                $processedAnswersCount = count($resultsData);
                $score = ($processedAnswersCount > 0) ? round(($correctCount / $processedAnswersCount) * 100) : 0;
                $attempt->score = $score;
                $attempt->save();

            });

        } catch (Exception $e) {
            Log::error('RandomQuizSubmit: Failed to save attempt.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return Redirect::route('random-quiz.generate')
                ->with('error', 'An error occurred while saving your quiz attempt. Please try again.');
        }

        if (! $attempt) {
            Log::error('RandomQuizSubmit: Attempt object null after transaction.');

            return Redirect::route('random-quiz.generate')
                ->with('error', 'Failed to create quiz attempt record.');
        }

        $lessonIdsToReview = collect($resultsData)->where('is_correct', false)->pluck('lesson_id')->filter()->unique();
        $reviewLessons = Lesson::whereIn('id', $lessonIdsToReview)
            ->with(['module.course'])
            ->select('id', 'title', 'slug', 'module_id')
            ->get();

        $reviewSuggestions = $reviewLessons->map(function ($lesson): ?array {
            $courseSlug = $lesson->module?->course?->slug;
            if (! $courseSlug) {
                return null;
            }

            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'url' => route('lessons.show', ['course' => $courseSlug, 'lesson' => $lesson->slug]),
            ];
        })->filter()->values();

        return Inertia::render('quizzes/Result', [
            'quiz' => ['id' => null, 'title' => 'Random Review Quiz'],
            'attempt' => $attempt->loadMissing('answers.question'),
            'results' => $resultsData,
            'reviewSuggestions' => $reviewSuggestions,
        ]);
    }
}
