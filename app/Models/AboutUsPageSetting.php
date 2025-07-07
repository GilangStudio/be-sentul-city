<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutUsPageSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessors for image URLs
    public function getBannerImageUrlAttribute()
    {
        return $this->banner_image_path ? asset('storage/' . $this->banner_image_path) : null;
    }

    public function getMainSection1ImageUrlAttribute()
    {
        return $this->main_section1_image_path ? asset('storage/' . $this->main_section1_image_path) : null;
    }

    public function getMainSection2ImageUrlAttribute()
    {
        return $this->main_section2_image_path ? asset('storage/' . $this->main_section2_image_path) : null;
    }

    public function getMetaTitleDisplayAttribute()
    {
        return $this->meta_title ?: 'About Us';
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
