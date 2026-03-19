<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_courses', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->unsignedInteger('workload_hours')->default(0);
            $table->enum('level', ['basic', 'intermediate', 'advanced'])->default('basic');
            $table->timestamps();
            $table->index('level');
        });

        Schema::create('academy_modules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained('academy_courses')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('order')->default(1);
            $table->timestamps();
            $table->index(['course_id', 'order']);
        });

        Schema::create('academy_lessons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('module_id')->constrained('academy_modules')->cascadeOnDelete();
            $table->string('title');
            $table->string('video_url')->nullable();
            $table->longText('content_text')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->boolean('is_free')->default(false);
            $table->timestamps();
        });

        Schema::create('academy_lesson_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lesson_id')->constrained('academy_lessons')->cascadeOnDelete();
            $table->string('file_path');
            $table->enum('type', ['pdf', 'slide', 'worksheet']);
            $table->timestamps();
        });

        Schema::create('academy_student_progress', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('academy_lessons')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_student_progress');
        Schema::dropIfExists('academy_lesson_attachments');
        Schema::dropIfExists('academy_lessons');
        Schema::dropIfExists('academy_modules');
        Schema::dropIfExists('academy_courses');
    }
};
