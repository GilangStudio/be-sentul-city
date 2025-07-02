<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    protected $guarded = ['id'];

    // Accessors for image URLs
    public function getBannerImageUrlAttribute()
    {
        return $this->banner_image_path ? asset('storage/' . $this->banner_image_path) : null;
    }

    public function getMetaTitleDisplayAttribute()
    {
        return $this->meta_title ?: '';
    }

    public function getMetaDescriptionDisplayAttribute()
    {
        return $this->meta_description ?: '';
    }

    public function getMetaKeywordsDisplayAttribute()
    {
        return $this->meta_keywords ?: '';
    }

    // Helper method to get banner media URL
    public function getBannerMediaUrlAttribute()
    {
        return $this->banner_image_url;
    }
}