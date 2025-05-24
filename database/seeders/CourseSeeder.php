<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

final class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Quiz::create([
            'module_id' => null,
            'type' => 'assessment',
            'title' => 'JS Basics Placement Assessment',
            'description' => 'Check your existing JavaScript knowledge to find the best starting point.',
            'order' => 0,
        ]);

        Quiz::create([
            'module_id' => null,
            'type' => 'final_review',
            'title' => 'JS Fundamentals - Final Review',
            'description' => 'Comprehensive review of all JavaScript Fundamentals topics.',
            'order' => 99,
        ]);

        Quiz::create([
            'module_id' => null,
            'type' => 'final_review',
            'title' => 'Intermediate Web Dev - Final Review',
            'description' => 'Comprehensive review of Intermediate Web Development topics.',
            'order' => 99,
        ]);

        $assessmentQuiz = Quiz::where('type', 'assessment')->where('title', 'JS Basics Placement Assessment')->first();
        $fundFinalReviewQuiz = Quiz::where('type', 'final_review')->where('title', 'JS Fundamentals - Final Review')->first();
        $intFinalReviewQuiz = Quiz::where('type', 'final_review')->where('title', 'Intermediate Web Dev - Final Review')->first();

        Course::create([
            'title' => 'JavaScript Fundamentals',
            'slug' => 'javascript-fundamentals',
            'description' => 'Master the absolute basics of JavaScript, from variables to basic DOM interaction.',
            'is_published' => true,
            'assessment_quiz_id' => $assessmentQuiz?->id,
            'final_review_quiz_id' => $fundFinalReviewQuiz?->id,
        ]);

        Course::create([
            'title' => 'Intermediate Web Development with JS',
            'slug' => 'intermediate-web-dev-js',
            'description' => 'Dive deeper into browser APIs, asynchronous JavaScript, and interacting with web pages.',
            'is_published' => true,
            'assessment_quiz_id' => null,
            'final_review_quiz_id' => $intFinalReviewQuiz?->id,
        ]);

        Course::create([
            'title' => 'Advanced JavaScript & Node.js',
            'slug' => 'advanced-js-nodejs',
            'description' => 'Explore modern JS features, server-side concepts with Node.js, and common patterns.',
            'is_published' => true,
            'assessment_quiz_id' => null,
        ]);
    }
}
