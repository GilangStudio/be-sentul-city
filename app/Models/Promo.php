<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessors
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getTitleDisplayAttribute()
    {
        return $this->title ?: 'Untitled Promo';
    }

    public function getDescriptionDisplayAttribute()
    {
        return $this->description ?: 'No description available';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }
}