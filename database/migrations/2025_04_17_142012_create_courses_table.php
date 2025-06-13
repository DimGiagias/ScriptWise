<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(false);
            // Set null on delete: if the quiz is deleted, the course doesn't break, just loses its assessment link.
            $table->foreignId('assessment_quiz_id')->nullable()->after('is_published')->constrained('quizzes')->onDelete('set null');
            $table->foreignId('final_review_quiz_id')->nullable()->after('assessment_quiz_id')->constrained('quizzes')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
