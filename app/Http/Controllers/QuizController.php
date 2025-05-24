<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

final class QuizController extends Controller
{
    /**
     * Display the quiz questions page.
     */
    public function show(Quiz $quiz): Response
    {
        $quiz->load(['questions' => function ($query): void {
            $query->select(['id', 'quiz_id', 'lesson_id', 'type', 'text', 'options', 'order'])
                ->orderBy('order');
        }]);

        $processedQuestions = $quiz->questions->map(function ($question): array {
            if ($question->options !== null) {
                $optionsArray = is_array($question->options) ? $question->options : json_decode($question->options, true);
            } else {
                $optionsArray = [];
            }

            return [
                'id' => $question->id,
                'quiz_id' => $question->quiz_id,
                'lesson_id' => $question->lesson_id,
                'type' => $question->type,
                'text' => $question->text,
                'options' => $optionsArray ?? [],
                'order' => $question->order,
            ];
        });

        $quizData = $quiz->only('id', 'title', 'description', 'type');
        $quizData['questions'] = $processedQuestions;

        Log::info('Processed Quiz Data:', $quizData);

        return Inertia::render('quizzes/Show', [
            'quiz' => $quizData,
        ]);
    }

    /**
     * Process submitted quiz answers, grade, and show results.
     */
    public function submit(Request $request, Quiz $quiz): Response
    {
        $user = Auth::user();
        $submittedAnswers = $request->input('answers', []);

        $request->validate([
            'answers' => 'required|array',
        ]);

        $questions = $quiz->questions()->get()->keyBy('id');

        $attempt = null;
        $correctCount = 0;
        $totalQuestions = $questions->count();
        $resultsData = [];
        $lessonIdsToReview = [];

        DB::transaction(function () use (
            $user, $quiz, $submittedAnswers, $questions, &$attempt,
            &$correctCount, &$totalQuestions, &$resultsData, &$lessonIdsToReview
        ): void {
            $attempt = QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'started_at' => now(),
                'completed_at' => now(),
            ]);

            foreach ($submittedAnswers as $questionId => $userAnswer) {
                if (! $questions->has($questionId)) {
                    continue;
                }

                $question = $questions->get($questionId);
                $isCorrect = false;
                $correctAnswer = $question->correct_answer;

                switch ($question->type) {
                    case 'multiple_choice':
                        if (is_string($userAnswer) && is_string($correctAnswer)) {
                            $isCorrect = mb_strtolower(mb_trim($userAnswer)) === mb_strtolower(mb_trim($correctAnswer));
                        } else {
                            $isCorrect = $userAnswer === $correctAnswer;
                        }
                        break;
                    case 'true_false':
                        if (is_string($userAnswer) && is_string($correctAnswer)) {
                            $isCorrect = mb_strtolower(mb_trim($userAnswer)) === mb_strtolower($correctAnswer);
                        }
                        break;
                    case 'fill_blank':
                        if (is_string($userAnswer) && is_string($correctAnswer)) {
                            $isCorrect = mb_strtolower(mb_trim($userAnswer)) === mb_strtolower(mb_trim($correctAnswer));
                        }
                        break;
                    default:
                        Log::warning("Grading logic not implemented for question type: {$question->type}");
                        break;
                }

                QuizAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'user_answer' => $userAnswer,
                    'is_correct' => $isCorrect,
                ]);

                if ($isCorrect) {
                    $correctCount++;
                } elseif ($question->lesson_id) {
                    $lessonIdsToReview[$question->lesson_id] = true;
                }

                $resultsData[] = [
                    'question_id' => $question->id,
                    'question_type' => $question->type,
                    'question_text' => $question->text,
                    'options' => $question->options,
                    'user_answer' => $userAnswer,
                    'correct_answer' => $question->correct_answer,
                    'is_correct' => $isCorrect,
                    'explanation' => $question->explanation,
                    'lesson_id' => $question->lesson_id,
                ];
            }

            $score = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100) : 0;
            $attempt->score = $score;
            $attempt->save();

        });

        $reviewLessons = Lesson::whereIn('id', array_keys($lessonIdsToReview))
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
            'quiz' => $quiz->only('id', 'title'),
            'attempt' => $attempt->only('id', 'score', 'completed_at'),
            'results' => $resultsData,
            'reviewSuggestions' => $reviewSuggestions,
        ]);
    }
}
