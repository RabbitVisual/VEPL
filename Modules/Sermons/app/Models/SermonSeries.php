<?php

namespace Modules\Sermons\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SermonSeries extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sermon_series';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover_image',
        'status',
        'is_featured',
        'user_id',
    ];

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function sermons(): HasMany
    {
        return $this->hasMany(Sermon::class, 'sermon_series_id');
    }

    public function outlines(): HasMany
    {
        return $this->hasMany(SermonOutline::class, 'sermon_series_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

