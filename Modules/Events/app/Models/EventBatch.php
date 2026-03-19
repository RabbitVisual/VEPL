<?php

namespace Modules\Events\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // ── Identificação e Vinculação ────────────────────────────────────────
        'event_id',
        'name',
        'description',
        'batch_type',

        // ── Precificação e Valor ──────────────────────────────────────────────
        'price',
        'pricing_strategy',
        'original_price',
        'discount_percentage',
        'price_justification',

        // ── Período de Vigência ───────────────────────────────────────────────
        'sale_start_date',
        'sale_end_date',
        'auto_transition',
        'next_batch_id',

        // ── Capacidade e Disponibilidade ──────────────────────────────────────
        'total_capacity',
        'available_spots',
        'reserved_spots',
        'sold_spots',
        'overselling_allowed',
        'overselling_limit',

        // ── Critérios de Elegibilidade ────────────────────────────────────────
        'eligibility_criteria',
        'requires_approval',
        'invitation_only',
        'target_audience',

        // ── Benefícios e Vantagens ────────────────────────────────────────────
        'included_benefits',
        'bonus_materials',
        'priority_seating',
        'exclusive_content_access',
        'special_conditions',

        // ── Pagamento e Facilidades ───────────────────────────────────────────
        'allows_installments',
        'max_installments',
        'installment_interest_rate',
        'grace_period_days',
        'partial_payment_allowed',

        // ── Marketing e Comunicação ───────────────────────────────────────────
        'marketing_label',
        'urgency_message',
        'color_theme',
        'badge_text',
        'is_featured',

        // ── Controle de Estado ────────────────────────────────────────────────
        'status',
        'is_visible',
        'is_default',
        'display_order',

        // ── Limites e Restrições ──────────────────────────────────────────────
        'min_purchase_quantity',
        'max_purchase_quantity',
        'per_user_limit',
        'geographic_restrictions',

        // ── Automação e Triggers ──────────────────────────────────────────────
        'automation_rules',
        'auto_activate_at',
        'auto_deactivate_at',
        'low_stock_threshold',
    ];

    protected $casts = [
        // ── Decimais ──────────────────────────────────────────────────────────
        'price'                      => 'decimal:2',
        'original_price'             => 'decimal:2',
        'discount_percentage'        => 'decimal:2',
        'installment_interest_rate'  => 'decimal:2',

        // ── Timestamps ────────────────────────────────────────────────────────
        'sale_start_date'            => 'datetime',
        'sale_end_date'              => 'datetime',
        'auto_activate_at'           => 'datetime',
        'auto_deactivate_at'         => 'datetime',

        // ── Inteiros ──────────────────────────────────────────────────────────
        'total_capacity'             => 'integer',
        'available_spots'            => 'integer',
        'reserved_spots'             => 'integer',
        'sold_spots'                 => 'integer',
        'overselling_limit'          => 'integer',
        'max_installments'           => 'integer',
        'grace_period_days'          => 'integer',
        'display_order'              => 'integer',
        'min_purchase_quantity'      => 'integer',
        'max_purchase_quantity'      => 'integer',
        'per_user_limit'             => 'integer',
        'low_stock_threshold'        => 'integer',

        // ── Arrays ────────────────────────────────────────────────────────────
        'eligibility_criteria'       => 'array',
        'target_audience'            => 'array',
        'included_benefits'          => 'array',
        'bonus_materials'            => 'array',
        'geographic_restrictions'    => 'array',
        'automation_rules'           => 'array',

        // ── Booleans ──────────────────────────────────────────────────────────
        'auto_transition'            => 'boolean',
        'overselling_allowed'        => 'boolean',
        'requires_approval'          => 'boolean',
        'invitation_only'            => 'boolean',
        'priority_seating'           => 'boolean',
        'exclusive_content_access'   => 'boolean',
        'allows_installments'        => 'boolean',
        'partial_payment_allowed'    => 'boolean',
        'is_featured'                => 'boolean',
        'is_visible'                 => 'boolean',
        'is_default'                 => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'batch_id');
    }

    public function nextBatch(): BelongsTo
    {
        return $this->belongsTo(EventBatch::class, 'auto_switch_to_batch_id');
    }
}
