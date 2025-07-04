<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticalInfoPlace extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'tags' => 'array',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(PracticalInfoCategory::class, 'category_id');
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getTagsDisplayAttribute()
    {
        return $this->tags ? implode(', ', $this->tags) : '';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
