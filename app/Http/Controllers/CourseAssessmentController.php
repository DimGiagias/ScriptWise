<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

final class CourseAssessmentController extends Controller
{
    /**
     * Display the pre-course assessment quiz.
     */
    public function show(Course $course): Response|RedirectResponse
    {
        // Ensure the course has an assessment quiz linked
        if (! $course->assessment_quiz_id) {
            Log::warning('Attempted assessment for course without one.', ['course_id' => $course->id]);

            return Redirect::route('courses.show', $course)->with('info', 'This course does not have a placement assessment.');
        }

        $quiz = Quiz::with(['questions' => function ($query): void {
            $query->select(['id', 'quiz_id', 'lesson_id', 'type', 'text', 'options', 'order'])
                ->orderBy('order');
        }])
            ->find($course->assessment_quiz_id);

        if (! $quiz || $quiz->type !== 'assessment') {
            Log::error('Assessment quiz not found or invalid type for course.', ['course_id' => $course->id, 'quiz_id' => $course->assessment_quiz_id]);

            return Redirect::route('courses.show', $course)->with('error', 'Assessment quiz not found.');
        }

        $processedQuestions = $quiz->questions->map(function ($question): array {
            $optionsArray = [];
            if (! empty($question->options)) {
                $optionsArray = is_array($question->options)
                    ? $question->options
                    : (json_decode($question->options, true) ?? []);
            }

            return [
                'id' => $question->id,
                'quiz_id' => $question->quiz_id,
                'lesson_id' => $question->lesson_id,
                'type' => $question->type,
                'text' => $question->text,
                'options' => $optionsArray,
                'order' => $question->order,
            ];
        });

        $quizDataForView = $quiz->only('id', 'title', 'description');
        $quizDataForView['questions'] = $processedQuestions;

        return Inertia::render('courses/assessment/Show', [
            'course' => $course->only('id', 'title', 'slug'),
            'quiz' => $quizDataForView,
        ]);
    }

    /**
     * Process the assessment submission and provide recommendation.
     */
    public function submit(Request $request, Course $course): Response|RedirectResponse
    {
        Auth::user();
        $submittedAnswers = $request->input('answers', []);

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        if (! $course->assessment_quiz_id) {
            return Redirect::route('courses.show', $course)->with('error', 'Assessment configuration missing.');
        }
        $assessmentQuiz = Quiz::find($course->assessment_quiz_id);
        if (! $assessmentQuiz) {
            return Redirect::route('courses.show', $course)->with('error', 'Assessment quiz not found for grading.');
        }
        $submittedQuestionIds = array_keys($submittedAnswers);
        $questions = Question::where('quiz_id', $assessmentQuiz->id)
            ->whereIn('id', $submittedQuestionIds)
            ->select(['id', 'lesson_id', 'options', 'correct_answer', 'type'])
            ->get()
            ->keyBy('id');

        $correctCount = 0;
        $totalQuestionsGraded = 0;
        $resultsByLesson = [];

        foreach ($submittedAnswers as $questionId => $userAnswer) {
            if (! $questions->has($questionId)) {
                continue;
            }

            $question = $questions->get($questionId);
            $isCorrect = false;
            $correctAnswer = $question->correct_answer;

            switch ($question->type) {
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

            if ($question->lesson_id) {
                if (! isset($resultsByLesson[$question->lesson_id])) {
                    $resultsByLesson[$question->lesson_id] = ['correct' => 0, 'total' => 0];
                }
                $resultsByLesson[$question->lesson_id]['total']++;
                if ($isCorrect) {
                    $resultsByLesson[$question->lesson_id]['correct']++;
                }
            }
            $totalQuestionsGraded++;
        }

        $score = ($totalQuestionsGraded > 0) ? round(($correctCount / $totalQuestionsGraded) * 100) : 0;
        $recommendedLessonSlug = null;
        $passThreshold = 80;

        $courseLessons = $course->load(['modules.lessons' => function ($q): void {
            $q->orderBy('order');
        }, 'modules' => function ($q): void {
            $q->orderBy('order');
        }])
            ->modules->pluck('lessons')->flatten()->sortBy('id');

        $firstLessonSlug = $courseLessons->first()?->slug;

        foreach ($courseLessons as $lesson) {
            if (isset($resultsByLesson[$lesson->id])) {
                $lessonData = $resultsByLesson[$lesson->id];
                $lessonScore = ($lessonData['total'] > 0) ? ($lessonData['correct'] / $lessonData['total'] * 100) : 100; // Assume pass if no questions asked

                if ($lessonScore < $passThreshold) {
                    $recommendedLessonSlug = $lesson->slug;
                    break;
                }
            } else {
                $recommendedLessonSlug = $lesson->slug;
                Log::info("Assessment Recommendation: Recommending lesson {$lesson->id} as it had no assessment questions.");
                break;
            }
        }

        if ($recommendedLessonSlug === null && $totalQuestionsGraded > 0) {
            $recommendationMessage = 'Great job! You seem to have a strong grasp of the topics covered in this assessment. Feel free to review any topic or jump right into the later modules.';
            $recommendedLessonSlug = $firstLessonSlug;
        } elseif ($recommendedLessonSlug && $recommendedLessonSlug !== $firstLessonSlug) {
            $recommendedLesson = $courseLessons->firstWhere('slug', $recommendedLessonSlug);
            $recommendationMessage = "Based on your results, we recommend starting with the lesson '{$recommendedLesson?->title}'. However, you're welcome to start from the beginning for a full review.";
        } else {
            $recommendationMessage = 'Assessment complete. You can start with the first lesson.';
            $recommendedLessonSlug = $firstLessonSlug;
        }

        return Inertia::render('courses/assessment/Result', [
            'course' => $course->only('id', 'title', 'slug'),
            'score' => $score,
            'recommendationMessage' => $recommendationMessage,
            'recommendedLessonSlug' => $recommendedLessonSlug,
            'firstLessonSlug' => $firstLessonSlug,
        ]);
    }
}
