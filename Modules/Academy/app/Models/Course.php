<?php

declare(strict_types=1);

namespace Modules\Academy\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $table = 'academy_courses';

    protected $fillable = [
        'title',
        'description',
        'cover_image',
        'workload_hours',
        'level',
    ];

    protected $casts = [
        'workload_hours' => 'integer',
    ];

    public function modules(): HasMany
    {
        return $this->hasMany(AcademyModule::class, 'course_id')->orderBy('order');
    }
}
