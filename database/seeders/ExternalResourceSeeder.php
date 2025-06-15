<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ExternalResource;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

final class ExternalResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExternalResource::truncate();

        $lessonDeclare = Lesson::where('slug', 'declaring-variables')->first();
        if ($lessonDeclare) {
            ExternalResource::create([
                'lesson_id' => $lessonDeclare->id,
                'title' => 'MDN: let keyword',
                'url' => 'https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/let',
                'type' => 'documentation',
                'description' => 'Detailed documentation on the `let` keyword from Mozilla Developer Network.',
            ]);
            ExternalResource::create([
                'lesson_id' => $lessonDeclare->id,
                'title' => 'Understanding var, let, and const in JavaScript (Video)',
                'url' => 'https://www.youtube.com/watch?v=s-hLgT_t3uA',
                'type' => 'video',
                'description' => 'A video explaining the differences and use cases for variable declarations.',
            ]);
        }

        $lessonTypes = Lesson::where('slug', 'primitive-data-types')->first();
        if ($lessonTypes) {
            ExternalResource::create([
                'lesson_id' => $lessonTypes->id,
                'title' => 'MDN: JavaScript data types and data structures',
                'url' => 'https://developer.mozilla.org/en-US/docs/Web/JavaScript/Data_structures',
                'type' => 'documentation',
            ]);
        }
    }
}
