<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrongsCorrection extends Model
{
    protected $table = 'bible_strongs_corrections';

    protected $fillable = [
        'strong_number',
        'field',
        'current_value',
        'proposed_value',
        'justification',
        'requested_by',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }

    /**
     * Scopes por status.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Retorna label do campo corrigido em Português.
     */
    public function getFieldLabelAttribute(): string
    {
        return match ($this->field) {
            'description_pt' => 'Definição (PT-BR)',
            'lemma_br' => 'Equivalente Semântico (PT)',
            'xlit' => 'Transliteração Acadêmica',
            'pronounce' => 'Pronúncia',
            default => $this->field,
        };
    }

    /**
     * Aplica a correção aprovada na tabela bible_strongs_lexicon.
     */
    public function apply(): void
    {
        StrongsLexicon::where('number', $this->strong_number)
            ->update([$this->field => $this->proposed_value]);
    }
}
