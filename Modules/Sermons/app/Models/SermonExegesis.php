<?php

namespace Modules\Sermons\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SermonExegesis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sermon_exegesis';

    protected $fillable = [
        'book',
        'chapter',
        'verse_start',
        'verse_end',
        'book_id',
        'chapter_id',
        'title',
        'content',
        'user_id',
        'status',
        'is_official',
        'cover_image',
        'audio_url',
    ];

    protected $casts = [
        'is_official' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

