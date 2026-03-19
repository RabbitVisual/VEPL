<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lotes de Inscrição para Formações VEPL.
     * 
     * Sistema de comercialização por lotes que permite escalonamento de preços
     * baseado em datas, disponibilidade e estratégias de acessibilidade educacional.
     * Ideal para formações com diferentes momentos de precificação.
     */
    public function up(): void
    {
        Schema::create('event_batches', function (Blueprint $table) {
            // ── Identificação e Vinculação ────────────────────────────────────────
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade')->comment('Formação vinculada');
            
            // ── Definição do Lote ─────────────────────────────────────────────────
            $table->string('name')->comment('Nome do lote (ex: "1º Lote - Promocional")');
            $table->text('description')->nullable()->comment('Descrição das vantagens/características');
            $table->enum('batch_type', [
                'early_bird',               // Super antecipado
                'advance',                  // Antecipado
                'regular',                  // Regular
                'late',                     // Tardio
                'last_chance',              // Última oportunidade
                'promotional',              // Promocional
                'scholarship',              // Bolsas de estudo
                'group_discount'            // Desconto grupo
            ])->default('regular')->comment('Tipo de lote');

            // ── Precificação e Valor ──────────────────────────────────────────────
            $table->decimal('price', 10, 2)->default(0)->comment('Preço do lote');
            $table->enum('pricing_strategy', ['fixed', 'promotional', 'sliding_scale', 'scholarship'])->default('fixed')->comment('Estratégia de precificação');
            $table->decimal('original_price', 10, 2)->nullable()->comment('Preço original (para mostrar desconto)');
            $table->decimal('discount_percentage', 5, 2)->nullable()->comment('Percentual de desconto');
            $table->text('price_justification')->nullable()->comment('Justificativa do preço');

            // ── Período de Vigência ───────────────────────────────────────────────
            $table->timestamp('sale_start_date')->nullable()->comment('Início das vendas');
            $table->timestamp('sale_end_date')->nullable()->comment('Final das vendas');
            $table->boolean('auto_transition')->default(false)->comment('Transição automática para próximo lote');
            $table->foreignId('next_batch_id')->nullable()->comment('Próximo lote na sequência');

            // ── Capacidade e Disponibilidade ──────────────────────────────────────
            $table->integer('total_capacity')->nullable()->comment('Capacidade total do lote');
            $table->integer('available_spots')->nullable()->comment('Vagas disponíveis');
            $table->integer('reserved_spots')->default(0)->comment('Vagas reservadas');
            $table->integer('sold_spots')->default(0)->comment('Vagas vendidas');
            $table->boolean('overselling_allowed')->default(false)->comment('Permite sobrevenda');
            $table->integer('overselling_limit')->nullable()->comment('Limite de sobrevenda');

            // ── Critérios de Elegibilidade ────────────────────────────────────────
            $table->json('eligibility_criteria')->nullable()->comment('Critérios para acesso ao lote');
            $table->boolean('requires_approval')->default(false)->comment('Exige aprovação para acesso');
            $table->boolean('invitation_only')->default(false)->comment('Apenas por convite');
            $table->json('target_audience')->nullable()->comment('Público-alvo específico');

            // ── Benefícios e Vantagens ────────────────────────────────────────────
            $table->json('included_benefits')->nullable()->comment('Benefícios inclusos no lote');
            $table->json('bonus_materials')->nullable()->comment('Materiais bônus');
            $table->boolean('priority_seating')->default(false)->comment('Assentos prioritários');
            $table->boolean('exclusive_content_access')->default(false)->comment('Acesso a conteúdo exclusivo');
            $table->text('special_conditions')->nullable()->comment('Condições especiais');

            // ── Pagamento e Facilidades ───────────────────────────────────────────
            $table->boolean('allows_installments')->default(false)->comment('Permite parcelamento');
            $table->integer('max_installments')->nullable()->comment('Máximo de parcelas');
            $table->decimal('installment_interest_rate', 5, 2)->default(0)->comment('Taxa de juros parcelamento');
            $table->integer('grace_period_days')->nullable()->comment('Prazo de carência em dias');
            $table->boolean('partial_payment_allowed')->default(false)->comment('Permite pagamento parcial');

            // ── Marketing e Comunicação ───────────────────────────────────────────
            $table->string('marketing_label', 100)->nullable()->comment('Rótulo de marketing (ex: "Oferta Limitada")');
            $table->string('urgency_message')->nullable()->comment('Mensagem de urgência');
            $table->string('color_theme', 30)->nullable()->comment('Cor temática para UI');
            $table->string('badge_text', 50)->nullable()->comment('Texto do badge');
            $table->boolean('is_featured')->default(false)->comment('Destaque especial');

            // ── Controle de Estado ────────────────────────────────────────────────
            $table->enum('status', ['draft', 'scheduled', 'active', 'paused', 'sold_out', 'expired', 'cancelled'])->default('draft')->comment('Status do lote');
            $table->boolean('is_visible')->default(true)->comment('Visível na interface');
            $table->boolean('is_default')->default(false)->comment('Lote padrão');
            $table->unsignedSmallInteger('display_order')->default(0)->comment('Ordem de exibição');

            // ── Limites e Restrições ──────────────────────────────────────────────
            $table->integer('min_purchase_quantity')->default(1)->comment('Quantidade mínima por compra');
            $table->integer('max_purchase_quantity')->nullable()->comment('Quantidade máxima por compra');
            $table->integer('per_user_limit')->nullable()->comment('Limite por usuário');
            $table->json('geographic_restrictions')->nullable()->comment('Restrições geográficas');

            // ── Automação e Triggers ──────────────────────────────────────────────
            $table->json('automation_rules')->nullable()->comment('Regras de automação');
            $table->timestamp('auto_activate_at')->nullable()->comment('Ativação automática');
            $table->timestamp('auto_deactivate_at')->nullable()->comment('Desativação automática');
            $table->integer('low_stock_threshold')->nullable()->comment('Limite para alerta de estoque baixo');

            $table->timestamps();
            $table->softDeletes();

            // ── Índices Estratégicos ──────────────────────────────────────────────
            $table->index(['event_id', 'status', 'is_visible'], 'eb_event_status_visible_idx');
            $table->index(['event_id', 'display_order'], 'eb_event_order_idx');
            $table->index(['sale_start_date', 'sale_end_date'], 'eb_sale_period_idx');
            $table->index('batch_type', 'eb_type_idx');
            $table->index(['is_default', 'event_id'], 'eb_default_event_idx');
            $table->index(['auto_transition', 'next_batch_id'], 'eb_transition_idx');
            $table->index('status', 'eb_status_idx');
        });

        // FK para próximo lote (self-reference)
        Schema::table('event_batches', function (Blueprint $table) {
            $table->foreign('next_batch_id')->references('id')->on('event_batches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_batches');
    }
};