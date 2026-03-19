<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cupons Promocionais para Formações VEPL.
     * 
     * Sistema de códigos promocionais e cupons de desconto voltado
     * para acessibilidade educacional e campanhas específicas de
     * incentivo à formação pastoral e ministerial.
     */
    public function up(): void
    {
        Schema::create('event_coupons', function (Blueprint $table) {
            // ── Identificação e Básicos ───────────────────────────────────────────
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade')->comment('Formação vinculada');
            $table->string('code', 50)->comment('Código promocional');
            $table->string('name')->comment('Nome descritivo do cupom');
            $table->text('description')->nullable()->comment('Descrição detalhada');

            // ── Tipo e Finalidade ─────────────────────────────────────────────────
            $table->enum('coupon_type', [
                'scholarship',              // Bolsa de estudos
                'ministry_support',         // Apoio ministerial
                'early_bird',              // Antecipação
                'group_discount',          // Desconto grupo
                'loyalty_reward',          // Recompensa fidelidade
                'referral_bonus',          // Bônus indicação
                'hardship_assistance',     // Auxílio necessidade
                'promotional',             // Promocional geral
                'partnership',             // Parcerias institucionais
                'staff_discount'           // Desconto funcionários
            ])->default('promotional')->comment('Tipo de cupom');
            
            $table->enum('purpose', [
                'accessibility',            // Acessibilidade financeira
                'incentive',               // Incentivo participação
                'retention',               // Retenção de alunos
                'acquisition',             // Aquisição novos alunos
                'partnership',             // Parcerias
                'emergency_aid'            // Auxílio emergencial
            ])->default('incentive')->comment('Finalidade principal');

            // ── Configuração de Desconto ──────────────────────────────────────────
            $table->enum('discount_type', ['percentage', 'fixed_amount', 'free_registration'])->comment('Tipo de desconto');
            $table->decimal('discount_value', 10, 2)->comment('Valor do desconto');
            $table->decimal('max_discount_amount', 10, 2)->nullable()->comment('Desconto máximo em valor');
            $table->decimal('min_order_amount', 10, 2)->nullable()->comment('Valor mínimo do pedido');
            $table->boolean('applies_to_fees')->default(false)->comment('Aplica a taxas administrativas');

            // ── Elegibilidade e Restrições ────────────────────────────────────────
            $table->json('eligibility_criteria')->nullable()->comment('Critérios de elegibilidade');
            $table->json('ministry_level_restrictions')->nullable()->comment('Restrições por nível ministerial');
            $table->json('geographic_restrictions')->nullable()->comment('Restrições geográficas');
            $table->boolean('first_time_only')->default(false)->comment('Apenas primeira participação VEPL');
            $table->boolean('requires_documentation')->default(false)->comment('Exige documentação');
            $table->json('required_documents')->nullable()->comment('Documentos exigidos');

            // ── Período de Validade ───────────────────────────────────────────────
            $table->timestamp('valid_from')->nullable()->comment('Válido a partir de');
            $table->timestamp('valid_until')->nullable()->comment('Válido até');
            $table->json('valid_weekdays')->nullable()->comment('Dias da semana válidos');
            $table->time('valid_time_start')->nullable()->comment('Horário inicial válido');
            $table->time('valid_time_end')->nullable()->comment('Horário final válido');

            // ── Limites de Uso ────────────────────────────────────────────────────
            $table->integer('total_usage_limit')->nullable()->comment('Limite total de usos');
            $table->integer('per_user_limit')->nullable()->comment('Limite por usuário');
            $table->integer('per_church_limit')->nullable()->comment('Limite por igreja');
            $table->integer('current_usage')->default(0)->comment('Uso atual');
            $table->json('usage_tracking')->nullable()->comment('Rastreamento de uso');

            // ── Aprovação e Controle ──────────────────────────────────────────────
            $table->boolean('requires_approval')->default(false)->comment('Exige aprovação para uso');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->comment('Aprovado por');
            $table->timestamp('approved_at')->nullable()->comment('Data de aprovação');
            $table->text('approval_justification')->nullable()->comment('Justificativa da aprovação');
            $table->enum('approval_status', ['pending', 'approved', 'rejected', 'suspended'])->nullable()->comment('Status de aprovação');

            // ── Fonte e Origem ────────────────────────────────────────────────────
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('Criado por');
            $table->string('source_campaign')->nullable()->comment('Campanha de origem');
            $table->string('partner_organization')->nullable()->comment('Organização parceira');
            $table->text('funding_source')->nullable()->comment('Fonte de financiamento');
            $table->json('sponsor_information')->nullable()->comment('Informações do patrocinador');

            // ── Comunicação e Marketing ───────────────────────────────────────────
            $table->text('marketing_message')->nullable()->comment('Mensagem de marketing');
            $table->string('display_label')->nullable()->comment('Rótulo para exibição');
            $table->string('success_message')->nullable()->comment('Mensagem de sucesso');
            $table->boolean('publicly_visible')->default(false)->comment('Visível publicamente');
            $table->boolean('shareable')->default(false)->comment('Pode ser compartilhado');

            // ── Combinabilidade ───────────────────────────────────────────────────
            $table->boolean('stackable')->default(false)->comment('Pode ser combinado com outros');
            $table->json('incompatible_with')->nullable()->comment('Incompatível com outros cupons');
            $table->integer('priority_level')->default(0)->comment('Prioridade de aplicação');
            $table->boolean('exclusive')->default(false)->comment('Uso exclusivo (não combina)');

            // ── Rastreamento e Analytics ──────────────────────────────────────────
            $table->integer('total_clicks')->default(0)->comment('Total de cliques/visualizações');
            $table->integer('total_attempts')->default(0)->comment('Total de tentativas de uso');
            $table->integer('successful_uses')->default(0)->comment('Usos bem-sucedidos');
            $table->decimal('total_discount_given', 12, 2)->default(0)->comment('Total de desconto concedido');
            $table->json('usage_analytics')->nullable()->comment('Dados de analytics de uso');

            // ── Status e Controle ─────────────────────────────────────────────────
            $table->boolean('is_active')->default(true)->comment('Cupom ativo');
            $table->enum('status', ['draft', 'active', 'paused', 'expired', 'depleted', 'cancelled'])->default('draft')->comment('Status do cupom');
            $table->text('deactivation_reason')->nullable()->comment('Motivo da desativação');
            $table->timestamp('last_used_at')->nullable()->comment('Último uso');

            $table->timestamps();
            $table->softDeletes();

            // ── Índices para Performance ──────────────────────────────────────────
            $table->unique(['event_id', 'code'], 'ecoup_event_code_unique');
            $table->index(['code', 'is_active'], 'ecoup_code_active_idx');
            $table->index(['event_id', 'coupon_type'], 'ecoup_event_type_idx');
            $table->index(['valid_from', 'valid_until'], 'ecoup_validity_idx');
            $table->index(['requires_approval', 'approval_status'], 'ecoup_approval_idx');
            $table->index('partner_organization', 'ecoup_partner_idx');
            $table->index(['stackable', 'priority_level'], 'ecoup_stackable_idx');
            $table->index(['status', 'is_active'], 'ecoup_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_coupons');
    }
};