<?php

namespace Modules\Events\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // ── Identificação e Metadados ─────────────────────────────────────────
        'title',
        'slug',
        'description',
        'description_long',
        
        // ── Categorização e Tipagem ───────────────────────────────────────────
        'event_type_id',
        'educational_objectives',
        'target_audience',
        
        // ── Recursos Visuais ──────────────────────────────────────────────────
        'banner_path',
        'logo_path',
        'theme_config',

        // ── Programação e Cronograma ──────────────────────────────────────────
        'start_date',
        'end_date',
        'schedule',
        'workload_hours',
        
        // ── Localização e Modalidade ──────────────────────────────────────────
        'location',
        'location_data',
        'delivery_mode',
        'meeting_url',
        
        // ── Capacidade e Limitações ───────────────────────────────────────────
        'capacity',
        'min_participants',
        'max_per_registration',
        
        // ── Controle de Acesso e Visibilidade ─────────────────────────────────
        'visibility',
        'requires_ordination',
        'requires_ministry_experience',
        'min_ministry_years',

        // ── Status e Fluxo de Aprovação ───────────────────────────────────────
        'status',
        'requires_administrative_approval',
        'approved_at',
        'approved_by',
        'approval_notes',

        // ── Inscrições e Prazos ───────────────────────────────────────────────
        'registration_start_date',
        'registration_end_date',
        'allow_late_registration',
        'form_fields',
        'default_required_fields',

        // ── Certificação e Documentação ───────────────────────────────────────
        'issues_certificate',
        'certificate_template',
        'issues_continuing_education_credit',
        'credit_hours',

        // ── Recursos Didáticos e Materiais ────────────────────────────────────
        'materials_included',
        'bibliography',
        'prerequisites',

        // ── Aspectos Financeiros ──────────────────────────────────────────────
        'is_free',
        'base_price',
        'treasury_campaign_id',

        // ── Integração com Outros Módulos ─────────────────────────────────────
        'ministry_id',
        'ministry_plan_id',
        'setlist_id',

        // ── Configurações Avançadas ───────────────────────────────────────────
        'options',
        'is_featured',
        'difficulty_level',
        
        // ── Contato e Responsabilidade ────────────────────────────────────────
        'contact_name',
        'contact_email',
        'contact_phone',
        'contact_whatsapp',

        // ── Controle de Auditoria ─────────────────────────────────────────────
        'created_by',
        'last_modified_by',
        'last_significant_change',
    ];

    protected $casts = [
        // ── Datas e Timestamps ────────────────────────────────────────────────
        'start_date'                   => 'datetime',
        'end_date'                     => 'datetime',
        'registration_start_date'      => 'datetime',
        'registration_end_date'        => 'datetime',
        'approved_at'                  => 'datetime',
        'last_significant_change'      => 'datetime',
        
        // ── Arrays e JSON ─────────────────────────────────────────────────────
        'educational_objectives'       => 'array',
        'target_audience'              => 'array',
        'location_data'                => 'array',
        'schedule'                     => 'array',
        'form_fields'                  => 'array',
        'default_required_fields'      => 'array',
        'materials_included'           => 'array',
        'options'                      => 'array',
        'theme_config'                 => 'array',
        
        // ── Números ───────────────────────────────────────────────────────────
        'capacity'                     => 'integer',
        'min_participants'             => 'integer',
        'max_per_registration'         => 'integer',
        'workload_hours'               => 'integer',
        'min_ministry_years'           => 'integer',
        'credit_hours'                 => 'decimal:2',
        'base_price'                   => 'decimal:2',
        
        // ── Booleans ──────────────────────────────────────────────────────────
        'requires_ordination'                => 'boolean',
        'requires_ministry_experience'       => 'boolean',
        'requires_administrative_approval'   => 'boolean',
        'allow_late_registration'            => 'boolean',
        'issues_certificate'                 => 'boolean',
        'issues_continuing_education_credit' => 'boolean',
        'is_free'                            => 'boolean',
        'is_featured'                        => 'boolean',
    ];

    /**
     * Get theme config attribute ensuring default structure.
     */
    public function getThemeConfigAttribute($value)
    {
        $config = $value ? json_decode($value, true) : [];
        if (!is_array($config)) {
            $config = [];
        }

        return [
            'theme'           => $config['theme'] ?? 'modern',
            'primary_color'   => $config['primary_color'] ?? '#4F46E5',
            'secondary_color' => $config['secondary_color'] ?? '#111827',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Status de Formações VEPL
    // ─────────────────────────────────────────────────────────────────────────
    const STATUS_DRAFT              = 'draft';
    const STATUS_AWAITING_APPROVAL   = 'awaiting_approval';
    const STATUS_APPROVED           = 'approved';
    const STATUS_PUBLISHED          = 'published';
    const STATUS_IN_PROGRESS        = 'in_progress';
    const STATUS_COMPLETED          = 'completed';
    const STATUS_CANCELLED          = 'cancelled';

    // ─────────────────────────────────────────────────────────────────────────
    // Visibility (Visibilidade de Formações)
    // ─────────────────────────────────────────────────────────────────────────
    const VISIBILITY_PUBLIC        = 'public';
    const VISIBILITY_MEMBERS       = 'members';
    const VISIBILITY_BOTH          = 'both';
    const VISIBILITY_MINISTERS_ONLY = 'ministers_only';

    // ─────────────────────────────────────────────────────────────────────────
    // Público Alvo Ministerial (VEPL)
    // ─────────────────────────────────────────────────────────────────────────
    const AUDIENCE_PASTORS              = 'pastors';              // Pastores
    const AUDIENCE_MINISTERS            = 'ministers';            // Ministros ordenados
    const AUDIENCE_DEACONS              = 'deacons';              // Diáconos
    const AUDIENCE_LAY_LEADERS          = 'lay_leaders';          // Líderes leigos
    const AUDIENCE_YOUTH_LEADERS        = 'youth_leaders';        // Líderes de jovens
    const AUDIENCE_MUSIC_MINISTERS      = 'music_ministers';      // Ministros de música
    const AUDIENCE_WOMEN_LEADERS        = 'women_leaders';        // Líderes femininas
    const AUDIENCE_SEMINARY_STUDENTS    = 'seminary_students';    // Estudantes de seminário
    const AUDIENCE_CHURCH_PLANTERS      = 'church_planters';      // Plantadores de igreja
    const AUDIENCE_MISSIONARIES         = 'missionaries';         // Missionários
    const AUDIENCE_PASTORAL_COUPLES     = 'pastoral_couples';     // Casais pastorais
    const AUDIENCE_DENOMINATIONAL_LEADERS = 'denominational_leaders'; // Líderes denominacionais
    const AUDIENCE_ALL_MINISTERS        = 'all_ministers';        // Todos os ministros

    public static function getAudienceOptions(): array
    {
        return [
            self::AUDIENCE_ALL_MINISTERS        => 'Todos os Ministros',
            self::AUDIENCE_PASTORS              => 'Pastores',
            self::AUDIENCE_MINISTERS            => 'Ministros Ordenados',
            self::AUDIENCE_DEACONS              => 'Diáconos',
            self::AUDIENCE_LAY_LEADERS          => 'Líderes Leigos',
            self::AUDIENCE_YOUTH_LEADERS        => 'Líderes de Jovens',
            self::AUDIENCE_MUSIC_MINISTERS      => 'Ministros de Música',
            self::AUDIENCE_WOMEN_LEADERS        => 'Líderes Femininas',
            self::AUDIENCE_SEMINARY_STUDENTS    => 'Estudantes de Seminário',
            self::AUDIENCE_CHURCH_PLANTERS      => 'Plantadores de Igreja',
            self::AUDIENCE_MISSIONARIES         => 'Missionários',
            self::AUDIENCE_PASTORAL_COUPLES     => 'Casais Pastorais',
            self::AUDIENCE_DENOMINATIONAL_LEADERS => 'Líderes Denominacionais',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Modalidades de Entrega
    // ─────────────────────────────────────────────────────────────────────────
    const DELIVERY_MODE_PRESENCIAL = 'presencial';
    const DELIVERY_MODE_ONLINE     = 'online';
    const DELIVERY_MODE_HIBRIDO    = 'hibrido';

    public static function getDeliveryModeOptions(): array
    {
        return [
            self::DELIVERY_MODE_PRESENCIAL => 'Presencial',
            self::DELIVERY_MODE_ONLINE     => 'Online',
            self::DELIVERY_MODE_HIBRIDO    => 'Híbrido',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Níveis de Dificuldade
    // ─────────────────────────────────────────────────────────────────────────
    const DIFFICULTY_INICIANTE      = 'iniciante';
    const DIFFICULTY_INTERMEDIARIO  = 'intermediario';
    const DIFFICULTY_AVANCADO       = 'avancado';
    const DIFFICULTY_ESPECIALIZADO  = 'especializado';

    public static function getDifficultyLevelOptions(): array
    {
        return [
            self::DIFFICULTY_INICIANTE     => 'Iniciante',
            self::DIFFICULTY_INTERMEDIARIO => 'Intermediário',
            self::DIFFICULTY_AVANCADO      => 'Avançado',
            self::DIFFICULTY_ESPECIALIZADO => 'Especializado',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Recurrence
    // ─────────────────────────────────────────────────────────────────────────
    const RECURRENCE_WEEKLY  = 'weekly';
    const RECURRENCE_MONTHLY = 'monthly';
    const RECURRENCE_YEARLY  = 'yearly';

    public static function getRecurrenceOptions(): array
    {
        return [
            ''                        => 'Sem recorrência (evento único)',
            self::RECURRENCE_WEEKLY   => 'Semanal',
            self::RECURRENCE_MONTHLY  => 'Mensal',
            self::RECURRENCE_YEARLY   => 'Anual',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Fields control: quais campos são exibidos/obrigatórios no formulário
    // Valores possíveis para cada chave: 'required' | 'optional' | 'disabled'
    // ─────────────────────────────────────────────────────────────────────────
    public static function getAvailableFormFields(): array
    {
        return [
            'name'       => 'Nome completo',
            'email'      => 'E-mail',
            'phone'      => 'Telefone / WhatsApp',
            'birth_date' => 'Data de nascimento',
            'gender'     => 'Gênero',
            'cpf'        => 'CPF',
            'rg'         => 'RG',
            'church'     => 'Igreja que congrega',
            'city'       => 'Cidade',
            'state'      => 'Estado',
            'address'    => 'Endereço completo',
            'shirt_size' => 'Tamanho da camiseta',
            'food_restrictions' => 'Restrições alimentares',
            'emergency_contact' => 'Contato de emergência',
        ];
    }

    /** Default required fields status when not set */
    public static function defaultRequiredFields(): array
    {
        return [
            'name'       => 'required',
            'email'      => 'required',
            'phone'      => 'optional',
            'birth_date' => 'optional',
            'gender'     => 'disabled',
            'cpf'        => 'disabled',
            'rg'         => 'disabled',
            'church'     => 'disabled',
            'city'       => 'disabled',
            'state'      => 'disabled',
            'address'    => 'disabled',
            'shirt_size' => 'disabled',
            'food_restrictions' => 'disabled',
            'emergency_contact' => 'disabled',
        ];
    }

    /** Get the effective required_fields for this event (merged with defaults) */
    public function getEffectiveRequiredFields(): array
    {
        $defaults = self::defaultRequiredFields();
        $saved = is_array($this->default_required_fields) ? $this->default_required_fields : [];
        return array_merge($defaults, $saved);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Page options defaults
    // ─────────────────────────────────────────────────────────────────────────
    public static function defaultOptions(): array
    {
        return [
            'has_badge'       => false,
            'has_certificate' => false,
            'has_checkin'     => true,
            'has_ticket'      => true,
            'show_schedule'   => true,
            'show_speakers'   => true,
            'show_about'      => true,
            'show_location'   => true,
            'show_map'        => true,
            'show_capacity'   => true,
            'show_cover'      => true,
            'show_contact'    => true,
            'show_audience'   => true,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Boot
    // ─────────────────────────────────────────────────────────────────────────
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────────────────────────────────────
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(\Modules\Ministries\App\Models\Ministry::class, 'ministry_id');
    }

    public function ministryPlan(): BelongsTo
    {
        return $this->belongsTo(\Modules\Ministries\App\Models\MinistryPlan::class, 'ministry_plan_id');
    }

    public function setlist(): BelongsTo
    {
        return $this->belongsTo(\Modules\Worship\App\Models\WorshipSetlist::class, 'setlist_id');
    }

    public function treasuryCampaign(): BelongsTo
    {
        return $this->belongsTo(\Modules\Treasury\App\Models\Campaign::class, 'treasury_campaign_id');
    }

    public function speakers(): HasMany
    {
        return $this->hasMany(EventSpeaker::class, 'event_id')->orderBy('order');
    }

    public function registrationSegments(): HasMany
    {
        return $this->hasMany(EventRegistrationSegment::class, 'event_id')->orderBy('order');
    }

    public function priceRules(): HasMany
    {
        return $this->hasMany(EventPriceRule::class, 'event_id')->orderBy('order');
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(EventCoupon::class, 'event_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(EventBatch::class, 'event_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }

    public function confirmedRegistrations(): HasMany
    {
        return $this->registrations()->where('status', EventRegistration::STATUS_CONFIRMED);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(EventCertificate::class, 'event_id');
    }

    public function badges(): HasMany
    {
        return $this->hasMany(EventBadge::class, 'event_id');
    }

    public function getBadgeTemplate(): ?EventBadge
    {
        return $this->badges()->first();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────────────────────────────────

    public function getTotalParticipantsAttribute(): int
    {
        return \Modules\Events\App\Models\Participant::whereHas('registration', function ($q) {
            $q->where('event_id', $this->id)->where('status', 'confirmed');
        })->count();
    }

    // Método removido - duplicado com versão mais nova abaixo

    // Método removido - duplicado com versão mais nova abaixo

    public function getFormattedTimeAttribute(): ?string
    {
        return $this->start_date?->format('H:i');
    }

    public function getFormattedDateAttribute(): ?string
    {
        return $this->start_date?->translatedFormat('d/m/Y');
    }

    public function getIsActiveAttribute(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) {
            return false;
        }
        if ($this->end_date) {
            return $this->end_date->isFuture() || $this->end_date->isToday();
        }
        if ($this->start_date) {
            return $this->start_date->greaterThanOrEqualTo(now()->subHours(6));
        }
        return false;
    }

    public function getIsRegistrationOpenAttribute(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) return false;
        if ($this->registration_deadline && $this->registration_deadline->isPast()) return false;
        return true;
    }

    public function getAudienceDisplayAttribute(): string
    {
        if (empty($this->target_audience)) return 'Todos';
        $options = self::getAudienceOptions();
        $labels = array_map(fn($key) => $options[$key] ?? $key, (array) $this->target_audience);
        return implode(', ', $labels);
    }

    public function getRecurrenceDisplayAttribute(): ?string
    {
        return match ($this->recurrence_type) {
            self::RECURRENCE_WEEKLY  => 'Semanal',
            self::RECURRENCE_MONTHLY => 'Mensal',
            self::RECURRENCE_YEARLY  => 'Anual',
            default                  => null,
        };
    }

    public function getOptionsAttribute($value): array
    {
        $arr = is_array($value) ? $value : (is_string($value) ? json_decode($value, true) : []);
        return array_merge(self::defaultOptions(), is_array($arr) ? $arr : []);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Option helpers
    // ─────────────────────────────────────────────────────────────────────────
    public function hasBadgeEnabled(): bool          { return (bool) ($this->options['has_badge'] ?? false); }
    public function hasCertificateEnabled(): bool    { return (bool) ($this->options['has_certificate'] ?? false); }
    public function hasCheckinEnabled(): bool        { return (bool) ($this->options['has_checkin'] ?? true); }
    public function hasTicketEnabled(): bool         { return (bool) ($this->options['has_ticket'] ?? true); }
    public function showScheduleEnabled(): bool      { return (bool) ($this->options['show_schedule'] ?? true); }
    public function showSpeakersEnabled(): bool      { return (bool) ($this->options['show_speakers'] ?? true); }
    public function showAboutEnabled(): bool         { return (bool) ($this->options['show_about'] ?? true); }
    public function showLocationEnabled(): bool      { return (bool) ($this->options['show_location'] ?? true); }
    public function showMapEnabled(): bool           { return (bool) ($this->options['show_map'] ?? true); }
    public function showCapacityEnabled(): bool      { return (bool) ($this->options['show_capacity'] ?? true); }
    public function showCoverEnabled(): bool         { return (bool) ($this->options['show_cover'] ?? true); }
    public function showContactEnabled(): bool       { return (bool) ($this->options['show_contact'] ?? true); }
    public function showAudienceEnabled(): bool      { return (bool) ($this->options['show_audience'] ?? true); }

    // ─────────────────────────────────────────────────────────────────────────
    // Capacity helpers
    // ─────────────────────────────────────────────────────────────────────────
    public function hasCapacityAvailable(int $additionalParticipants = 1): bool
    {
        if ($this->capacity === null) return true;
        return ($this->total_participants + $additionalParticipants) <= $this->capacity;
    }

    public function isFree(): bool
    {
        $hasPaidBatches = $this->batches()->where('price', '>', 0)->exists();
        $hasPaidRules   = $this->priceRules()->where('price', '>', 0)->exists();
        return !$hasPaidBatches && !$hasPaidRules;
    }

    public function hasBatches(): bool
    {
        return $this->batches()->exists();
    }

    public function hasRegistrationSegments(): bool
    {
        return $this->registrationSegments()->exists();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopePublic($query)
    {
        return $query->whereIn('visibility', [self::VISIBILITY_PUBLIC, self::VISIBILITY_BOTH]);
    }

    public function scopeMembers($query)
    {
        return $query->whereIn('visibility', [self::VISIBILITY_MEMBERS, self::VISIBILITY_BOTH]);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function ($q) {
                $q->where('start_date', '>=', now())
                  ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Métodos de Conveniência para VEPL
    // ─────────────────────────────────────────────────────────────────────────
    
    public function isAwaitingApproval(): bool
    {
        return $this->status === self::STATUS_AWAITING_APPROVAL;
    }

    public function isApproved(): bool
    {
        return !is_null($this->approved_at) && in_array($this->status, [
            self::STATUS_APPROVED, 
            self::STATUS_PUBLISHED, 
            self::STATUS_IN_PROGRESS, 
            self::STATUS_COMPLETED
        ]);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function requiresMinistryExperience(): bool
    {
        return $this->requires_ministry_experience && $this->min_ministry_years > 0;
    }

    public function isOnline(): bool
    {
        return $this->delivery_mode === self::DELIVERY_MODE_ONLINE;
    }

    public function isHybrid(): bool
    {
        return $this->delivery_mode === self::DELIVERY_MODE_HIBRIDO;
    }

    public function issuesCertificate(): bool
    {
        return $this->issues_certificate;
    }

    public function offersContinuingEducationCredit(): bool
    {
        return $this->issues_continuing_education_credit && $this->credit_hours > 0;
    }

    public function isRestrictedToMinisters(): bool
    {
        return $this->visibility === self::VISIBILITY_MINISTERS_ONLY;
    }

    public function requiresOrdination(): bool
    {
        return $this->requires_ordination;
    }

    public function hasWorkloadHours(): bool
    {
        return !is_null($this->workload_hours) && $this->workload_hours > 0;
    }

    public function getFormattedWorkloadAttribute(): string
    {
        if (!$this->workload_hours) return 'Não informado';
        
        $hours = $this->workload_hours;
        if ($hours < 24) {
            return "{$hours}h";
        }
        
        $days = intval($hours / 8);
        $remainingHours = $hours % 8;
        
        if ($remainingHours > 0) {
            return "{$days}d {$remainingHours}h";
        }
        
        return "{$days}d";
    }

    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Rascunho',
            self::STATUS_AWAITING_APPROVAL => 'Aguardando Aprovação',
            self::STATUS_APPROVED => 'Aprovado',
            self::STATUS_PUBLISHED => 'Publicado',
            self::STATUS_IN_PROGRESS => 'Em Andamento',
            self::STATUS_COMPLETED => 'Concluído',
            self::STATUS_CANCELLED => 'Cancelado',
            default => 'Status Desconhecido'
        };
    }

    public function getVisibilityDisplayAttribute(): string
    {
        return match($this->visibility) {
            self::VISIBILITY_PUBLIC => 'Público',
            self::VISIBILITY_MEMBERS => 'Membros',
            self::VISIBILITY_BOTH => 'Ambos',
            self::VISIBILITY_MINISTERS_ONLY => 'Apenas Ministros',
            default => 'Indefinido'
        };
    }

    public function getDeliveryModeDisplayAttribute(): string
    {
        return match($this->delivery_mode) {
            self::DELIVERY_MODE_PRESENCIAL => 'Presencial',
            self::DELIVERY_MODE_ONLINE => 'Online',
            self::DELIVERY_MODE_HIBRIDO => 'Híbrido',
            default => 'Não especificado'
        };
    }
}
