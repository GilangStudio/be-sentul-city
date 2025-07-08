<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    protected $guarded = ['id'];

    // Relationships
    public function banners()
    {
        return $this->hasMany(HomePageBanner::class);
    }

    // Accessors for SEO
    public function getMetaTitleDisplayAttribute()
    {
        return $this->meta_title ?: 'Sentul City - Your Dream City';
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