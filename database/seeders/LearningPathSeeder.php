<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\LearningPath;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class LearningPathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LearningPath::truncate();

        LearningPath::create([
            'name' => 'Frontend Developer Path',
            'slug' => Str::slug('Frontend Developer Path'),
            'description' => 'Learn the essentials of frontend web development, focusing on JavaScript, HTML, CSS, and modern frameworks.',
            'is_active' => true,
        ]);

        LearningPath::create([
            'name' => 'Backend JavaScript Developer Path',
            'slug' => Str::slug('Backend JavaScript Developer Path'),
            'description' => 'Master server-side JavaScript with Node.js, Express, and databases to build robust APIs and web applications.',
            'is_active' => true,
        ]);

        LearningPath::create([
            'name' => 'Full-Stack JavaScript Path',
            'slug' => Str::slug('Full-Stack JavaScript Path'),
            'description' => 'Become proficient in both frontend and backend JavaScript technologies for a comprehensive skill set.',
            'is_active' => true,
        ]);
    }
}
