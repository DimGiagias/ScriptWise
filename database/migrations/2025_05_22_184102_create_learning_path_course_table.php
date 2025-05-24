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
        Schema::create('learning_path_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_path_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['learning_path_id', 'course_id']);
            $table->index(['learning_path_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_path_course');
    }
};
