<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;

class BibleWordTag extends Model
{
    protected $table = 'bible_word_tags';

    protected $fillable = [
        'verse_id',
        'position',
        'word_surface',
        'strong_number',
        'morphology',
        'lang',
    ];

    public $timestamps = false;

    public function verse()
    {
        return $this->belongsTo(Verse::class, 'verse_id');
    }
}

