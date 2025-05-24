<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Database\Seeder;

final class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Fundamentals Course Modules ---
        $fundCourse = Course::where('slug', 'javascript-fundamentals')->first();
        if ($fundCourse) {
            Module::create(['course_id' => $fundCourse->id, 'title' => '1. Introduction', 'order' => 1]);
            Module::create(['course_id' => $fundCourse->id, 'title' => '2. Variables & Data Types', 'order' => 2]);
            Module::create(['course_id' => $fundCourse->id, 'title' => '3. Operators', 'order' => 3]);
            Module::create(['course_id' => $fundCourse->id, 'title' => '4. Control Flow (If/Loops)', 'order' => 4]);
            Module::create(['course_id' => $fundCourse->id, 'title' => '5. Functions', 'order' => 5]);
            Module::create(['course_id' => $fundCourse->id, 'title' => '6. Arrays & Objects Intro', 'order' => 6]);
            Module::create(['course_id' => $fundCourse->id, 'title' => '7. Basic DOM Manipulation', 'order' => 7]);
        }

        // --- Intermediate Course Modules ---
        $intCourse = Course::where('slug', 'intermediate-web-dev-js')->first();
        if ($intCourse) {
            Module::create(['course_id' => $intCourse->id, 'title' => '1. DOM Deep Dive', 'order' => 1]);
            Module::create(['course_id' => $intCourse->id, 'title' => '2. Events In-Depth', 'order' => 2]);
            Module::create(['course_id' => $intCourse->id, 'title' => '3. Working with Forms', 'order' => 3]);
            Module::create(['course_id' => $intCourse->id, 'title' => '4. Asynchronous JavaScript & Promises', 'order' => 4]);
            Module::create(['course_id' => $intCourse->id, 'title' => '5. Fetch API & AJAX', 'order' => 5]);
            Module::create(['course_id' => $intCourse->id, 'title' => '6. Browser Storage', 'order' => 6]);
        }

        // --- Advanced Course Modules ---
        $advCourse = Course::where('slug', 'advanced-js-nodejs')->first();
        if ($advCourse) {
            Module::create(['course_id' => $advCourse->id, 'title' => '1. Async/Await', 'order' => 1]);
            Module::create(['course_id' => $advCourse->id, 'title' => '2. JavaScript Modules (ESM/CJS)', 'order' => 2]);
            Module::create(['course_id' => $advCourse->id, 'title' => '3. OOP in JavaScript (Classes)', 'order' => 3]);
            Module::create(['course_id' => $advCourse->id, 'title' => '4. Functional Programming Patterns', 'order' => 4]);
            Module::create(['course_id' => $advCourse->id, 'title' => '5. Advanced Error Handling', 'order' => 5]);
            Module::create(['course_id' => $advCourse->id, 'title' => '6. Introduction to Node.js', 'order' => 6]);
            Module::create(['course_id' => $advCourse->id, 'title' => '7. Building a Simple API with Node', 'order' => 7]);
        }
    }
}
