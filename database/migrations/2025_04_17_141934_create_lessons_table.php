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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->comment('Lesson text, Markdown or HTML');
            $table->text('video_embed_html')->nullable()->after('content');
            $table->text('assignment')->nullable()->comment('Assignment description');
            $table->text('initial_code')->nullable()->comment('Starting code for editor');
            $table->text('expected_output')->nullable()->comment('For simple stdout checks');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index(['module_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
