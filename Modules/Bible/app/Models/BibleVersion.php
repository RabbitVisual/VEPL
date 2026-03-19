<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BibleVersion extends Model
{
    protected $fillable = [
        'name',
        'abbreviation',
        'description',
        'language',
        'file_name',
        'is_active',
        'is_default',
        'total_books',
        'total_chapters',
        'total_verses',
        'imported_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'imported_at' => 'datetime',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function getTotalBooksAttribute($value)
    {
        if ($value !== null && $value > 0) {
            return $value;
        }

        return $this->books()->count();
    }

    public function getTotalChaptersAttribute($value)
    {
        if ($value !== null && $value > 0) {
            return $value;
        }

        return $this->books()->withCount('chapters')->get()->sum('chapters_count');
    }

    public function getTotalVersesAttribute($value)
    {
        if ($value !== null && $value > 0) {
            return $value;
        }

        return $this->books()->withCount('verses')->get()->sum('verses_count');
    }
}
