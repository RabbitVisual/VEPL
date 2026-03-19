<?php

namespace Modules\Ministries\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MinistryPlan extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_UNDER_ADMIN_REVIEW = 'under_admin_review';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_IN_EXECUTION = 'in_execution';

    public const STATUS_ARCHIVED = 'archived';

    public const PERIOD_ANNUAL = 'annual';

    public const PERIOD_SEMIANNUAL = 'semiannual';

    public const PERIOD_QUARTERLY = 'quarterly';

    public const PERIOD_MONTHLY = 'monthly';

    protected $fillable = [
        'ministry_id',
        'title',
        'period_year',
        'period_type',
        'period_start',
        'period_end',
        'objectives',
        'goals',
        'activities',
        'budget_requested',
        'budget_notes',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
        'approval_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'goals' => 'array',
        'activities' => 'array',
        'budget_requested' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MinistryReport::class, 'plan_id');
    }

    /**
     * Extract planned activities with dates from activities JSON for event generation.
     */
    public function plannedActivities(): array
    {
        $activities = is_array($this->activities) ? $this->activities : [];
        return array_filter($activities, function ($a) {
            return ! empty($a['date'] ?? $a['start_date'] ?? null);
        });
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', self::STATUS_UNDER_ADMIN_REVIEW);
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', [self::STATUS_APPROVED, self::STATUS_IN_EXECUTION]);
    }

    public function isApproved(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_IN_EXECUTION], true);
    }
}
