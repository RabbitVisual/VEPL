<?php

namespace Modules\HomePage\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'photo',
        'testimonial',
        'position',
        'ministerial_title',
        'formation_completed',
        'church_affiliation',
        'city',
        'state',
        'testimonial_type',
        'impact_score',
        'ministry_level',
        'video_url',
        'is_verified',
        'verification_data',
        'is_active',
        'is_featured',
        'order',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'order' => 'integer',
        'created_by' => 'integer',
        'verification_data' => 'array',
    ];

    /**
     * Scope para testemunhos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ordenação
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
