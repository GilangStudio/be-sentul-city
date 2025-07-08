<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageBanner extends Model
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
        return $this->title ?: 'Welcome to Sentul City';
    }

    public function getSubtitleDisplayAttribute()
    {
        return $this->subtitle ?: '';
    }

    public function getButtonTextDisplayAttribute()
    {
        return $this->button_text ?: '';
    }

    public function getHasButtonAttribute()
    {
        return $this->button_text && $this->button_url;
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
}