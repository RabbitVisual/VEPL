<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BibleOfficialCommentary extends Model
{
    protected $table = 'bible_official_commentaries';

    protected $fillable = [
        'book_id',
        'chapter_id',
        'verse_id',
        'official_commentary',
        'is_published',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function verse(): BelongsTo
    {
        return $this->belongsTo(Verse::class);
    }
}
