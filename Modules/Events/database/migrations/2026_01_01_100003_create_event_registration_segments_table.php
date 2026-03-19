<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Segmentos de Inscrição para Formações VEPL.
     * 
     * Representa trilhas específicas de participação, perfis ministeriais
     * ou categorias de liderança dentro de uma formação. Permite
     * segmentação por nível pastoral, função eclesiástica ou especialização.
     */
    public function up(): void
    {
        Schema::create('event_registration_segments', function (Blueprint $table) {
            // ── Identificação e Vinculação ────────────────────────────────────────
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade')->comment('Formação vinculada');
            
            // ── Definição do Segmento ─────────────────────────────────────────────
            $table->string('name')->comment('Nome da trilha/segmento');
            $table->text('description')->nullable()->comment('Descrição detalhada da trilha');
            $table->string('subtitle', 150)->nullable()->comment('Subtítulo ou função');
            
            // ── Classificação Ministerial ─────────────────────────────────────────
            $table->enum('ministry_level', ['leigo', 'lider', 'diacono', 'ministro', 'pastor'])->default('leigo')->comment('Nível ministerial alvo');
            $table->json('target_roles')->nullable()->comment('Funções específicas: ["pastor_titular", "pastor_auxiliar", "evangelista"]');
            $table->boolean('requires_ordination')->default(false)->comment('Exige ordenação para esta trilha');
            $table->integer('min_ministry_experience_years')->nullable()->comment('Experiência mínima em anos');

            // ── Critérios Demográficos ────────────────────────────────────────────
            $table->enum('gender_restriction', ['all', 'male', 'female'])->default('all')->comment('Restrição de gênero');
            $table->integer('min_age')->nullable()->comment('Idade mínima');
            $table->integer('max_age')->nullable()->comment('Idade máxima');
            $table->boolean('married_only')->default(false)->comment('Apenas casados');

            // ── Capacidade e Logística ────────────────────────────────────────────
            $table->integer('capacity')->nullable()->comment('Capacidade específica desta trilha');
            $table->integer('min_participants')->nullable()->comment('Mínimo para formação da trilha');
            $table->decimal('price', 10, 2)->nullable()->comment('Preço específico desta trilha');
            $table->boolean('requires_accommodation')->default(false)->comment('Exige hospedagem');

            // ── Recursos Educacionais Específicos ─────────────────────────────────
            $table->json('specific_materials')->nullable()->comment('Materiais específicos desta trilha');
            $table->text('learning_objectives')->nullable()->comment('Objetivos de aprendizagem específicos');
            $table->json('competencies')->nullable()->comment('Competências desenvolvidas');
            $table->decimal('credit_hours', 5, 2)->nullable()->comment('Horas de crédito específicas');

            // ── Formulário e Documentação ─────────────────────────────────────────
            $table->json('required_fields')->nullable()->comment('Campos obrigatórios específicos');
            $table->json('additional_form_fields')->nullable()->comment('Campos extras do formulário');
            $table->json('required_documents')->nullable()->comment('Documentos exigidos: ["diploma", "carta_ordenacao"]');
            
            // ── Certificação Diferenciada ─────────────────────────────────────────
            $table->string('certificate_title')->nullable()->comment('Título específico do certificado');
            $table->boolean('professional_certification')->default(false)->comment('Certificação profissional reconhecida');
            $table->string('certification_body')->nullable()->comment('Órgão certificador');

            // ── Configuração de Preços ────────────────────────────────────────────
            $table->enum('pricing_strategy', ['fixed', 'tier_based', 'sliding_scale', 'free'])->default('fixed')->comment('Estratégia de precificação');
            $table->json('price_rules_config')->nullable()->comment('Configuração de regras de preço');
            $table->boolean('allows_partial_payment')->default(false)->comment('Permite pagamento parcelado');
            $table->integer('max_installments')->nullable()->comment('Máximo de parcelas');

            // ── Organização e Apresentação ────────────────────────────────────────
            $table->unsignedSmallInteger('display_order')->default(0)->comment('Ordem de exibição');
            $table->boolean('is_active')->default(true)->comment('Segmento ativo');
            $table->boolean('is_featured')->default(false)->comment('Destaque especial');
            $table->string('icon', 100)->nullable()->comment('Ícone para identificação visual');
            $table->string('color', 30)->nullable()->comment('Cor de identificação');

            $table->timestamps();

            // ── Índices para Consultas Eficientes ────────────────────────────────
            $table->index(['event_id', 'display_order'], 'ers_event_order_idx');
            $table->index(['event_id', 'is_active'], 'ers_event_active_idx');
            $table->index('ministry_level', 'ers_ministry_level_idx');
            $table->index(['requires_ordination', 'ministry_level'], 'ers_ordination_level_idx');
            $table->index(['gender_restriction', 'ministry_level'], 'ers_gender_level_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registration_segments');
    }
};