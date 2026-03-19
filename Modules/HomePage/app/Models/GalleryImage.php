<?php

namespace Modules\HomePage\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class GalleryImage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'image_url',
        'category',
        'content_type',
        'formation_context',
        'captured_at',
        'event_id',
        'sermon_id',
        'educational_metadata',
        'tags',
        'is_active',
        'is_featured',
        'order',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'order' => 'integer',
        'created_by' => 'integer',
        'captured_at' => 'date',
        'educational_metadata' => 'array',
        'tags' => 'array',
    ];

    /**
     * Scope para imagens ativas
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

    /**
     * Scope por categoria
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get image URL attribute
     */
    public function getImageUrlAttribute($value)
    {
        return $value ?: ($this->image_path ? Storage::url($this->image_path) : null);
    }
}
