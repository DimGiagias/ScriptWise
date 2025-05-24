<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
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

final class CourseReviewController extends Controller
{
    public const REVIEW_QUIZ_WRONG_QUESTIONS_TARGET = 5; // Target number of previously wrong questions

    public const REVIEW_QUIZ_NEW_QUESTIONS_TARGET = 5;  // Target number of new/random questions

    /**
     * Generate and display the final review quiz for a course.
     */
    public function generate(Course $course): Response|RedirectResponse
    {
        $user = Auth::user();

        $placeholderQuiz = $course->finalReviewQuiz;
        if (! $placeholderQuiz) {
            Log::warning("CourseReview: Course {$course->id} has no final_review_quiz_id linked.");
            $quizTitle = "{$course->title} - Final Review";
            $quizDescription = "A comprehensive review of topics from {$course->title}.";
        } else {
            $quizTitle = $placeholderQuiz->title;
            $quizDescription = $placeholderQuiz->description;
        }

        $moduleIds = $course->modules()->pluck('id');
        $courseQuizIds = Quiz::whereIn('module_id', $moduleIds)->where('type', 'module')->pluck('id');

        $incorrectQuestionIds = QuizAnswer::join('quiz_attempts', 'quiz_answers.quiz_attempt_id', '=', 'quiz_attempts.id')
            ->where('quiz_attempts.user_id', $user->id)
            ->whereIn('quiz_attempts.quiz_id', $courseQuizIds)
            ->where('quiz_answers.is_correct', false)
            ->distinct()
            ->pluck('quiz_answers.question_id');

        $wrongQuestions = Question::whereIn('id', $incorrectQuestionIds)
            ->inRandomOrder()
            ->take(self::REVIEW_QUIZ_WRONG_QUESTIONS_TARGET)
            ->get();

        $allLessonIdsInCourse = $course->modules->pluck('lessons')->flatten()->pluck('id');
        $remainingQuestionsTarget = self::REVIEW_QUIZ_NEW_QUESTIONS_TARGET + (self::REVIEW_QUIZ_WRONG_QUESTIONS_TARGET - $wrongQuestions->count());

        $newQuestions = collect();
        if ($remainingQuestionsTarget > 0 && $allLessonIdsInCourse->isNotEmpty()) {
            $newQuestions = Question::whereIn('lesson_id', $allLessonIdsInCourse)
                ->whereNotIn('id', $wrongQuestions->pluck('id'))
                ->inRandomOrder()
                ->take($remainingQuestionsTarget)
                ->get();
        }

        $finalQuestions = $wrongQuestions->concat($newQuestions)->shuffle();

        if ($finalQuestions->isEmpty()) {
            return Inertia::render('Courses/Review/Show', [
                'course' => $course->only('id', 'title', 'slug'),
                'quizData' => null,
                'message' => 'Not enough questions available for a review quiz for this course yet. Try completing more module quizzes.',
            ]);
        }

        $displayQuestions = $finalQuestions->map(function ($q): array {
            $optionsArray = is_array($q->options) ? $q->options : (json_decode($q->options, true) ?? []);

            return [
                'id' => $q->id,
                'lesson_id' => $q->lesson_id,
                'type' => $q->type,
                'text' => $q->text,
                'options' => $optionsArray,
            ];
        });

        $quizDataForView = [
            'id' => 'final_review_'.$course->id.'_'.uniqid(),
            'title' => $quizTitle,
            'description' => $quizDescription,
            'questions' => $displayQuestions,
        ];

        session(['_courseReviewQuestions_'.$course->id => $finalQuestions->keyBy('id')->toArray()]);

        return Inertia::render('courses/review/Show', [
            'course' => $course->only('id', 'title', 'slug'),
            'quizData' => $quizDataForView,
            'message' => null,
        ]);
    }

    /**
     * Process and grade the submitted final review quiz.
     */
    public function submit(Request $request, Course $course): Response|RedirectResponse
    {
        $user = Auth::user();
        $submittedAnswers = $request->input('answers', []);
        $request->input('quizId');

        $request->validate(['answers' => 'required|array', 'quizId' => 'required|string']);

        $correctQuestionsData = session('_courseReviewQuestions_'.$course->id);
        session()->forget('_courseReviewQuestions_'.$course->id);

        if (! $correctQuestionsData) {
            return Redirect::route('course.review.generate', $course)
                ->with('error', 'Review quiz session expired. Please try again.');
        }
        $correctQuestions = collect($correctQuestionsData);

        $attempt = null;
        $resultsData = [];
        $correctCount = 0;
        $processedAnswersCount = 0;

        try {
            DB::transaction(function () use (
                $user, $course, $submittedAnswers, $correctQuestions,
                &$attempt, &$correctCount, &$processedAnswersCount, &$resultsData
            ): void {
                $attempt = QuizAttempt::create([
                    'user_id' => $user->id,
                    'quiz_id' => $course->final_review_quiz_id,
                    'type' => QuizAttempt::TYPE_FINAL_REVIEW,
                    'started_at' => now(),
                    'completed_at' => now(),
                ]);

                foreach ($submittedAnswers as $questionId => $userAnswer) {
                    if (! $correctQuestions->has($questionId)) {
                        continue;
                    }

                    $questionData = $correctQuestions->get($questionId);
                    $question = is_array($questionData) ? (object) $questionData : $questionData;

                    $optionsArray = (isset($question->options) && is_string($question->options))
                        ? (json_decode($question->options, true) ?? [])
                        : ($question->options ?? []);

                    $isCorrect = false;
                    $correctAnswer = $question->correct_answer ?? null;

                    // TODO: Refactor the grading logic to Service/Trait later
                    switch ($question->type ?? 'multiple_choice') {
                        case 'multiple_choice':
                        case 'fill_blank':
                        case 'true_false':
                            if (is_string($userAnswer) && is_string($correctAnswer)) {
                                $isCorrect = mb_strtolower(mb_trim($userAnswer)) === mb_strtolower(mb_trim($correctAnswer));
                            }
                            break;
                    }

                    if ($isCorrect) {
                        $correctCount++;
                    }
                    $processedAnswersCount++;

                    QuizAnswer::create([
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'user_answer' => $userAnswer,
                        'is_correct' => $isCorrect,
                    ]);

                    $resultsData[] = [
                        'question_id' => $question->id,
                        'question_type' => $question->type ?? 'multiple_choice',
                        'question_text' => $question->text ?? 'N/A',
                        'options' => $optionsArray,
                        'user_answer' => $userAnswer,
                        'correct_answer' => $correctAnswer ?? 'N/A',
                        'is_correct' => $isCorrect,
                        'explanation' => $question->explanation ?? '',
                        'lesson_id' => $question->lesson_id ?? null,
                    ];
                }

                $score = ($processedAnswersCount > 0) ? round(($correctCount / $processedAnswersCount) * 100) : 0;
                $attempt->score = $score;
                $attempt->save();
            });
        } catch (Exception $e) {
            Log::error('CourseReviewSubmit: Failed to save attempt.', ['error' => $e->getMessage()]);

            return Redirect::route('course.review.generate', $course)
                ->with('error', 'An error occurred saving your review attempt.');
        }

        $lessonsToReviewDeeply = [];
        foreach ($resultsData as $result) {
            if ($result['is_correct']) {
                continue;
            }
            if (! $result['lesson_id']) {
                continue;
            }
            if (isset($lessonsToReviewDeeply[$result['lesson_id']])) {
                continue;
            }
            $lessonModel = Lesson::with('externalResources')->find($result['lesson_id']);
            if ($lessonModel) {
                $lessonsToReviewDeeply[$result['lesson_id']] = [
                    'id' => $lessonModel->id,
                    'title' => $lessonModel->title,
                    'url' => route('lessons.show', ['course' => $course->slug, 'lesson' => $lessonModel->slug]),
                    'external_resources' => $lessonModel->externalResources->map(fn ($res) => $res->only('title', 'url', 'type', 'description'))->toArray(),
                ];
            }
        }

        return Inertia::render('quizzes/Result', [
            'quiz' => ['id' => $attempt->quiz_id, 'title' => $course->finalReviewQuiz?->title ?? "{$course->title} - Final Review"],
            'attempt' => $attempt,
            'results' => $resultsData,
            'reviewSuggestions' => [], // TODO: Legacy, not used here
            'deepReviewSuggestions' => array_values($lessonsToReviewDeeply),
        ]);
    }
}
