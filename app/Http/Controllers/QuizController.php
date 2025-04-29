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
        $quiz->load(['questions' => function ($query) {
            $query->select(['id', 'quiz_id', 'lesson_id', 'type', 'text', 'options', 'order'])
                ->orderBy('order');
        }]);

        $processedQuestions = $quiz->questions->map(function ($question) {
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
        // Expecting answers in format: { 'question_id': 'selected_option_id', ... }
        $submittedAnswers = $request->input('answers', []);

        $request->validate([
            'answers' => 'required|array',
        ]);

        // --- Grading Logic ---
        // Fetch the actual questions with answers for grading
        $questions = $quiz->questions()->get()->keyBy('id');

        $attempt = null;
        $correctCount = 0;
        $totalQuestions = $questions->count();
        $resultsData = []; // Store detailed results for the frontend view
        $lessonIdsToReview = []; // Collect IDs of lessons linked to incorrect answers

        // Use a transaction to ensure attempt and answers are saved together
        DB::transaction(function () use (
            $user, $quiz, $submittedAnswers, $questions, &$attempt,
            &$correctCount, &$totalQuestions, &$resultsData, &$lessonIdsToReview
        ) {
            // 1. Create the QuizAttempt
            $attempt = QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'started_at' => now(), // Improve this later if start time matters
                'completed_at' => now(),
                // Score calculated and saved at the end
            ]);

            // 2. Iterate through submitted answers, grade, and save QuizAnswer
            foreach ($submittedAnswers as $questionId => $userAnswer) {
                // Ensure the submitted question actually belongs to this quiz
                if (! $questions->has($questionId)) {
                    continue; // Skip if invalid question ID submitted
                }

                $question = $questions->get($questionId);
                $isCorrect = false;

                // Perform grading (case-insensitive comparison for strings)
                if (is_string($userAnswer) && is_string($question->correct_answer)) {
                    $isCorrect = mb_strtolower(mb_trim($userAnswer)) === mb_strtolower(mb_trim($question->correct_answer));
                } else {
                    // Fallback for non-string comparison (might need adjustment for other types)
                    $isCorrect = $userAnswer === $question->correct_answer;
                }

                // Create the answer record
                QuizAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'user_answer' => $userAnswer,
                    'is_correct' => $isCorrect,
                ]);

                // Update counts and prepare data for results view
                if ($isCorrect) {
                    $correctCount++;
                } elseif ($question->lesson_id) {
                    // If incorrect AND linked to a lesson, mark lesson for review
                    $lessonIdsToReview[$question->lesson_id] = true; // Use as key for uniqueness
                }

                // Add detailed info for the results page
                $resultsData[] = [
                    'question_id' => $question->id,
                    'question_text' => $question->text,
                    'options' => $question->options, // Send options back too
                    'user_answer' => $userAnswer,
                    'correct_answer' => $question->correct_answer,
                    'is_correct' => $isCorrect,
                    'explanation' => $question->explanation,
                    'lesson_id' => $question->lesson_id,
                ];
            } // End foreach answer

            // 3. Calculate score and update the attempt record
            $score = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100) : 0;
            $attempt->score = $score;
            $attempt->save();

        }); // End transaction

        // 4. Fetch Lesson details for review suggestions
        $reviewLessons = Lesson::whereIn('id', array_keys($lessonIdsToReview))
            ->with(['module.course']) // Eager load for generating URLs
            ->select('id', 'title', 'slug', 'module_id') // Select necessary fields
            ->get();

        // Format review suggestions with URLs
        $reviewSuggestions = $reviewLessons->map(function ($lesson) {
            // Need course slug for the lesson URL
            $courseSlug = $lesson->module?->course?->slug; // Get slug via relationships
            if (! $courseSlug) {
                return null;
            }

            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                // Ensure your route name and parameters match 'routes/web.php'
                'url' => route('lessons.show', ['course' => $courseSlug, 'lesson' => $lesson->slug]),
            ];
        })->filter()->values(); // Remove nulls and re-index array

        // 5. Render the Results view via Inertia
        return Inertia::render('quizzes/Result', [
            'quiz' => $quiz->only('id', 'title'),
            'attempt' => $attempt->only('id', 'score', 'completed_at'), // Pass attempt details
            'results' => $resultsData, // Pass detailed answer results
            'reviewSuggestions' => $reviewSuggestions, // Pass formatted review links
        ]);
    }
}
