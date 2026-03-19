<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Regras de Precificação para Formações VEPL.
     * 
     * Sistema flexível de precificação que considera critérios ministeriais,
     * demográficos, temporais e socioeconômicos para oferecer valores
     * justos e acessíveis às diferentes realidades dos participantes.
     */
    public function up(): void
    {
        Schema::create('event_price_rules', function (Blueprint $table) {
            // ── Identificação e Vinculação ────────────────────────────────────────
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade')->comment('Formação vinculada');
            $table->foreignId('registration_segment_id')->nullable()->constrained('event_registration_segments')->onDelete('cascade')->comment('Segmento específico (opcional)');
            
            // ── Definição da Regra ────────────────────────────────────────────────
            $table->string('name')->comment('Nome descritivo da regra');
            $table->text('description')->nullable()->comment('Descrição detalhada dos critérios');
            $table->enum('rule_type', [
                'ministry_level',           // Nível ministerial
                'ordination_status',        // Status de ordenação
                'church_membership',        // Membresia batista
                'age_bracket',              // Faixa etária
                'marital_status',           // Estado civil
                'geographic_region',        // Região geográfica
                'early_bird',               // Inscrição antecipada
                'group_discount',           // Desconto por grupo
                'financial_assistance',     // Assistência financeira
                'student_discount',         // Desconto estudantil
                'senior_discount',          // Desconto terceira idade
                'repeat_participant',       // Participante recorrente
                'referral_bonus',           // Bônus por indicação
                'loyalty_member',           // Membro fidelidade
                'promotional_code'          // Código promocional
            ])->comment('Tipo da regra de precificação');

            // ── Critérios Ministeriais ────────────────────────────────────────────
            $table->json('ministry_levels')->nullable()->comment('Níveis ministeriais aplicáveis: ["leigo", "diacono", "pastor"]');
            $table->boolean('requires_ordination')->nullable()->comment('Exige ordenação (null = não se aplica)');
            $table->integer('min_ministry_years')->nullable()->comment('Anos mínimos de ministério');
            $table->integer('max_ministry_years')->nullable()->comment('Anos máximos de ministério');
            $table->enum('church_membership_type', ['membro', 'congregado', 'visitante', 'transferido'])->nullable()->comment('Tipo de membresia');
            $table->json('denomination_affiliations')->nullable()->comment('Afiliações denominacionais: ["batista_brasileira", "batista_independente"]');

            // ── Critérios Demográficos ────────────────────────────────────────────
            $table->integer('min_age')->nullable()->comment('Idade mínima');
            $table->integer('max_age')->nullable()->comment('Idade máxima');
            $table->enum('gender_restriction', ['all', 'male', 'female'])->nullable()->comment('Restrição de gênero');
            $table->json('marital_status_options')->nullable()->comment('Estados civis aplicáveis');
            $table->json('education_levels')->nullable()->comment('Níveis educacionais: ["fundamental", "medio", "superior"]');

            // ── Critérios Geográficos e Socioeconômicos ───────────────────────────
            $table->json('geographic_regions')->nullable()->comment('Regiões/estados aplicáveis');
            $table->json('city_types')->nullable()->comment('Tipos de cidade: ["capital", "interior", "rural"]');
            $table->decimal('max_monthly_income', 10, 2)->nullable()->comment('Renda familiar máxima');
            $table->boolean('requires_income_verification')->default(false)->comment('Exige comprovação de renda');

            // ── Critérios Temporais ───────────────────────────────────────────────
            $table->timestamp('valid_from')->nullable()->comment('Válida a partir de');
            $table->timestamp('valid_until')->nullable()->comment('Válida até');
            $table->boolean('weekends_only')->default(false)->comment('Aplicável apenas em finais de semana');
            $table->json('valid_weekdays')->nullable()->comment('Dias da semana válidos: [1,2,3,4,5,6,7]');

            // ── Critérios de Grupo e Volume ───────────────────────────────────────
            $table->integer('min_group_size')->nullable()->comment('Tamanho mínimo do grupo');
            $table->integer('max_group_size')->nullable()->comment('Tamanho máximo do grupo');
            $table->boolean('same_church_required')->default(false)->comment('Membros da mesma igreja');
            $table->boolean('pastoral_endorsement_required')->default(false)->comment('Exige carta pastoral');

            // ── Configuração de Preços e Descontos ────────────────────────────────
            $table->enum('discount_type', ['fixed_amount', 'percentage', 'override_price', 'free'])->default('percentage')->comment('Tipo de desconto');
            $table->decimal('discount_value', 10, 2)->default(0)->comment('Valor do desconto (% ou R$)');
            $table->decimal('override_price', 10, 2)->nullable()->comment('Preço substituto (ignora preço base)');
            $table->decimal('max_discount_amount', 10, 2)->nullable()->comment('Valor máximo de desconto em R$');
            $table->boolean('stackable')->default(false)->comment('Cumulativo com outras regras');

            // ── Limites de Uso ────────────────────────────────────────────────────
            $table->integer('usage_limit')->nullable()->comment('Limite total de usos');
            $table->integer('usage_per_user')->nullable()->comment('Usos por pessoa');
            $table->integer('current_usage')->default(0)->comment('Uso atual da regra');

            // ── Códigos Promocionais ──────────────────────────────────────────────
            $table->string('promo_code', 50)->nullable()->comment('Código promocional');
            $table->boolean('requires_approval')->default(false)->comment('Exige aprovação manual');
            $table->text('justification_required')->nullable()->comment('Justificativa obrigatória');

            // ── Controle de Prioridade e Ativação ─────────────────────────────────
            $table->unsignedSmallInteger('priority')->default(100)->comment('Prioridade (menor número = maior prioridade)');
            $table->boolean('is_active')->default(true)->comment('Regra ativa');
            $table->boolean('auto_apply')->default(false)->comment('Aplicação automática');
            $table->boolean('visible_to_user')->default(true)->comment('Visível na interface do usuário');

            // ── Configurações Avançadas ───────────────────────────────────────────
            $table->json('additional_criteria')->nullable()->comment('Critérios adicionais em JSON');
            $table->json('required_documentation')->nullable()->comment('Documentação exigida para aplicação');
            $table->text('terms_and_conditions')->nullable()->comment('Termos específicos da regra');

            $table->timestamps();

            // ── Índices para Performance Otimizada ───────────────────────────────
            $table->index(['event_id', 'priority', 'is_active'], 'epr_event_priority_idx');
            $table->index(['event_id', 'rule_type'], 'epr_event_type_idx');
            $table->index('registration_segment_id', 'epr_segment_idx');
            $table->index(['valid_from', 'valid_until'], 'epr_validity_idx');
            $table->index('promo_code', 'epr_promo_idx');
            $table->index(['auto_apply', 'is_active'], 'epr_auto_active_idx');
            $table->index(['requires_ordination', 'rule_type'], 'epr_ordination_type_idx');
            $table->index('stackable', 'epr_stackable_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_price_rules');
    }
};