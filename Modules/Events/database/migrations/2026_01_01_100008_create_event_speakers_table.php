<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Preletores e Facilitadores de Formações VEPL.
     * 
     * Gestão de palestrantes, pastores convidados, facilitadores e
     * especialistas que ministram nas formações educacionais.
     */
    public function up(): void
    {
        Schema::create('event_speakers', function (Blueprint $table) {
            // ── Identificação Básica ──────────────────────────────────────────────
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade')->comment('Formação vinculada');
            $table->string('full_name')->comment('Nome completo do preletor');
            $table->string('display_name', 100)->nullable()->comment('Nome para exibição');

            // ── Título e Qualificação ─────────────────────────────────────────────
            $table->string('ministerial_title')->nullable()->comment('Título ministerial (Pastor, Dr., Rev.)');
            $table->string('academic_titles')->nullable()->comment('Títulos acadêmicos');
            $table->enum('speaker_type', [
                'pastor_principal',         // Pastor principal
                'pastor_convidado',         // Pastor convidado
                'teologo',                  // Teólogo
                'especialista',             // Especialista
                'facilitador',              // Facilitador
                'mentor',                   // Mentor
                'moderador',                // Moderador
                'tradutor'                  // Tradutor/intérprete
            ])->default('pastor_convidado')->comment('Tipo de participação');

            // ── Biografia e Credenciais ───────────────────────────────────────────
            $table->text('biography')->nullable()->comment('Biografia resumida');
            $table->text('ministry_experience')->nullable()->comment('Experiência ministerial');
            $table->string('current_position')->nullable()->comment('Posição atual');
            $table->string('affiliated_church')->nullable()->comment('Igreja afiliada');
            $table->string('denomination')->nullable()->comment('Denominação/convenção');
            $table->json('qualifications')->nullable()->comment('Qualificações e certificações');
            $table->json('specialization_areas')->nullable()->comment('Áreas de especialização');

            // ── Formação Acadêmica ────────────────────────────────────────────────
            $table->string('theological_education')->nullable()->comment('Formação teológica');
            $table->string('highest_degree')->nullable()->comment('Maior titulação');
            $table->string('alma_mater')->nullable()->comment('Instituição de formação');
            $table->json('publications')->nullable()->comment('Publicações e obras');
            $table->integer('years_teaching')->nullable()->comment('Anos de experiência docente');

            // ── Recursos Visuais ──────────────────────────────────────────────────
            $table->string('photo_path')->nullable()->comment('Foto do preletor');
            $table->string('banner_image')->nullable()->comment('Imagem de banner');
            $table->string('profile_color', 30)->nullable()->comment('Cor de identificação');

            // ── Programação e Participação ────────────────────────────────────────
            $table->json('session_assignments')->nullable()->comment('Sessões/palestras designadas');
            $table->text('session_topics')->nullable()->comment('Tópicos das palestras');
            $table->integer('allocated_minutes')->nullable()->comment('Tempo total alocado em minutos');
            $table->enum('participation_mode', ['presencial', 'online', 'hibrido'])->default('presencial')->comment('Modo de participação');
            $table->unsignedSmallInteger('display_order')->default(0)->comment('Ordem de apresentação');

            // ── Contato e Logística ───────────────────────────────────────────────
            $table->string('contact_email')->nullable()->comment('E-mail de contato');
            $table->string('contact_phone', 20)->nullable()->comment('Telefone de contato');
            $table->string('assistant_name')->nullable()->comment('Nome do assistente');
            $table->string('assistant_contact', 20)->nullable()->comment('Contato do assistente');
            
            // ── Hospedagem e Acomodação ───────────────────────────────────────────
            $table->boolean('requires_accommodation')->default(false)->comment('Precisa de hospedagem');
            $table->enum('accommodation_type', ['hotel', 'guest_house', 'host_family', 'own_arrangement'])->nullable()->comment('Tipo de hospedagem');
            $table->json('dietary_preferences')->nullable()->comment('Preferências alimentares');
            $table->text('special_requirements')->nullable()->comment('Requisitos especiais');

            // ── Aspectos Financeiros ──────────────────────────────────────────────
            $table->decimal('honorarium_amount', 10, 2)->nullable()->comment('Valor dos honorários');
            $table->enum('payment_method_preference', ['pix', 'transferencia', 'cheque', 'dinheiro'])->nullable()->comment('Forma de pagamento preferida');
            $table->boolean('travel_expenses_covered')->default(false)->comment('Despesas de viagem cobertas');
            $table->decimal('estimated_travel_cost', 10, 2)->nullable()->comment('Custo estimado de viagem');

            // ── Recursos Técnicos ─────────────────────────────────────────────────
            $table->json('av_requirements')->nullable()->comment('Requisitos audiovisuais');
            $table->boolean('requires_live_stream')->default(false)->comment('Exige transmissão ao vivo');
            $table->json('presentation_materials')->nullable()->comment('Materiais de apresentação');
            $table->boolean('allows_recording')->default(true)->comment('Permite gravação');

            // ── Avaliação e Feedback ──────────────────────────────────────────────
            $table->decimal('participant_rating', 3, 2)->nullable()->comment('Avaliação dos participantes');
            $table->integer('total_evaluations')->default(0)->comment('Total de avaliações recebidas');
            $table->text('participant_feedback')->nullable()->comment('Feedback dos participantes');
            $table->text('organizer_notes')->nullable()->comment('Observações da organização');

            // ── Comunicação e Marketing ───────────────────────────────────────────
            $table->boolean('featured_speaker')->default(false)->comment('Preletor destaque');
            $table->text('social_media_bio')->nullable()->comment('Bio para redes sociais');
            $table->json('social_media_links')->nullable()->comment('Links de redes sociais');
            $table->boolean('available_for_interviews')->default(false)->comment('Disponível para entrevistas');
            $table->string('media_kit_url')->nullable()->comment('URL do kit de mídia');

            // ── Status e Confirmação ──────────────────────────────────────────────
            $table->enum('confirmation_status', ['invited', 'confirmed', 'tentative', 'declined', 'cancelled'])->default('invited')->comment('Status de confirmação');
            $table->timestamp('confirmed_at')->nullable()->comment('Data de confirmação');
            $table->timestamp('invitation_sent_at')->nullable()->comment('Convite enviado em');
            $table->text('decline_reason')->nullable()->comment('Motivo da recusa');

            $table->timestamps();
            $table->softDeletes();

            // ── Índices para Consultas Eficientes ────────────────────────────────
            $table->index(['event_id', 'display_order'], 'es_event_order_idx');
            $table->index(['event_id', 'speaker_type'], 'es_event_type_idx');
            $table->index('confirmation_status', 'es_confirmation_idx');
            $table->index(['featured_speaker', 'event_id'], 'es_featured_idx');
            $table->index('ministerial_title', 'es_title_idx');
            $table->index(['requires_accommodation', 'accommodation_type'], 'es_accommodation_idx');
            $table->index(['participation_mode', 'requires_live_stream'], 'es_mode_stream_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_speakers');
    }
};