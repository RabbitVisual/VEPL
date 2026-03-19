<?php

declare(strict_types=1);

namespace Modules\Academy\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $table = 'academy_lessons';

    protected $fillable = [
        'module_id',
        'title',
        'video_url',
        'content_text',
        'duration_minutes',
        'is_free',
    ];

    protected $casts = [
        'module_id' => 'integer',
        'duration_minutes' => 'integer',
        'is_free' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(AcademyModule::class, 'module_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LessonAttachment::class, 'lesson_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(StudentProgress::class, 'lesson_id');
    }
}
