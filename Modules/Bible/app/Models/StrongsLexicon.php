<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrongsLexicon extends Model
{
    protected $table = 'bible_strongs_lexicon';

    protected $fillable = [
        'number',
        'lang',
        'lemma',
        'pronounce',
        'xlit',
        'description_pt',
        'lemma_br',
        'is_reviewed',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'is_reviewed' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Retorna se o número é hebraico (H) ou grego (G).
     */
    public function isHebrew(): bool
    {
        return $this->lang === 'he';
    }

    public function isGreek(): bool
    {
        return $this->lang === 'gr';
    }

    /**
     * Revisor da entrada (Admin que marcou como revisada).
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }

    /**
     * Correções associadas a este número Strong.
     */
    public function corrections()
    {
        return $this->hasMany(StrongsCorrection::class, 'strong_number', 'number');
    }

    /**
     * Scope: apenas hebraico (AT).
     */
    public function scopeHebrew($query)
    {
        return $query->where('lang', 'he');
    }

    /**
     * Scope: apenas grego (NT).
     */
    public function scopeGreek($query)
    {
        return $query->where('lang', 'gr');
    }
}
