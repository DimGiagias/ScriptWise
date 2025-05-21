<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

final class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $introQuiz = Quiz::where('title', 'Quiz: Introduction Concepts')->first();
        $varsQuiz = Quiz::where('title', 'Quiz: Variables')->first();

        $lessonWhatIsJs = Lesson::where('slug', 'what-is-javascript')->first();
        $lessonSetup = Lesson::where('slug', 'setting-up-environment')->first();
        $lessonDeclare = Lesson::where('slug', 'declaring-variables')->first();
        $lessonTypes = Lesson::where('slug', 'primitive-data-types')->first();

        if ($introQuiz) {
            Question::create([
                'quiz_id' => $introQuiz->id,
                'lesson_id' => $lessonWhatIsJs?->id,
                'type' => 'multiple_choice',
                'text' => 'What is JavaScript primarily used for?',
                'options' => json_encode([
                    ['id' => 'a', 'text' => 'Styling web pages'],
                    ['id' => 'b', 'text' => 'Creating dynamic web content'],
                    ['id' => 'c', 'text' => 'Managing databases'],
                    ['id' => 'd', 'text' => 'Server-side logic only'],
                ]),
                'correct_answer' => 'b',
                'explanation' => 'JavaScript runs in the browser to make web pages interactive.',
                'order' => 1,
            ]);

            Question::create([
                'quiz_id' => $introQuiz->id,
                'lesson_id' => $lessonSetup?->id,
                'type' => 'multiple_choice',
                'text' => 'Where can you easily test JavaScript code?',
                'options' => json_encode([
                    ['id' => 'a', 'text' => 'Microsoft Word'],
                    ['id' => 'b', 'text' => 'Browser Developer Console'],
                    ['id' => 'c', 'text' => 'Photoshop'],
                    ['id' => 'd', 'text' => 'CSS file'],
                ]),
                'correct_answer' => 'b',
                'explanation' => 'The browser console is a built-in tool for running JS snippets.',
                'order' => 2,
            ]);

        }

        if ($varsQuiz) {
            Question::create([
                'quiz_id' => $varsQuiz->id,
                'lesson_id' => $lessonDeclare?->id,
                'type' => 'multiple_choice',
                'text' => 'Which keyword is used to declare a variable whose value cannot be reassigned?',
                'options' => json_encode([
                    ['id' => 'a', 'text' => 'var'],
                    ['id' => 'b', 'text' => 'let'],
                    ['id' => 'c', 'text' => 'const'],
                    ['id' => 'd', 'text' => 'static'],
                ]),
                'correct_answer' => 'c',
                'explanation' => '`const` declares block-scoped variables whose value cannot be reassigned.',
                'order' => 1,
            ]);
            Question::create([
                'quiz_id' => $varsQuiz->id,
                'lesson_id' => $lessonTypes?->id,
                'type' => 'multiple_choice',
                'text' => 'Which of these is NOT a primitive data type in JavaScript?',
                'options' => json_encode([
                    ['id' => 'a', 'text' => 'String'],
                    ['id' => 'b', 'text' => 'Number'],
                    ['id' => 'c', 'text' => 'Array'],
                    ['id' => 'd', 'text' => 'Boolean'],
                ]),
                'correct_answer' => 'c',
                'explanation' => 'Arrays are objects in JavaScript, not primitive types.',
                'order' => 2,
            ]);
        }
    }
}
