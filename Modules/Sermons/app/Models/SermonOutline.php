<?php

namespace Modules\Sermons\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SermonOutline extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sermon_outlines';

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'content',
        'cover_image',
        'video_url',
        'audio_url',
        'sermon_series_id',
        'category_id',
        'user_id',
        'status',
        'visibility',
        'is_featured',
        'views',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(SermonSeries::class, 'sermon_series_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SermonCategory::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

