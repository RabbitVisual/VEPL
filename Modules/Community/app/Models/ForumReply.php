<?php

declare(strict_types=1);

namespace Modules\Community\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumReply extends Model
{
    protected $table = 'forum_replies';

    protected $fillable = [
        'topic_id',
        'user_id',
        'body',
        'is_accepted_answer',
    ];

    protected $casts = [
        'topic_id' => 'integer',
        'user_id' => 'integer',
        'is_accepted_answer' => 'boolean',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
