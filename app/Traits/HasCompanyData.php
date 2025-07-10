<?php

namespace App\Traits;

use App\Models\ETownSection;
use App\Models\AboutUsPageSetting;

trait HasCompanyData
{
    /**
     * Get company data for inclusion in API responses
     */
    protected function getCompanyData(): array
    {
        $aboutPage = AboutUsPageSetting::first();
        
        if (!$aboutPage || !$aboutPage->is_active) {
            return $this->getDefaultCompanyData();
        }

        $etownSection = ETownSection::first();
        
        return [
            'name' => $aboutPage->company_name,
            'logos' => [
                'header' => [
                    'url' => $aboutPage->company_logo_header_url,
                    'alt_text' => $aboutPage->company_logo_header_alt_text,
                ],
                'footer' => [
                    'url' => $aboutPage->company_logo_footer_url,
                    'alt_text' => $aboutPage->company_logo_footer_alt_text,
                ],
            ],
            'contact' => [
                'phone' => $aboutPage->phone,
                'email' => $aboutPage->email,
                'address' => $aboutPage->address,
                'website_url' => $aboutPage->website_url,
            ],
            'social_media' => [
                'facebook' => [
                    'url' => $aboutPage->facebook_url,
                ],
                'instagram' => [
                    'url' => $aboutPage->instagram_url,
                ],
                'youtube' => [
                    'url' => $aboutPage->youtube_url,
                ],
                'twitter' => [
                    'url' => $aboutPage->twitter_url,
                ],
                'linkedin' => [
                    'url' => $aboutPage->linkedin_url,
                ],
            ],
            'mobile_apps' => [
                'google_play' => [
                    'url' => $etownSection?->google_play_url,
                ],
                'apple_store' => [
                    'url' => $etownSection?->apple_store_url,
                ],
            ],
        ];
    }

    /**
     * Get default company data when no About Us page is configured
     */
    protected function getDefaultCompanyData(): array
    {
        return [
            'name' => 'PT SENTUL CITY Tbk.',
            'logos' => [
                'header' => [
                    'url' => null,
                    'alt_text' => null,
                    'available' => false,
                ],
                'footer' => [
                    'url' => null,
                    'alt_text' => null,
                    'available' => false,
                ],
            ],
            'contact' => [
                'phone' => null,
                'email' => null,
                'address' => null,
                'website_url' => null,
            ],
            'social_media' => [
                'facebook' => ['url' => null],
                'instagram' => ['url' => null],
                'youtube' => ['url' => null],
                'twitter' => ['url' => null],
                'linkedin' => ['url' => null],
            ],
        ];
    }
}