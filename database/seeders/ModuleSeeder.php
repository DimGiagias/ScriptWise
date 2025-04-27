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
        $jsBasics = Course::where('slug', 'javascript-basics')->first();

        if ($jsBasics) {
            Module::create([
                'course_id' => $jsBasics->id,
                'title' => 'Introduction',
                'order' => 1,
            ]);
            Module::create([
                'course_id' => $jsBasics->id,
                'title' => 'Variables and Data Types',
                'order' => 2,
            ]);
            Module::create([
                'course_id' => $jsBasics->id,
                'title' => 'Operators',
                'order' => 3,
            ]);
        }
    }
}
