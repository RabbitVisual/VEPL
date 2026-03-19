<?php

namespace Modules\HomePage\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'inquiry_type',
        'ministerial_context',
        'interest_area',
        'lead_score',
        'lead_scoring',
        'follow_up_scheduled',
        'status',
        'read_at',
        'replied_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
        'follow_up_scheduled' => 'datetime',
        'lead_score' => 'integer',
        'lead_scoring' => 'array',
    ];

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopePendingFollowUp($query)
    {
        return $query->whereNotNull('follow_up_scheduled')
            ->where('follow_up_scheduled', '<=', now())
            ->whereIn('status', ['new', 'in_progress']);
    }
}
