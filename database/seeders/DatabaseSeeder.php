<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            LearningPathSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            CourseSeeder::class,
            LearningPathCourseSeeder::class,
            ModuleSeeder::class,
            LessonSeeder::class,
            ExternalResourceSeeder::class,
            QuizSeeder::class,
            QuestionSeeder::class,
        ]);
    }
}
