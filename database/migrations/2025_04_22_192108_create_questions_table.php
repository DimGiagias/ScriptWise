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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            // Crucial link for suggesting review topics!
            // Can be null if a question is general, or set null if lesson deleted.
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('set null');
            // Start with multiple choice, add more later if needed
            $table->enum('type', ['multiple_choice', 'true_false', 'fill_blank'])->default('multiple_choice');
            $table->text('text')->comment('The question text');
            $table->json('options')->nullable();
            $table->string('correct_answer')->comment('The ID of the correct option (e.g., "a", "b")');
            $table->text('explanation')->nullable()->comment('Explanation shown after answering');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index(['quiz_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
