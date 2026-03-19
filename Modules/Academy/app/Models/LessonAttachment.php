<?php

declare(strict_types=1);

namespace Modules\Academy\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonAttachment extends Model
{
    protected $table = 'academy_lesson_attachments';

    protected $fillable = [
        'lesson_id',
        'file_path',
        'type',
    ];

    protected $casts = [
        'lesson_id' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }
}
