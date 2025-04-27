<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

final class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::create([
            'title' => 'JavaScript Basics',
            'slug' => 'javascript-basics',
            'description' => 'Learn the fundamentals of JavaScript.',
            'is_published' => true,
        ]);

        Course::create([
            'title' => 'Advanced JavaScript Concepts',
            'slug' => 'advanced-javascript',
            'description' => 'Dive deeper into JavaScript topics.',
            'is_published' => false,
        ]);
    }
}
