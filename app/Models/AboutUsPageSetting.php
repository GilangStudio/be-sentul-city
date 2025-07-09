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

    public function getHomeThumbnailImageUrlAttribute()
    {
        return $this->home_thumbnail_image_path ? asset('storage/' . $this->home_thumbnail_image_path) : null;
    }

    public function getCompanyLogoHeaderUrlAttribute()
    {
        return $this->company_logo_header_path ? asset('storage/' . $this->company_logo_header_path) : null;
    }

    public function getCompanyLogoFooterUrlAttribute()
    {
        return $this->company_logo_footer_path ? asset('storage/' . $this->company_logo_footer_path) : null;
    }

    public function getMainSection1ImageUrlAttribute()
    {
        return $this->main_section1_image_path ? asset('storage/' . $this->main_section1_image_path) : null;
    }

    public function getMainSection2ImageUrlAttribute()
    {
        return $this->main_section2_image_path ? asset('storage/' . $this->main_section2_image_path) : null;
    }

    // SEO Accessors
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

    // Logo Status Accessors
    public function getHasHeaderLogoAttribute()
    {
        return !empty($this->company_logo_header_path);
    }

    public function getHasFooterLogoAttribute()
    {
        return !empty($this->company_logo_footer_path);
    }

    public function getHasAnyLogoAttribute()
    {
        return $this->has_header_logo || $this->has_footer_logo;
    }

    // Social Media Accessors
    public function getHasSocialMediaAttribute()
    {
        return $this->facebook_url || $this->instagram_url || $this->youtube_url || 
               $this->twitter_url || $this->linkedin_url;
    }

    public function getSocialMediaLinksAttribute()
    {
        $links = [];
        
        if ($this->facebook_url) {
            $links['facebook'] = [
                'url' => $this->facebook_url,
                'icon' => 'ti ti-brand-facebook',
                'name' => 'Facebook'
            ];
        }
        
        if ($this->instagram_url) {
            $links['instagram'] = [
                'url' => $this->instagram_url,
                'icon' => 'ti ti-brand-instagram',
                'name' => 'Instagram'
            ];
        }
        
        if ($this->youtube_url) {
            $links['youtube'] = [
                'url' => $this->youtube_url,
                'icon' => 'ti ti-brand-youtube',
                'name' => 'YouTube'
            ];
        }
        
        if ($this->twitter_url) {
            $links['twitter'] = [
                'url' => $this->twitter_url,
                'icon' => 'ti ti-brand-twitter',
                'name' => 'Twitter'
            ];
        }
        
        if ($this->linkedin_url) {
            $links['linkedin'] = [
                'url' => $this->linkedin_url,
                'icon' => 'ti ti-brand-linkedin',
                'name' => 'LinkedIn'
            ];
        }
        
        return $links;
    }
}