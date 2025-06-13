<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use App\Models\LearningPath;
use Illuminate\Database\Seeder;

final class LearningPathCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('learning_path_course')->truncate();

        $pathFrontend = LearningPath::where('slug', 'frontend-developer-path')->first();
        $pathBackend = LearningPath::where('slug', 'backend-javascript-developer-path')->first();
        $pathFullstack = LearningPath::where('slug', 'full-stack-javascript-path')->first();

        $courseFundamentals = Course::where('slug', 'javascript-fundamentals')->first();
        $courseIntermediate = Course::where('slug', 'intermediate-web-dev-js')->first();
        $courseAdvanced = Course::where('slug', 'advanced-js-nodejs')->first();

        if ($pathFrontend && $courseFundamentals) {
            $pathFrontend->courses()->attach($courseFundamentals->id, ['order' => 1]);
        }
        if ($pathFrontend && $courseIntermediate) {
            $pathFrontend->courses()->attach($courseIntermediate->id, ['order' => 2]);
        }

        if ($pathBackend && $courseFundamentals) {
            $pathBackend->courses()->attach($courseFundamentals->id, ['order' => 1]);
        }
        if ($pathBackend && $courseAdvanced) {
            $pathBackend->courses()->attach($courseAdvanced->id, ['order' => 2]);
        }

        if ($pathFullstack && $courseFundamentals) {
            $pathFullstack->courses()->attach($courseFundamentals->id, ['order' => 1]);
        }
        if ($pathFullstack && $courseIntermediate) {
            $pathFullstack->courses()->attach($courseIntermediate->id, ['order' => 2]);
        }
        if ($pathFullstack && $courseAdvanced) {
            $pathFullstack->courses()->attach($courseAdvanced->id, ['order' => 3]);
        }
    }
}
