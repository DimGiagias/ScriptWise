<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;

final class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        function createLesson($module, $order, $title, $slug, $content, $video_embed_html = null, $assignment = null, $initial_code = null, $expected_output = null)
        {
            if ($module) {
                Lesson::create([
                    'module_id' => $module->id,
                    'title' => $order.'. '.$title,
                    'slug' => $slug,
                    'content' => "<p>{$content}</p>",
                    'video_embed_html' => $video_embed_html,
                    'assignment' => $assignment,
                    'initial_code' => $initial_code,
                    'expected_output' => $expected_output,
                    'order' => $order,
                ]);
            }
        }

        // --- Fundamentals Lessons ---
        $modFund1 = Module::where('title', '1. Introduction')->first();
        createLesson($modFund1, 1, 'What is JavaScript?', 'what-is-javascript', 'JavaScript (JS) is a versatile scripting language used primarily for creating dynamic and interactive web content.', '<iframe width="560" height="315" src="https://www.youtube.com/embed/W6NZfCO5sik" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>', 'Use `console.log` to print the message "Hello, World!" to the console.', 'console.log("Your message here");', "Hello, World!\n");
        createLesson($modFund1, 2, 'Setting Up Your Environment', 'setting-up-environment', 'You can run JavaScript directly in your browser\'s developer console (usually F12). For larger projects, you\'ll use a code editor and Node.js.');
        createLesson($modFund1, 3, 'Your First Code: console.log', 'first-code-console-log', 'The `console.log()` function is essential for displaying output and debugging your code.', '<iframe width="560" height="315" src="https://www.youtube.com/embed/hdI2bqOjy3c?start=18" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>', 'Use `console.log` to print the message "Hello, World!" to the console.', 'console.log("Your message here");', "Hello, World!\n");

        $modFund2 = Module::where('title', '2. Variables & Data Types')->first();
        createLesson($modFund2, 1, 'Declaring Variables (var, let, const)', 'declaring-variables', 'Learn how `var`, `let`, and `const` are used to store data. Modern JS prefers `let` and `const`.', null, "Declare a variable `city` using `let` with the value 'Paris'. Log it.", "let city = \nconsole.log(city);", "Paris\n");
        createLesson($modFund2, 2, 'Primitive Data Types', 'primitive-data-types', 'Explore String, Number, Boolean, Null, Undefined, Symbol, and BigInt.', null, 'Declare constants `age` (number 30) and `isStudent` (boolean false). Log their types.', "const age = \nconst isStudent = \nconsole.log(typeof age);\nconsole.log(typeof isStudent);", "number\nboolean\n");
        createLesson($modFund2, 3, 'Type Coercion', 'type-coercion', 'Understand how JavaScript sometimes automatically converts data types, which can lead to unexpected results.', null, 'Log the result of `5 + "5"` to the console. What is the type?', 'console.log(5 + "5");\nconsole.log(typeof (5 + "5"));', "55\nstring\n");

        // ... Add lessons for Fundamentals Modules 3, 4, 5, 6, 7 with placeholders ...
        $modFund3 = Module::where('title', '3. Operators')->first();
        createLesson($modFund3, 1, 'Arithmetic Operators', 'arithmetic-operators', 'Placeholder: +, -, *, /, %, ++, --');
        createLesson($modFund3, 2, 'Comparison Operators', 'comparison-operators', 'Placeholder: ==, ===, !=, !==, >, <, >=, <=');
        createLesson($modFund3, 3, 'Logical Operators', 'logical-operators', 'Placeholder: &&, ||, !');

        $modFund4 = Module::where('title', '4. Control Flow (If/Loops)')->first();
        createLesson($modFund4, 1, 'If / Else Statements', 'if-else', 'Placeholder: Conditional execution.');
        createLesson($modFund4, 2, 'Switch Statements', 'switch', 'Placeholder: Multi-way branching.');
        createLesson($modFund4, 3, 'For Loops', 'for-loops', 'Placeholder: Iterating a set number of times.');
        createLesson($modFund4, 4, 'While Loops', 'while-loops', 'Placeholder: Iterating based on a condition.');

        // --- Intermediate Lessons ---
        $modInt1 = Module::where('title', '1. DOM Deep Dive')->first();
        createLesson($modInt1, 1, 'Selecting Elements (Advanced)', 'dom-selecting-advanced', 'Placeholder: querySelectorAll, getElementsByClassName, etc.');
        createLesson($modInt1, 2, 'Traversing the DOM', 'dom-traversing', 'Placeholder: parentNode, children, nextElementSibling.');
        createLesson($modInt1, 3, 'Creating & Appending Elements', 'dom-creating-appending', 'Placeholder: createElement, appendChild, insertBefore.');

        // --- Advanced Lessons ---
        $modAdv1 = Module::where('title', '1. Async/Await')->first();
        createLesson($modAdv1, 1, 'Async/Await Syntax', 'async-await-syntax', 'Placeholder: Cleaner way to handle Promises.');
        createLesson($modAdv1, 2, 'Error Handling with Async/Await', 'async-await-errors', 'Placeholder: Using try...catch blocks.');

    }
}
