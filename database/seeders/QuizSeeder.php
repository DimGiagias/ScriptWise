<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

final class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $introModule = Module::where('title', 'Introduction')->first();
        $varsModule = Module::where('title', 'Variables and Data Types')->first();

        if ($introModule) {
            $introQuiz = Quiz::create([
                'module_id' => $introModule->id,
                'title' => 'Quiz: Introduction Concepts',
                'description' => 'Test your understanding of the basics.',
                'order' => 1, // Order of quiz within the module
            ]);

            // Associate this quiz with the lessons it covers
            $lesson1 = Lesson::where('slug', 'what-is-javascript')->first();
            $lesson2 = Lesson::where('slug', 'setting-up-environment')->first();
            if ($lesson1) {
                $introQuiz->lessons()->attach($lesson1->id);
            }
            if ($lesson2) {
                $introQuiz->lessons()->attach($lesson2->id);
            }
        }

        if ($varsModule) {
            $varsQuiz = Quiz::create([
                'module_id' => $varsModule->id,
                'title' => 'Quiz: Variables',
                'description' => 'Check your knowledge of JS variables.',
                'order' => 1,
            ]);
            $lesson3 = Lesson::where('slug', 'declaring-variables')->first();
            $lesson4 = Lesson::where('slug', 'primitive-data-types')->first();
            if ($lesson3) {
                $varsQuiz->lessons()->attach($lesson3->id);
            }
            if ($lesson4) {
                $varsQuiz->lessons()->attach($lesson4->id);
            }
        }
    }
}
