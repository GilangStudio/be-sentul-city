<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean'
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status === 'published' ? 'Published' : 'Draft';
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getExcerptAttribute()
    {
        return Str::limit(strip_tags($this->content), 100);
    }

    // Meta SEO Accessors
    public function getMetaTitleDisplayAttribute()
    {
        return $this->meta_title ?: $this->title;
    }

    public function getMetaDescriptionDisplayAttribute()
    {
        return $this->meta_description ?: $this->excerpt;
    }

    public function getMetaKeywordsDisplayAttribute()
    {
        return $this->meta_keywords ?: '';
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}