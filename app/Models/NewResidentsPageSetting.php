<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewResidentsPageSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessors
    public function getBannerImageUrlAttribute()
    {
        return $this->banner_image_path ? asset('storage/' . $this->banner_image_path) : null;
    }

    public function getNeighborhoodImageUrlAttribute()
    {
        return $this->neighborhood_image_path ? asset('storage/' . $this->neighborhood_image_path) : null;
    }

    public function getMetaTitleDisplayAttribute()
    {
        return $this->meta_title ?: 'New Residents';
    }

    public function getMetaDescriptionDisplayAttribute()
    {
        return $this->meta_description ?: '';
    }

    public function getMetaKeywordsDisplayAttribute()
    {
        return $this->meta_keywords ?: '';
    }
}
