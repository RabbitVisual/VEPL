<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Consolidates 23+ migrations into a professional, integrated schema for the Worship Module.
     */
    public function up(): void
    {
        // ── Governança Instrumental ───────────────────────────────────────────

        Schema::create('worship_instrument_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('gray');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('worship_instruments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('worship_instrument_categories')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('worship_team_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('worship_equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('worship_team_role_id')->nullable()->constrained('worship_team_roles')->nullOnDelete();
            $table->string('status')->default('active'); // active, maintenance, broken
            $table->string('serial_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── Gestão de Músicas e Conteúdo ──────────────────────────────────────

        Schema::create('worship_songs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artist')->nullable();
            $table->integer('bpm')->nullable();
            $table->string('time_signature')->default('4/4')->nullable();
            $table->string('original_key')->nullable();
            $table->text('content_chordpro')->nullable();
            $table->text('lyrics_only')->nullable();
            $table->string('youtube_id')->nullable();
            $table->json('themes')->nullable();
            $table->string('multitrack_url')->nullable();
            $table->text('song_structure')->nullable();
            $table->timestamps();
        });

        Schema::create('worship_custom_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('worship_media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type'); // image, video
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->timestamps();
        });

        // ── Planejamento e Escalas ────────────────────────────────────────────

        Schema::create('worship_setlists', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('scheduled_at');
            $table->foreignId('leader_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ministry_id')->nullable()->constrained('ministries')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, rehearsal, live, finished
            $table->string('background_image')->nullable();
            $table->text('producer_notes')->nullable();
            $table->string('stage_layout_pdf')->nullable();
            $table->timestamps();
        });

        Schema::create('worship_setlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setlist_id')->constrained('worship_setlists')->onDelete('cascade');
            $table->string('type')->default('song'); // song, slide, timeline
            $table->string('title')->nullable();
            $table->foreignId('song_id')->nullable()->constrained('worship_songs')->onDelete('cascade');
            $table->foreignId('custom_slide_id')->nullable()->constrained('worship_custom_slides')->onDelete('cascade');
            $table->string('override_key')->nullable();
            $table->text('arrangement_note')->nullable();
            $table->integer('order')->default(0);
            $table->json('content')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('worship_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setlist_id')->constrained('worship_setlists')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('instrument_id')->nullable()->constrained('worship_instruments')->onDelete('cascade');
            $table->foreignId('worship_team_role_id')->nullable()->constrained('worship_team_roles')->nullOnDelete();
            $table->string('status')->default('pending'); // pending, confirmed, declined
            $table->text('member_notes')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });

        // ── Academia Worship (Escola de Músicos) ─────────────────────────────

        Schema::create('worship_academy_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('instrument_id')->constrained('worship_instruments')->cascadeOnDelete();
            $table->foreignId('worship_team_role_id')->nullable()->constrained('worship_team_roles')->nullOnDelete();
            $table->unsignedBigInteger('asset_id')->nullable(); // Integration with global assets if exists
            $table->string('level')->default('beginner');
            $table->string('difficulty_level')->nullable();
            $table->string('category')->nullable(); // vocal, instrumental, teoria, espiritualidade
            $table->string('status')->default('draft'); // draft, published, archived
            $table->text('description')->nullable();
            $table->text('biblical_reflection')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });

        Schema::create('worship_academy_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('worship_academy_courses')->cascadeOnDelete();
            $table->string('title');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('worship_academy_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('worship_academy_modules')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('worship_academy_courses')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('type')->default('video'); // video, chordpro, material
            $table->string('video_url')->nullable();
            $table->string('multicam_video_url')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('sheet_music_pdf')->nullable();
            $table->longText('content')->nullable();
            $table->text('teacher_tips')->nullable();
            $table->string('bible_reference')->nullable();
            $table->foreignId('requirement_song_id')->nullable()->constrained('worship_songs')->nullOnDelete();
            $table->integer('order')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->timestamps();
        });

        Schema::create('worship_lesson_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('worship_academy_lessons')->cascadeOnDelete();
            $table->string('type')->default('other'); // pdf, audio, guitar_pro, other
            $table->string('label');
            $table->string('file_path');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('worship_academy_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('worship_academy_courses')->cascadeOnDelete();
            $table->integer('progress_percent')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });

        Schema::create('worship_academy_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('worship_academy_lessons')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->integer('score')->default(0);
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worship_academy_progress');
        Schema::dropIfExists('worship_academy_enrollments');
        Schema::dropIfExists('worship_lesson_materials');
        Schema::dropIfExists('worship_academy_lessons');
        Schema::dropIfExists('worship_academy_modules');
        Schema::dropIfExists('worship_academy_courses');
        Schema::dropIfExists('worship_rosters');
        Schema::dropIfExists('worship_setlist_items');
        Schema::dropIfExists('worship_setlists');
        Schema::dropIfExists('worship_media_assets');
        Schema::dropIfExists('worship_custom_slides');
        Schema::dropIfExists('worship_songs');
        Schema::dropIfExists('worship_equipments');
        Schema::dropIfExists('worship_team_roles');
        Schema::dropIfExists('worship_instruments');
        Schema::dropIfExists('worship_instrument_categories');
    }
};
