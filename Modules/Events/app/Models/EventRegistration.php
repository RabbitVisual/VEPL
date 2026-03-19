<?php

namespace Modules\Events\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRegistration extends Model
{
    use SoftDeletes;

    protected $table = 'event_registrations';

    protected $fillable = [
        // ── Identificação e Rastreamento ──────────────────────────────────────
        'uuid',
        'registration_number',
        'event_id',
        'batch_id',
        'user_id',

        // ── Dados do Responsável ──────────────────────────────────────────────
        'responsible_name',
        'responsible_email',
        'responsible_phone',
        'responsible_church',
        'responsible_title',

        // ── Aspectos Financeiros ──────────────────────────────────────────────
        'subtotal_amount',
        'discount_amount',
        'total_amount',
        'applied_discounts',
        'currency',

        // ── Status e Fluxo de Aprovação ───────────────────────────────────────
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
        'approval_notes',

        // ── Integração com PaymentGateway ─────────────────────────────────────
        'payment_gateway_id',
        'payment_method',
        'payment_reference',
        'payment_metadata',
        'payment_due_date',
        'paid_at',

        // ── Presença e Check-in ───────────────────────────────────────────────
        'access_token',
        'qr_code_hash',
        'first_access_at',
        'last_activity_at',
        'attendance_record',
        'attendance_percentage',

        // ── Certificação e Conclusão ──────────────────────────────────────────
        'eligible_for_certificate',
        'certificate_issued_at',
        'certificate_number',
        'completion_status',
        'final_grade',

        // ── Acomodação e Logística ────────────────────────────────────────────
        'requires_accommodation',
        'accommodation_type',
        'dietary_restrictions',
        'accessibility_needs',
        'special_requests',

        // ── Networking e Relacionamentos ──────────────────────────────────────
        'opt_in_directory',
        'ministry_interests',
        'ministry_background',
        'available_for_mentoring',

        // ── Documentação e Conformidade ───────────────────────────────────────
        'submitted_documents',
        'documents_verified_at',
        'terms_accepted',
        'terms_accepted_at',
        'terms_version',

        // ── Comunicação e Feedback ────────────────────────────────────────────
        'communication_preferences',
        'participant_notes',
        'admin_notes',
        'feedback_submitted',
        'feedback_submitted_at',

        // ── Códigos Promocionais ──────────────────────────────────────────────
        'discount_code_used',
        'discount_code_amount',
        
        // ── Metadados e Auditoria ─────────────────────────────────────────────
        'registration_metadata',
        'registration_source',
        'user_agent',
        'ip_address',

        // ── Campos Legados (compatibilidade) ──────────────────────────────────
        'notes',
        'discount_code',
        'custom_responses',
        'ticket_hash',
        'checked_in_at',
    ];

    protected $casts = [
        // ── Decimais ──────────────────────────────────────────────────────────
        'subtotal_amount'       => 'decimal:2',
        'discount_amount'       => 'decimal:2',
        'total_amount'          => 'decimal:2',
        'discount_code_amount'  => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'final_grade'           => 'decimal:2',

        // ── Arrays ────────────────────────────────────────────────────────────
        'applied_discounts'            => 'array',
        'payment_metadata'             => 'array',
        'attendance_record'            => 'array',
        'dietary_restrictions'         => 'array',
        'accessibility_needs'          => 'array',
        'ministry_interests'           => 'array',
        'submitted_documents'          => 'array',
        'communication_preferences'    => 'array',
        'feedback_submitted'           => 'array',
        'registration_metadata'        => 'array',
        'custom_responses'             => 'array',

        // ── Timestamps ────────────────────────────────────────────────────────
        'submitted_at'          => 'datetime',
        'approved_at'           => 'datetime',
        'payment_due_date'      => 'datetime',
        'paid_at'               => 'datetime',
        'first_access_at'       => 'datetime',
        'last_activity_at'      => 'datetime',
        'certificate_issued_at' => 'datetime',
        'documents_verified_at' => 'datetime',
        'terms_accepted_at'     => 'datetime',
        'feedback_submitted_at' => 'datetime',
        'checked_in_at'         => 'datetime',

        // ── Booleans ──────────────────────────────────────────────────────────
        'requires_accommodation'   => 'boolean',
        'opt_in_directory'         => 'boolean',
        'available_for_mentoring'  => 'boolean',
        'eligible_for_certificate' => 'boolean',
        'terms_accepted'           => 'boolean',
        'marketing_opt_in'         => 'boolean',
    ];

    // Status constants for VEPL
    const STATUS_DRAFT              = 'draft';
    const STATUS_SUBMITTED          = 'submitted';
    const STATUS_UNDER_REVIEW       = 'under_review';
    const STATUS_APPROVED           = 'approved';
    const STATUS_PAYMENT_PENDING    = 'payment_pending';
    const STATUS_PAYMENT_PROCESSING = 'payment_processing';
    const STATUS_CONFIRMED          = 'confirmed';
    const STATUS_ACTIVE             = 'active';
    const STATUS_COMPLETED          = 'completed';
    const STATUS_CANCELLED          = 'cancelled';
    const STATUS_REFUNDED           = 'refunded';
    const STATUS_TRANSFERRED        = 'transferred';
    const STATUS_WAITLISTED         = 'waitlisted';

    // Backwards compatibility
    const STATUS_PENDING = 'payment_pending';

    /**
     * Get the event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the batch (lote)
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(EventBatch::class, 'batch_id');
    }

    /**
     * Get participants
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'registration_id');
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_CONFIRMED => 'Confirmada',
            self::STATUS_CANCELLED => 'Cancelada',
            self::STATUS_REFUNDED => 'Reembolsada',
            default => 'Pendente'
        };
    }

    /**
     * Scope for confirmed registrations
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope for pending registrations
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Get all payments
     */
    public function payments()
    {
        return $this->morphMany(\Modules\PaymentGateway\App\Models\Payment::class, 'payable');
    }

    /**
     * Get the latest payment
     */
    public function latestPayment()
    {
        return $this->morphOne(\Modules\PaymentGateway\App\Models\Payment::class, 'payable')->latestOfMany();
    }
}
