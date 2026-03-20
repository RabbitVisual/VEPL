<?php

namespace Modules\Sermons\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;

class SermonBibleReference extends Model
{
    protected $fillable = [
        'sermon_id',
        'book',
        'bible_version_id',
        'book_id',
        'chapter_id',
        'verse_start_id',
        'verse_end_id',
        'type',
        'context',
        'exegesis_notes',
        'order',
    ];

    // Type constants
    const TYPE_MAIN = 'main';

    const TYPE_SUPPORT = 'support';

    const TYPE_ILLUSTRATION = 'illustration';

    const TYPE_OTHER = 'other';

    /**
     * Get the sermon
     */
    public function sermon(): BelongsTo
    {
        return $this->belongsTo(Sermon::class, 'sermon_id');
    }

    /**
     * Get the bible version
     */
    public function bibleVersion(): BelongsTo
    {
        return $this->belongsTo(BibleVersion::class, 'bible_version_id');
    }

    /**
     * Get the book
     */
    public function bookModel(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    /**
     * Get the chapter
     */
    public function chapterModel(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function verseStart(): BelongsTo
    {
        return $this->belongsTo(Verse::class, 'verse_start_id');
    }

    public function verseEnd(): BelongsTo
    {
        return $this->belongsTo(Verse::class, 'verse_end_id');
    }

    /**
     * Get formatted reference
     */
    public function getFormattedReferenceAttribute(): string
    {
        $reference = $this->bookModel?->name ?? $this->book;

        if ($this->chapterModel) {
            $reference .= ' '.$this->chapterModel->chapter_number;
        }

        if ($this->verseStart) {
            $reference .= ':'.$this->verseStart->verse_number;
            if ($this->verseEnd && $this->verseEnd->id !== $this->verseStart->id) {
                $reference .= '-'.$this->verseEnd->verse_number;
            }
        }

        return $reference;
    }

    /**
     * Get type display name
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_MAIN => 'Principal',
            self::TYPE_SUPPORT => 'Apoio',
            self::TYPE_ILLUSTRATION => 'Ilustração',
            self::TYPE_OTHER => 'Outro',
            default => 'Principal'
        };
    }
}
