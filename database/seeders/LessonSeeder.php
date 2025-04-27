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
        $introModule = Module::where('title', 'Introduction')->first();
        $varsModule = Module::where('title', 'Variables and Data Types')->first();

        if ($introModule) {
            Lesson::create([
                'module_id' => $introModule->id,
                'title' => 'What is JavaScript?',
                'slug' => 'what-is-javascript',
                'content' => '<p>JavaScript is a scripting language used to create and control dynamic website content...</p>',
                'order' => 1,
            ]);
            Lesson::create([
                'module_id' => $introModule->id,
                'title' => 'Setting up Your Environment',
                'slug' => 'setting-up-environment',
                'content' => '<p>You can run JavaScript directly in your browser\'s console...</p>',
                'order' => 2,
            ]);
        }

        if ($varsModule) {
            Lesson::create([
                'module_id' => $varsModule->id,
                'title' => 'Declaring Variables (var, let, const)',
                'slug' => 'declaring-variables',
                'content' => '<p>Variables are containers for storing data values. Use <code>let</code> and <code>const</code>...</p>',
                'order' => 1,
            ]);
            Lesson::create([
                'module_id' => $varsModule->id,
                'title' => 'Primitive Data Types',
                'slug' => 'primitive-data-types',
                'content' => '<p>JavaScript has several primitive types: String, Number, Boolean, Null, Undefined, Symbol, BigInt...</p>',
                'order' => 2,
            ]);
        }
    }
}
