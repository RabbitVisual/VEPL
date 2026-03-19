<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Participantes de Formações VEPL.
     * 
     * Dados específicos de cada participante inscrito em formações pastorais,
     * incluindo informações ministeriais, educacionais, eclesiásticas e de
     * acompanhamento pedagógico individual.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            // ── Identificação e Vinculação ────────────────────────────────────────
            $table->id();
            $table->foreignId('registration_id')->constrained('event_registrations')->onDelete('cascade')->comment('Inscrição vinculada');
            $table->foreignId('registration_segment_id')->nullable()->constrained('event_registration_segments')->onDelete('set null')->comment('Trilha específica');
            
            // ── Dados Pessoais ────────────────────────────────────────────────────
            $table->string('full_name')->comment('Nome completo');
            $table->string('preferred_name', 100)->nullable()->comment('Nome preferido/social');
            $table->string('email')->comment('E-mail principal');
            $table->string('phone', 20)->nullable()->comment('Telefone');
            $table->string('whatsapp', 20)->nullable()->comment('WhatsApp');
            $table->date('birth_date')->comment('Data de nascimento');
            $table->enum('gender', ['M', 'F', 'O'])->nullable()->comment('Gênero');
            $table->string('document_type', 20)->default('cpf')->comment('Tipo de documento');
            $table->string('document_number', 30)->nullable()->comment('Número do documento');

            // ── Dados Ministeriais e Eclesiásticos ────────────────────────────────
            $table->string('ministry_title')->nullable()->comment('Título ministerial (Pastor, Diácono, etc.)');
            $table->enum('ordination_status', ['nao_ordenado', 'candidato', 'licenciado', 'ordenado'])->default('nao_ordenado')->comment('Status de ordenação');
            $table->date('ordination_date')->nullable()->comment('Data de ordenação');
            $table->string('ordaining_church')->nullable()->comment('Igreja que ordenou');
            $table->integer('ministry_experience_years')->nullable()->comment('Anos de experiência ministerial');
            $table->text('ministry_background')->nullable()->comment('Histórico ministerial');

            // ── Informações Eclesiásticas ─────────────────────────────────────────
            $table->string('home_church')->nullable()->comment('Igreja de origem');
            $table->string('church_role')->nullable()->comment('Função na igreja');
            $table->enum('membership_status', ['membro', 'congregado', 'visitante', 'transferido', 'disciplinado'])->default('membro')->comment('Status de membresia');
            $table->string('baptist_convention')->nullable()->comment('Convenção batista de afiliação');
            $table->date('baptism_date')->nullable()->comment('Data do batismo');
            $table->string('baptizing_church')->nullable()->comment('Igreja do batismo');

            // ── Formação Acadêmica e Teológica ────────────────────────────────────
            $table->enum('education_level', ['fundamental', 'medio', 'superior', 'especializacao', 'mestrado', 'doutorado'])->nullable()->comment('Nível educacional');
            $table->string('academic_institution')->nullable()->comment('Instituição de ensino');
            $table->string('theological_education')->nullable()->comment('Formação teológica');
            $table->string('current_studies')->nullable()->comment('Estudos atuais');
            $table->json('previous_vepl_courses')->nullable()->comment('Cursos VEPL anteriores');

            // ── Situação Profissional ─────────────────────────────────────────────
            $table->string('profession')->nullable()->comment('Profissão secular');
            $table->enum('ministry_dedication', ['secular', 'bivocacional', 'tempo_parcial', 'tempo_integral'])->default('secular')->comment('Dedicação ministerial');
            $table->string('workplace')->nullable()->comment('Local de trabalho');
            $table->decimal('monthly_income', 10, 2)->nullable()->comment('Renda mensal (para bolsas)');
            $table->boolean('financial_assistance_requested')->default(false)->comment('Solicitou auxílio financeiro');

            // ── Endereço e Localização ────────────────────────────────────────────
            $table->string('address')->nullable()->comment('Endereço completo');
            $table->string('city', 100)->nullable()->comment('Cidade');
            $table->string('state', 2)->nullable()->comment('Estado (UF)');
            $table->string('zip_code', 10)->nullable()->comment('CEP');
            $table->string('country', 2)->default('BR')->comment('País');

            // ── Contato de Emergência ─────────────────────────────────────────────
            $table->string('emergency_contact_name')->nullable()->comment('Nome do contato de emergência');
            $table->string('emergency_contact_phone', 20)->nullable()->comment('Telefone de emergência');
            $table->string('emergency_relationship', 50)->nullable()->comment('Parentesco/relação');

            // ── Acomodação e Necessidades Especiais ───────────────────────────────
            $table->boolean('needs_accommodation')->default(false)->comment('Precisa de hospedagem');
            $table->enum('accommodation_preference', ['single', 'shared', 'family'])->nullable()->comment('Preferência de acomodação');
            $table->json('dietary_restrictions')->nullable()->comment('Restrições alimentares');
            $table->json('accessibility_needs')->nullable()->comment('Necessidades especiais');
            $table->text('health_considerations')->nullable()->comment('Considerações de saúde');
            $table->json('medication_schedule')->nullable()->comment('Medicamentos/horários');

            // ── Networking e Interação ────────────────────────────────────────────
            $table->boolean('opt_in_networking')->default(true)->comment('Aceita networking');
            $table->json('ministry_interests')->nullable()->comment('Áreas de interesse ministerial');
            $table->json('languages_spoken')->nullable()->comment('Idiomas falados');
            $table->boolean('available_for_mentoring')->default(false)->comment('Disponível para mentorar');
            $table->boolean('seeking_mentor')->default(false)->comment('Busca mentor');

            // ── Presença e Check-in ───────────────────────────────────────────────
            $table->boolean('checked_in')->default(false)->comment('Realizou check-in');
            $table->timestamp('checked_in_at')->nullable()->comment('Data/hora do check-in');
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->onDelete('set null')->comment('Quem fez o check-in');
            $table->json('session_attendance')->nullable()->comment('Presença por sessão');
            $table->decimal('total_attendance_hours', 5, 2)->nullable()->comment('Total de horas de presença');

            // ── Avaliação e Performance ───────────────────────────────────────────
            $table->json('assessment_scores')->nullable()->comment('Notas de avaliações');
            $table->decimal('final_grade', 5, 2)->nullable()->comment('Nota final');
            $table->enum('performance_level', ['insuficiente', 'regular', 'bom', 'otimo', 'excelente'])->nullable()->comment('Nível de desempenho');
            $table->text('instructor_feedback')->nullable()->comment('Feedback dos instrutores');
            $table->json('peer_evaluations')->nullable()->comment('Avaliações dos pares');

            // ── Formulários Customizados ──────────────────────────────────────────
            $table->json('custom_responses')->nullable()->comment('Respostas a campos personalizados');
            $table->json('registration_answers')->nullable()->comment('Respostas do formulário de inscrição');
            $table->text('motivation_statement')->nullable()->comment('Declaração de motivação');
            $table->text('ministry_goals')->nullable()->comment('Objetivos ministeriais');

            // ── Comunicação e Preferências ────────────────────────────────────────
            $table->json('communication_preferences')->nullable()->comment('Preferências de comunicação');
            $table->boolean('marketing_opt_in')->default(false)->comment('Aceita comunicações de marketing');
            $table->string('preferred_language', 5)->default('pt_BR')->comment('Idioma preferido');
            $table->enum('preferred_contact_method', ['email', 'phone', 'whatsapp', 'sms'])->default('email')->comment('Método de contato preferido');

            // ── Certificados e Documentação ───────────────────────────────────────
            $table->string('badge_design_preference')->nullable()->comment('Preferência de design do crachá');
            $table->json('certificate_customizations')->nullable()->comment('Personalizações do certificado');
            $table->boolean('digital_certificate_only')->default(false)->comment('Apenas certificado digital');

            // ── Metadados e Auditoria ─────────────────────────────────────────────
            $table->json('registration_metadata')->nullable()->comment('Metadados da inscrição');
            $table->timestamp('profile_last_updated')->nullable()->comment('Última atualização do perfil');
            $table->text('administrative_notes')->nullable()->comment('Observações administrativas');
            
            $table->timestamps();
            $table->softDeletes();

            // ── Índices Estratégicos ──────────────────────────────────────────────
            $table->index(['registration_id', 'registration_segment_id'], 'p_reg_segment_idx');
            $table->index('email', 'p_email_idx');
            $table->index(['checked_in', 'checked_in_at'], 'p_checkin_idx');
            $table->index(['ordination_status', 'ministry_experience_years'], 'p_ordination_exp_idx');
            $table->index(['membership_status', 'baptist_convention'], 'p_membership_conv_idx');
            $table->index(['performance_level', 'final_grade'], 'p_performance_idx');
            $table->index(['needs_accommodation', 'accommodation_preference'], 'p_accommodation_idx');
            $table->index('document_number', 'p_document_idx');
            $table->index(['ministry_title', 'ministry_experience_years'], 'p_ministry_idx');
            $table->index(['opt_in_networking', 'available_for_mentoring'], 'p_networking_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};