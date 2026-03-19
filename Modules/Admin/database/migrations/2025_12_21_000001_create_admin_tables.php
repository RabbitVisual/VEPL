<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabelas do módulo Admin.
     *
     * Inclui: configurações do sistema (settings) e
     *         relacionamentos/conexões entre usuários (user_relationships).
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // CONFIGURAÇÕES DO SISTEMA
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Chave única da configuração (ex: site_name)');
            $table->text('value')->nullable()->comment('Valor armazenado como texto');
            $table->string('type')->default('string')->comment('Tipos: string | text | boolean | integer | file | json');
            $table->string('group')->default('general')->comment('Grupos: general | appearance | email | intercessor | notifications…');
            $table->text('description')->nullable()->comment('Descrição legível para o painel de administração');
            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────────────────
        // RELACIONAMENTOS ENTRE USUÁRIOS
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('user_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('related_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('related_name')->nullable();                        // Nome livre (se o relacionado não for usuário cadastrado)
            $table->string('relationship_type', 50)->index();                  // cônjuge | filho | discípulo | mentorado…
            $table->string('status', 20)->default('pending')->index();         // pending | active | rejected
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(
                ['user_id', 'related_user_id', 'relationship_type'],
                'user_relationships_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_relationships');
        Schema::dropIfExists('settings');
    }
};
