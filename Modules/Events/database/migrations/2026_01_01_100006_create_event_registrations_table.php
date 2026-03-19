<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Inscrições em Formações VEPL.
     * 
     * Registros de inscrições de pastores, líderes e participantes em formações
     * educacionais. Inclui controle completo de status, pagamentos, certificações
     * e integração com outros módulos do sistema.
     */
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            // ── Identificação e Rastreamento ──────────────────────────────────────
            $table->id();
            $table->uuid('uuid')->unique()->comment('Identificador público único');
            $table->string('registration_number')->unique()->comment('Número sequencial da inscrição');
            
            // ── Vinculações Principais ────────────────────────────────────────────
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade')->comment('Formação vinculada');
            $table->foreignId('batch_id')->nullable()->constrained('event_batches')->onDelete('set null')->comment('Lote de inscrição');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Usuário responsável');

            // ── Dados do Responsável pela Inscrição ───────────────────────────────
            $table->string('responsible_name')->comment('Nome do responsável');
            $table->string('responsible_email')->comment('E-mail do responsável');
            $table->string('responsible_phone', 20)->nullable()->comment('Telefone do responsável');
            $table->string('responsible_church')->nullable()->comment('Igreja de origem');
            $table->string('responsible_title')->nullable()->comment('Título ministerial');

            // ── Aspectos Financeiros ──────────────────────────────────────────────
            $table->decimal('subtotal_amount', 10, 2)->default(0)->comment('Subtotal antes de descontos');
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Total de descontos aplicados');
            $table->decimal('total_amount', 10, 2)->default(0)->comment('Valor final da inscrição');
            $table->json('applied_discounts')->nullable()->comment('Descontos aplicados');
            $table->string('currency', 3)->default('BRL')->comment('Moeda utilizada');

            // ── Status e Fluxo de Aprovação ───────────────────────────────────────
            $table->enum('status', [
                'draft',                    // Rascunho
                'submitted',                // Submetida
                'under_review',             // Em análise
                'approved',                 // Aprovada
                'payment_pending',          // Aguardando pagamento
                'payment_processing',       // Processando pagamento
                'confirmed',                // Confirmada
                'active',                   // Ativa/Participando
                'completed',                // Concluída
                'cancelled',                // Cancelada
                'refunded',                 // Reembolsada
                'transferred',              // Transferida
                'waitlisted'                // Lista de espera
            ])->default('draft')->comment('Status da inscrição');
            
            $table->timestamp('submitted_at')->nullable()->comment('Data/hora de submissão');
            $table->timestamp('approved_at')->nullable()->comment('Data/hora de aprovação');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->comment('Aprovador');
            $table->text('approval_notes')->nullable()->comment('Observações da aprovação');

            // ── Integração com PaymentGateway ─────────────────────────────────────
            $table->foreignId('payment_gateway_id')->nullable()->comment('Gateway de pagamento usado');
            $table->string('payment_method', 50)->nullable()->comment('Método de pagamento');
            $table->string('payment_reference')->nullable()->comment('Referência do pagamento');
            $table->json('payment_metadata')->nullable()->comment('Metadados do pagamento');
            $table->timestamp('payment_due_date')->nullable()->comment('Prazo para pagamento');
            $table->timestamp('paid_at')->nullable()->comment('Data/hora do pagamento');

            // ── Presença e Participação ───────────────────────────────────────────
            $table->string('access_token', 100)->unique()->nullable()->comment('Token para acesso direto');
            $table->string('qr_code_hash')->unique()->nullable()->comment('Hash para QR code');
            $table->timestamp('first_access_at')->nullable()->comment('Primeiro acesso');
            $table->timestamp('last_activity_at')->nullable()->comment('Última atividade');
            $table->json('attendance_record')->nullable()->comment('Registro de presença por sessão');
            $table->decimal('attendance_percentage', 5, 2)->nullable()->comment('Percentual de presença');

            // ── Certificação e Conclusão ──────────────────────────────────────────
            $table->boolean('eligible_for_certificate')->default(true)->comment('Elegível para certificado');
            $table->timestamp('certificate_issued_at')->nullable()->comment('Data de emissão do certificado');
            $table->string('certificate_number')->unique()->nullable()->comment('Número do certificado');
            $table->enum('completion_status', ['not_started', 'in_progress', 'completed', 'certified', 'incomplete'])->default('not_started')->comment('Status de conclusão');
            $table->decimal('final_grade', 5, 2)->nullable()->comment('Nota final (se aplicável)');

            // ── Acomodação e Logística ────────────────────────────────────────────
            $table->boolean('requires_accommodation')->default(false)->comment('Precisa de hospedagem');
            $table->enum('accommodation_type', ['single', 'shared', 'family', 'not_needed'])->nullable()->comment('Tipo de acomodação');
            $table->json('dietary_restrictions')->nullable()->comment('Restrições alimentares');
            $table->json('accessibility_needs')->nullable()->comment('Necessidades de acessibilidade');
            $table->text('special_requests')->nullable()->comment('Pedidos especiais');

            // ── Networking e Relacionamentos ──────────────────────────────────────
            $table->boolean('opt_in_directory')->default(false)->comment('Aceita aparecer no diretório de participantes');
            $table->json('ministry_interests')->nullable()->comment('Áreas de interesse ministerial');
            $table->text('ministry_background')->nullable()->comment('Contexto ministerial do participante');
            $table->boolean('available_for_mentoring')->default(false)->comment('Disponível para mentoria');

            // ── Documentação e Conformidade ───────────────────────────────────────
            $table->json('submitted_documents')->nullable()->comment('Documentos submetidos');
            $table->timestamp('documents_verified_at')->nullable()->comment('Data de verificação dos documentos');
            $table->boolean('terms_accepted')->default(false)->comment('Termos aceitos');
            $table->timestamp('terms_accepted_at')->nullable()->comment('Data de aceite dos termos');
            $table->string('terms_version', 10)->nullable()->comment('Versão dos termos aceitos');

            // ── Comunicação e Feedback ────────────────────────────────────────────
            $table->json('communication_preferences')->nullable()->comment('Preferências de comunicação');
            $table->text('participant_notes')->nullable()->comment('Observações do participante');
            $table->text('admin_notes')->nullable()->comment('Observações administrativas');
            $table->json('feedback_submitted')->nullable()->comment('Avaliação da formação');
            $table->timestamp('feedback_submitted_at')->nullable()->comment('Data do feedback');

            // ── Códigos Promocionais Utilizados ───────────────────────────────────
            $table->string('discount_code_used', 50)->nullable()->comment('Código promocional utilizado');
            $table->decimal('discount_code_amount', 10, 2)->default(0)->comment('Valor do desconto aplicado');
            
            // ── Metadados e Auditoria ─────────────────────────────────────────────
            $table->json('registration_metadata')->nullable()->comment('Metadados da inscrição');
            $table->string('registration_source', 50)->default('web')->comment('Origem da inscrição');
            $table->string('user_agent')->nullable()->comment('User agent do navegador');
            $table->string('ip_address', 45)->nullable()->comment('IP de origem');
            
            $table->timestamps();
            $table->softDeletes();

            // ── Índices Estratégicos para Performance ─────────────────────────────
            $table->index(['event_id', 'status'], 'er_event_status_idx');
            $table->index(['user_id', 'status'], 'er_user_status_idx');
            $table->index('batch_id', 'er_batch_idx');
            $table->index(['submitted_at', 'status'], 'er_submitted_status_idx');
            $table->index(['paid_at', 'total_amount'], 'er_payment_idx');
            $table->index('qr_code_hash', 'er_qr_idx');
            $table->index('access_token', 'er_access_idx');
            $table->index('registration_number', 'er_number_idx');
            $table->index(['completion_status', 'event_id'], 'er_completion_idx');
            $table->index(['requires_accommodation', 'event_id'], 'er_accommodation_idx');
            $table->index('payment_due_date', 'er_due_date_idx');
            $table->index(['certificate_issued_at', 'certificate_number'], 'er_certificate_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};