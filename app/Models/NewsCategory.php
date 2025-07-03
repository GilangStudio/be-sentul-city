<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsCategory extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    // Accessors
    public function getNewsCountAttribute()
    {
        return $this->news()->count();
    }

    public function getPublishedNewsCountAttribute()
    {
        return $this->news()->published()->count();
    }
}