<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabelas de framework e infraestrutura do sistema.
     *
     * Inclui: usuários, autenticação, sessões, cache, filas,
     *         tokens de API (Sanctum), logs de segurança e utilitários de CEP.
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // USUÁRIOS & AUTENTICAÇÃO
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('users', function (Blueprint $table) {
            // ── Identificação ──────────────────────────────────────────────────
            $table->id();
            $table->string('name');
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('cpf', 14)->unique()->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['M', 'F', 'O'])->nullable();
            $table->enum('marital_status', ['solteiro', 'casado', 'divorciado', 'viuvo', 'uniao_estavel'])->nullable();

            // ── Contato ────────────────────────────────────────────────────────
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('cellphone', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();

            // ── Endereço ───────────────────────────────────────────────────────
            $table->string('address', 255)->nullable();
            $table->string('address_number', 20)->nullable();
            $table->string('address_complement', 100)->nullable();
            $table->string('neighborhood', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip_code', 10)->nullable();

            // ── Dados Ministeriais e Eclesiásticos ─────────────────────────────
            $table->string('title')->nullable();                  // Pastor, Missionário, Diácono, Líder…
            $table->boolean('is_ordained')->default(false);
            $table->date('ordination_date')->nullable();
            $table->date('ministry_start_date')->nullable();
            $table->string('affiliated_church')->nullable();
            $table->string('baptist_convention')->nullable();
            $table->string('theological_education')->nullable();
            $table->text('biography')->nullable();

            // ── Dados de Fé ────────────────────────────────────────────────────
            $table->boolean('is_baptized')->default(false);
            $table->date('baptism_date')->nullable();

            // ── Profissional ───────────────────────────────────────────────────
            $table->string('profession', 100)->nullable();
            $table->string('education_level', 50)->nullable();
            $table->string('workplace', 255)->nullable();

            // ── Contato de Emergência ──────────────────────────────────────────
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relationship', 50)->nullable();

            // ── Segurança e Autenticação ───────────────────────────────────────
            $table->string('password');
            $table->text('two_factor_secret')->nullable();        // TOTP — armazenado criptografado
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->rememberToken();

            // ── Controle de Acesso ─────────────────────────────────────────────
            $table->unsignedBigInteger('role_id')->default(2);
            $table->boolean('is_active')->default(true);

            // ── Mídia e Notas ──────────────────────────────────────────────────
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('password_reset_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type');                               // email | cpf
            $table->string('identifier');                        // valor usado na busca
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status')->default('sent');           // sent | failed | completed
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // ─────────────────────────────────────────────────────────────────────
        // API — SANCTUM (Personal Access Tokens)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────────────────
        // CACHE
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // ─────────────────────────────────────────────────────────────────────
        // FILAS (Queue)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // ─────────────────────────────────────────────────────────────────────
        // UTILITÁRIOS — CEP (endereçamento brasileiro)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('cep_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('uf', 2)->comment('Unidade Federativa (Estado)');
            $table->string('cidade', 255)->comment('Nome da cidade');
            $table->string('cep_de', 8)->comment('CEP inicial da faixa');
            $table->string('cep_ate', 8)->comment('CEP final da faixa');
            $table->string('tipo', 50)->nullable()->comment('Tipo: urbano, rural, total…');
            $table->timestamps();

            $table->index('uf');
            $table->index('cidade');
            $table->index(['cep_de', 'cep_ate']);
            $table->index('cep_de');
            $table->index('cep_ate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cep_ranges');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_logs');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('user_photos');
        Schema::dropIfExists('users');
    }
};
