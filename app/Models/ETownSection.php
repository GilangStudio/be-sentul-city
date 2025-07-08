<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ETownSection extends Model
{
    protected $guarded = ['id'];

    // Accessors
    public function getAppMockupImageUrlAttribute()
    {
        return $this->app_mockup_image_path ? asset('storage/' . $this->app_mockup_image_path) : null;
    }

    public function getMetaTitleDisplayAttribute()
    {
        return $this->meta_title ?: 'E-Town App - Sentul City';
    }

    public function getMetaDescriptionDisplayAttribute()
    {
        return $this->meta_description ?: '';
    }

    public function getMetaKeywordsDisplayAttribute()
    {
        return $this->meta_keywords ?: '';
    }

    public function getHasAppStoreLinksAttribute()
    {
        return $this->google_play_url || $this->app_store_url;
    }
}