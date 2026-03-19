<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Formações e Eventos Educacionais da VEPL.
     * 
     * Tabela central para gerenciamento de formações pastorais, retiros espirituais,
     * congressos, seminários bíblicos, workshops ministeriais e demais eventos
     * educacionais voltados para pastores, líderes e profissionais do ministério cristão.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            // ── Identificação e Metadados ─────────────────────────────────────────
            $table->id();
            $table->string('title')->comment('Título da formação/evento');
            $table->string('slug')->unique()->comment('URL amigável');
            $table->text('description')->nullable()->comment('Descrição breve para listagens');
            $table->longText('description_long')->nullable()->comment('Descrição completa para página de detalhes');
            
            // ── Categorização e Tipagem ───────────────────────────────────────────
            $table->foreignId('event_type_id')->nullable()->constrained('event_types')->onDelete('set null')->comment('Tipo de formação');
            $table->json('educational_objectives')->nullable()->comment('Objetivos educacionais e competências');
            $table->json('target_audience')->nullable()->comment('Público alvo específico');
            
            // ── Recursos Visuais ──────────────────────────────────────────────────
            $table->string('banner_path')->nullable()->comment('Imagem principal/banner');
            $table->string('logo_path')->nullable()->comment('Logo específico do evento');
            $table->json('theme_config')->nullable()->comment('Configuração visual do tema');

            // ── Programação e Cronograma ──────────────────────────────────────────
            $table->dateTime('start_date')->comment('Data e hora de início');
            $table->dateTime('end_date')->nullable()->comment('Data e hora de término');
            $table->json('schedule')->nullable()->comment('Cronograma detalhado de atividades');
            $table->integer('workload_hours')->nullable()->comment('Carga horária total');
            
            // ── Localização e Modalidade ──────────────────────────────────────────
            $table->string('location')->nullable()->comment('Local físico do evento');
            $table->json('location_data')->nullable()->comment('Dados detalhados do local (endereço, coordenadas)');
            $table->enum('delivery_mode', ['presencial', 'online', 'hibrido'])->default('presencial')->comment('Modalidade de entrega');
            $table->string('meeting_url')->nullable()->comment('URL da reunião online (Zoom, Teams, etc.)');
            
            // ── Capacidade e Limitações ───────────────────────────────────────────
            $table->integer('capacity')->nullable()->comment('Capacidade máxima (null = ilimitado)');
            $table->integer('min_participants')->nullable()->comment('Mínimo de participantes para realização');
            $table->tinyInteger('max_per_registration')->unsigned()->default(5)->comment('Máximo de participantes por inscrição');
            
            // ── Controle de Acesso e Visibilidade ─────────────────────────────────
            $table->enum('visibility', ['public', 'members', 'both', 'ministers_only'])->default('public')->comment('Visibilidade do evento');
            $table->boolean('requires_ordination')->default(false)->comment('Exige ordenação ministerial');
            $table->boolean('requires_ministry_experience')->default(false)->comment('Exige experiência ministerial');
            $table->integer('min_ministry_years')->nullable()->comment('Anos mínimos de ministério');

            // ── Status e Fluxo de Aprovação ───────────────────────────────────────
            $table->enum('status', ['draft', 'awaiting_approval', 'approved', 'published', 'in_progress', 'completed', 'cancelled'])->default('draft')->comment('Status do evento');
            $table->boolean('requires_administrative_approval')->default(false)->comment('Exige aprovação da coordenação');
            $table->timestamp('approved_at')->nullable()->comment('Data/hora da aprovação');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->comment('Usuário que aprovou');
            $table->text('approval_notes')->nullable()->comment('Observações da aprovação/rejeição');

            // ── Inscrições e Prazos ───────────────────────────────────────────────
            $table->dateTime('registration_start_date')->nullable()->comment('Início das inscrições');
            $table->dateTime('registration_end_date')->nullable()->comment('Fim das inscrições');
            $table->boolean('allow_late_registration')->default(false)->comment('Permite inscrição após prazo');
            $table->json('form_fields')->nullable()->comment('Campos customizados do formulário');
            $table->json('default_required_fields')->nullable()->comment('Campos obrigatórios padrão');

            // ── Certificação e Documentação ───────────────────────────────────────
            $table->boolean('issues_certificate')->default(false)->comment('Emite certificado de participação');
            $table->string('certificate_template')->nullable()->comment('Template de certificado');
            $table->boolean('issues_continuing_education_credit')->default(false)->comment('Oferece crédito educação continuada');
            $table->decimal('credit_hours', 5, 2)->nullable()->comment('Horas de crédito educacional');

            // ── Recursos Didáticos e Materiais ────────────────────────────────────
            $table->json('materials_included')->nullable()->comment('Materiais inclusos (apostilas, livros, etc.)');
            $table->string('bibliography')->nullable()->comment('Bibliografia recomendada');
            $table->text('prerequisites')->nullable()->comment('Pré-requisitos necessários');

            // ── Aspectos Financeiros ──────────────────────────────────────────────
            $table->boolean('is_free')->default(true)->comment('Evento gratuito');
            $table->decimal('base_price', 10, 2)->nullable()->comment('Preço base para referência');
            $table->foreignId('treasury_campaign_id')->nullable()->constrained('campaigns')->onDelete('set null')->comment('Campanha da tesouraria');

            // ── Integração com Outros Módulos ─────────────────────────────────────
            $table->foreignId('ministry_id')->nullable()->constrained('ministries')->onDelete('set null')->comment('Ministério organizador');
            $table->foreignId('ministry_plan_id')->nullable()->comment('Plano ministerial vinculado');
            $table->foreignId('setlist_id')->nullable()->comment('Setlist de louvor (se aplicável)');

            // ── Configurações Avançadas ───────────────────────────────────────────
            $table->json('options')->nullable()->comment('Opções: badges, check-in, tickets, etc.');
            $table->boolean('is_featured')->default(false)->comment('Destaque na página inicial');
            $table->enum('difficulty_level', ['iniciante', 'intermediario', 'avancado', 'especializado'])->default('iniciante')->comment('Nível de dificuldade');
            
            // ── Contato e Responsabilidade ────────────────────────────────────────
            $table->string('contact_name', 150)->nullable()->comment('Nome do responsável');
            $table->string('contact_email', 150)->nullable()->comment('E-mail de contato');
            $table->string('contact_phone', 30)->nullable()->comment('Telefone de contato');
            $table->string('contact_whatsapp', 30)->nullable()->comment('WhatsApp de contato');

            // ── Controle de Auditoria ─────────────────────────────────────────────
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('Criador do evento');
            $table->foreignId('last_modified_by')->nullable()->constrained('users')->onDelete('set null')->comment('Última modificação por');
            $table->timestamp('last_significant_change')->nullable()->comment('Última mudança significativa');
            
            $table->timestamps();
            $table->softDeletes();

            // ── Índices Estratégicos para Performance ─────────────────────────────
            $table->index('slug', 'ev_slug_idx');
            $table->index(['status', 'visibility'], 'ev_status_visibility_idx');
            $table->index('start_date', 'ev_start_date_idx');
            $table->index(['start_date', 'end_date'], 'ev_date_range_idx');
            $table->index('event_type_id', 'ev_type_idx');
            $table->index('ministry_id', 'ev_ministry_idx');
            $table->index(['is_featured', 'status'], 'ev_featured_idx');
            $table->index('delivery_mode', 'ev_delivery_idx');
            $table->index('requires_ordination', 'ev_ordination_req_idx');
            $table->index(['registration_start_date', 'registration_end_date'], 'ev_reg_period_idx');
        });

        // ── Foreign Keys Condicionais (módulos opcionais) ─────────────────────────
        $this->addConditionalForeignKeys();
    }

    /**
     * Adiciona foreign keys apenas se as tabelas existirem
     */
    private function addConditionalForeignKeys(): void
    {
        if (Schema::hasTable('ministry_plans')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreign('ministry_plan_id')->references('id')->on('ministry_plans')->onDelete('set null');
            });
        }

        if (Schema::hasTable('worship_setlists')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreign('setlist_id')->references('id')->on('worship_setlists')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};