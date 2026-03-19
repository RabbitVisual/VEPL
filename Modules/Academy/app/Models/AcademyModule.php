<?php

declare(strict_types=1);

namespace Modules\Academy\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyModule extends Model
{
    protected $table = 'academy_modules';

    protected $fillable = [
        'course_id',
        'title',
        'order',
    ];

    protected $casts = [
        'course_id' => 'integer',
        'order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'module_id');
    }
}
