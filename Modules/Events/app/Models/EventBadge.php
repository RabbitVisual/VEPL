<?php

namespace Modules\Events\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBadge extends Model
{
    protected $fillable = [
        'event_id',
        'template_html',
        'orientation',
        'paper_size',
        'badges_per_page',
    ];

    protected $casts = [
        'badges_per_page' => 'integer',
    ];

    /**
     * Default badge template HTML
     */
    public static function getDefaultTemplate(): string
    {
        return <<<'HTML'
<div class="badge">
    <div class="badge-header">
        {{ event }}
    </div>
    <div class="badge-body">
        <div class="participant-name">
            {{ name }}
        </div>
        <div class="role-badge">
            {{ role }}
        </div>
    </div>
    <div class="badge-footer">
        {{ date }} • {{ location }}
    </div>
    </div>
    <div class="badge-footer">
        {{ date }} • {{ location }}
    </div>
</div>
HTML;
    }

    /**
     * Catálogo de templates de crachá pré-definidos.
     *
     * Cada template usa os placeholders padronizados:
     * {{ name }}, {{ event }}, {{ role }}, {{ date }}, {{ location }}, {{ qr_code }}.
     */
    public static function templates(): array
    {
        return [
            [
                'id' => 'default',
                'label' => __('events::messages.badge_template_default_label'),
                'description' => __('events::messages.badge_template_default_description'),
                'html' => self::getDefaultTemplate(),
            ],
            [
                'id' => 'minimal',
                'label' => __('events::messages.badge_template_minimal_label'),
                'description' => __('events::messages.badge_template_minimal_description'),
                'html' => <<<'HTML'
<div class="badge" style="border-radius: 6px; border: 1px solid #E5E7EB; padding: 8px 10px; font-family: sans-serif;">
    <div class="badge-body" style="text-align: center;">
        <div class="participant-name" style="font-size: 12pt; font-weight: 600; color: #111827; margin-bottom: 4px;">
            {{ name }}
        </div>
        <div class="role-badge" style="font-size: 9pt; color: #4B5563;">
            {{ role }}
        </div>
    </div>
    <div class="badge-footer" style="margin-top: 6px; font-size: 8pt; color: #6B7280; text-align: center;">
        {{ event }} • {{ date }}
    </div>
</div>
HTML,
            ],
            [
                'id' => 'kids',
                'label' => __('events::messages.badge_template_kids_label'),
                'description' => __('events::messages.badge_template_kids_description'),
                'html' => <<<'HTML'
<div class="badge" style="border-radius: 12px; border: 2px solid #FB923C; padding: 10px; background: #FFFBEB; font-family: sans-serif;">
    <div class="badge-header" style="font-size: 9pt; font-weight: 700; text-transform: uppercase; color: #EA580C; text-align: center; letter-spacing: 0.05em; margin-bottom: 4px;">
        {{ event }}
    </div>
    <div class="badge-body" style="text-align: center;">
        <div class="participant-name" style="font-size: 13pt; font-weight: 800; color: #1F2937; margin-bottom: 4px;">
            {{ name }}
        </div>
        <div class="role-badge" style="display: inline-block; padding: 3px 8px; border-radius: 999px; background: #F97316; color: #FEFCE8; font-size: 8pt; font-weight: 600;">
            {{ role }}
        </div>
    </div>
    <div class="badge-footer" style="margin-top: 6px; font-size: 8pt; color: #6B7280; text-align: center;">
        {{ date }} • {{ location }}
    </div>
</div>
HTML,
            ],
            [
                'id' => 'pastoral',
                'label' => __('events::messages.badge_template_pastoral_label'),
                'description' => __('events::messages.badge_template_pastoral_description'),
                'html' => <<<'HTML'
<div class="badge" style="border-radius: 4px; border: 1px solid #D1D5DB; padding: 8px 10px; background: #F9FAFB; font-family: sans-serif;">
    <div class="badge-header" style="font-size: 9pt; font-weight: 600; color: #111827; text-align: left; margin-bottom: 4px;">
        {{ event }}
    </div>
    <div class="badge-body" style="display: flex; flex-direction: column; align-items: flex-start; gap: 2px;">
        <div class="participant-name" style="font-size: 12pt; font-weight: 700; color: #111827;">
            {{ name }}
        </div>
        <div class="role-badge" style="font-size: 9pt; color: #4B5563;">
            {{ role }}
        </div>
    </div>
    <div class="badge-footer" style="margin-top: 6px; font-size: 8pt; color: #6B7280;">
        {{ date }} • {{ location }}
    </div>
</div>
HTML,
            ],
        ];
    }

    /**
     * Get the event that owns the badge template.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
