<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Certificados de Formações VEPL.
     * 
     * Sistema de certificação para formações pastorais com controle de
     * elegibilidade, templates personalizados e integração com órgãos
     * certificadores para educação continuada.
     */
    public function up(): void
    {
        Schema::create('event_certificates', function (Blueprint $table) {
            // ── Identificação e Vinculação ────────────────────────────────────────
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade')->comment('Formação vinculada');
            
            // ── Template e Design ─────────────────────────────────────────────────
            $table->string('template_name')->comment('Nome do template');
            $table->longText('template_html')->nullable()->comment('Template HTML do certificado');
            $table->longText('template_css')->nullable()->comment('Estilos CSS do certificado');
            $table->enum('certificate_type', [
                'participation',            // Participação
                'completion',               // Conclusão
                'achievement',              // Aproveitamento
                'continuing_education',     // Educação continuada
                'professional',             // Certificação profissional
                'accredited',               // Certificação credenciada
                'honorary'                  // Certificado honorário
            ])->default('participation')->comment('Tipo de certificação');

            // ── Critérios de Elegibilidade ────────────────────────────────────────
            $table->decimal('min_attendance_percentage', 5, 2)->default(75)->comment('Presença mínima em %');
            $table->decimal('min_grade', 5, 2)->nullable()->comment('Nota mínima para certificação');
            $table->boolean('requires_final_assessment')->default(false)->comment('Exige avaliação final');
            $table->json('completion_requirements')->nullable()->comment('Requisitos de conclusão');
            $table->boolean('requires_project_submission')->default(false)->comment('Exige entrega de projeto');

            // ── Credenciamento e Reconhecimento ───────────────────────────────────
            $table->string('issuing_institution')->nullable()->comment('Instituição emissora');
            $table->string('accreditation_body')->nullable()->comment('Órgão de credenciamento');
            $table->string('accreditation_number')->nullable()->comment('Número de credenciamento');
            $table->boolean('mec_recognized')->default(false)->comment('Reconhecido pelo MEC');
            $table->decimal('continuing_education_hours', 5, 2)->nullable()->comment('Horas de educação continuada');
            $table->string('certificate_category')->nullable()->comment('Categoria profissional');

            // ── Numeração e Autenticidade ─────────────────────────────────────────
            $table->string('certificate_prefix', 10)->default('VEPL')->comment('Prefixo dos certificados');
            $table->integer('next_certificate_number')->default(1)->comment('Próximo número sequencial');
            $table->string('validation_url_pattern')->nullable()->comment('Padrão da URL de validação');
            $table->boolean('digital_signature_enabled')->default(false)->comment('Assinatura digital habilitada');
            $table->json('signature_authorities')->nullable()->comment('Autoridades signatárias');

            // ── Conteúdo Textual ──────────────────────────────────────────────────
            $table->text('certificate_text')->nullable()->comment('Texto padrão do certificado');
            $table->text('achievement_description')->nullable()->comment('Descrição do aproveitamento');
            $table->text('skills_developed')->nullable()->comment('Competências desenvolvidas');
            $table->string('certificate_title')->nullable()->comment('Título específico');
            $table->text('legal_disclaimer')->nullable()->comment('Aviso legal');

            // ── Liberação e Disponibilização ──────────────────────────────────────
            $table->timestamp('release_date')->nullable()->comment('Data de liberação');
            $table->enum('release_trigger', [
                'manual',                   // Liberação manual
                'event_completion',         // Conclusão do evento
                'attendance_met',           // Presença atingida
                'assessment_passed',        // Avaliação aprovada
                'admin_approval'            // Aprovação administrativa
            ])->default('event_completion')->comment('Gatilho de liberação');
            $table->boolean('auto_generate')->default(true)->comment('Geração automática');
            $table->boolean('email_delivery')->default(true)->comment('Entrega por e-mail');

            // ── Configurações de Formato ──────────────────────────────────────────
            $table->enum('output_format', ['pdf', 'png', 'jpg', 'svg'])->default('pdf')->comment('Formato de saída');
            $table->enum('page_size', ['A4', 'Letter', 'A3', 'custom'])->default('A4')->comment('Tamanho da página');
            $table->enum('page_orientation', ['portrait', 'landscape'])->default('landscape')->comment('Orientação');
            $table->integer('print_quality_dpi')->default(300)->comment('Qualidade de impressão');

            // ── Personalização Visual ─────────────────────────────────────────────
            $table->string('background_image')->nullable()->comment('Imagem de fundo');
            $table->string('border_style')->nullable()->comment('Estilo da borda');
            $table->json('logo_configurations')->nullable()->comment('Configurações de logos');
            $table->json('font_settings')->nullable()->comment('Configurações de fontes');
            $table->string('color_theme', 30)->nullable()->comment('Tema de cores');

            // ── Validade e Renovação ──────────────────────────────────────────────
            $table->boolean('has_expiration')->default(false)->comment('Possui data de expiração');
            $table->integer('validity_months')->nullable()->comment('Validade em meses');
            $table->boolean('renewable')->default(false)->comment('Renovável');
            $table->text('renewal_requirements')->nullable()->comment('Requisitos para renovação');

            $table->timestamps();

            // ── Índices ────────────────────────────────────────────────────────────
            $table->index(['event_id', 'certificate_type'], 'ec_event_type_idx');
            $table->index('release_trigger', 'ec_trigger_idx');
            $table->index('auto_generate', 'ec_auto_idx');
            $table->index('accreditation_body', 'ec_accred_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_certificates');
    }
};