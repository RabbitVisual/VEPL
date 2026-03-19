<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Planos de Leitura Bíblica — Módulo Bible (Plans).
     *
     * Templates, Planos, Dias, Conteúdos (v2), Subscrições,
     * Progresso, Notas, Logs de Leitura, Auditoria e Badges.
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // TEMPLATES DE PLANO (Iniciante / Canônico / Cronológico / Doutrinário…)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_plan_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();               // nt_psalms | canonical | chronological | doctrinal | christ_centered
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('complexity', ['iniciante', 'standard', 'exegetical'])->default('standard');
            $table->enum('order_type', ['canonical', 'chronological', 'doctrinal', 'christ_centered', 'nt_psalms'])->default('canonical');
            $table->json('options')->nullable();               // Temas doutrinários, referências, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────────────────
        // PLANOS
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('type', ['sequential', 'chronological', 'thematic', 'manual'])->default('manual');
            $table->enum('reading_mode', ['digital', 'physical_timer'])->default('digital');
            $table->boolean('allow_back_tracking')->default(true)->comment('Permite acesso a dias já concluídos');
            $table->integer('duration_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_church_plan')->default(false); // Plano oficial da escola/church body
            $table->string('complexity', 32)->nullable();      // iniciante | standard | exegetical
            $table->string('template_key', 64)->nullable();    // FK lógica para bible_plan_templates.key
            $table->timestamps();
            $table->softDeletes();
        });

        // ─────────────────────────────────────────────────────────────────────
        // DIAS DO PLANO
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_plan_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('bible_plans')->cascadeOnDelete();
            $table->integer('day_number');
            $table->string('title')->nullable();               // "A queda do homem" ou null → usa "Dia X"
            $table->timestamps();

            $table->unique(['plan_id', 'day_number']);
        });

        // ─────────────────────────────────────────────────────────────────────
        // CONTEÚDOS DO DIA (v2 — polimórfico: escritura | devocional | vídeo)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_plan_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_day_id')->constrained('bible_plan_days')->cascadeOnDelete();
            $table->integer('order_index')->default(0);
            $table->enum('type', ['scripture', 'devotional', 'video'])->default('scripture');

            // Para devocionais e vídeos
            $table->string('title')->nullable();               // "Reflexão Matinal"
            $table->text('body')->nullable();                  // HTML ou URL de vídeo

            // Para escritura (colunas otimizadas sem overhead de JSON)
            $table->unsignedBigInteger('book_id')->nullable();
            $table->integer('chapter_start')->nullable();
            $table->integer('chapter_end')->nullable();
            $table->integer('verse_start')->nullable();
            $table->integer('verse_end')->nullable();

            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────────────────
        // SUBSCRIÇÕES (usuário → plano)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_plan_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('bible_plans')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('projected_end_date')->nullable();
            $table->integer('current_day_number')->default(1);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->time('notification_time')->nullable();

            // Gamificação / Streaks
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->timestamp('last_activity_at')->nullable();

            // Integração com módulo Intercessor (quando usuário está muito atrasado)
            $table->unsignedBigInteger('prayer_request_id')->nullable();

            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────────────────
        // PROGRESSO DIÁRIO
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('bible_plan_subscriptions')->cascadeOnDelete();
            $table->foreignId('plan_day_id')->constrained('bible_plan_days')->cascadeOnDelete();
            $table->integer('time_spent')->nullable()->comment('Segundos gastos na leitura (Bíblia Física + Cronômetro)');
            $table->timestamp('completed_at')->useCurrent();

            $table->unique(['subscription_id', 'plan_day_id']);
        });

        // ─────────────────────────────────────────────────────────────────────
        // ANOTAÇÕES PESSOAIS (por versículo ou por dia do plano)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_user_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_day_id')->nullable()->constrained('bible_plan_days')->nullOnDelete();
            $table->unsignedBigInteger('book_id')->nullable();
            $table->integer('chapter')->nullable();
            $table->integer('verse')->nullable();
            $table->text('note_content');
            $table->string('color_code')->default('#ffee00');  // Destaque amarelo por padrão
            $table->boolean('is_private')->default(true);
            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────────────────
        // LOG CANÔNICO DE LEITURA (Bereano da Semana, check-ins, streak engine)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('user_reading_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('bible_plan_subscriptions')->cascadeOnDelete();
            $table->foreignId('plan_day_id')->constrained('bible_plan_days')->cascadeOnDelete();
            $table->integer('day_number');
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['subscription_id', 'plan_day_id']);
        });

        // ─────────────────────────────────────────────────────────────────────
        // LOG DE AUDITORIA (ações de leitura para análise e suporte)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_reading_audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->constrained('bible_plan_subscriptions')->cascadeOnDelete();
            $table->string('action', 64);                      // day_completed | plan_reset | plan_started…
            $table->json('payload')->nullable();               // Contexto adicional da ação
            $table->timestamps();

            $table->index(['subscription_id', 'action']);
            $table->index('created_at');
        });

        // ─────────────────────────────────────────────────────────────────────
        // BADGES DE LEITURA (Bereano, Fiel ao Pacto, Leitor do Corpo…)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('badge_key', 64);                  // bereano_da_semana | fiel_ao_pacto | leitor_do_corpo
            $table->foreignId('subscription_id')->nullable()->constrained('bible_plan_subscriptions')->cascadeOnDelete();
            $table->timestamp('awarded_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_user_badges');
        Schema::dropIfExists('bible_reading_audit_log');
        Schema::dropIfExists('user_reading_logs');
        Schema::dropIfExists('bible_user_notes');
        Schema::dropIfExists('bible_user_progress');
        Schema::dropIfExists('bible_plan_subscriptions');
        Schema::dropIfExists('bible_plan_contents');
        Schema::dropIfExists('bible_plan_days');
        Schema::dropIfExists('bible_plans');
        Schema::dropIfExists('bible_plan_templates');
    }
};
