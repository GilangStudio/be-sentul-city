<?php

namespace App\Http\Controllers\API;

use App\Models\News;
use App\Models\Promo;
use App\Models\ETownSection;
use App\Models\HomePageBanner;
use App\Traits\HasCompanyData;
use App\Models\HomePageSetting;
use Illuminate\Http\JsonResponse;
use App\Models\AboutUsPageSetting;
use App\Http\Controllers\Controller;
use App\Models\NewResidentsPageSetting;

class HomePageApiController extends Controller
{
    use HasCompanyData;

    /**
     * Get complete homepage data for landing page
     */
    public function getHomepageData(): JsonResponse
    {
        try {
            $data = [
                'page_content' => [
                    'banners' => $this->getBannersData(),
                    'news' => $this->getNewsData(),
                    'promos' => $this->getPromosData(),
                    'neighborhood_guide' => $this->getNeighborhoodGuideData(),
                    'etown_section' => $this->getETownSectionData(),
                    'about_us' => $this->getAboutUsData(),
                ],
                'seo' => $this->getSeoData(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Homepage data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve homepage data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get SEO settings for homepage
     */
    private function getSeoData(): array
    {
        $homePageSettings = HomePageSetting::first();
        
        return [
            'meta_title' => $homePageSettings ? $homePageSettings->meta_title_display : 'Sentul City - Your Dream City',
            'meta_description' => $homePageSettings ? $homePageSettings->meta_description_display : '',
            'meta_keywords' => $homePageSettings ? $homePageSettings->meta_keywords_display : '',
        ];
    }

    /**
     * Get homepage banners
     */
    private function getBannersData(): array
    {
        $banners = HomePageBanner::active()
                                ->ordered()
                                ->select('id', 'title', 'subtitle', 'button_text', 'button_url', 'image_path', 'image_alt_text', 'order')
                                ->get()
                                ->map(function ($banner) {
                                    return [
                                        'id' => $banner->id,
                                        'title' => $banner->title_display,
                                        'subtitle' => $banner->subtitle_display,
                                        'button_text' => $banner->button_text_display,
                                        'button_url' => $banner->button_url,
                                        'image_url' => $banner->image_url,
                                        'image_alt_text' => $banner->image_alt_text,
                                        'has_button' => $banner->has_button,
                                        'order' => $banner->order,
                                    ];
                                });

        return $banners->toArray();
    }

    /**
     * Get news data (3 latest + 1 featured)
     */
    private function getNewsData(): array
    {
        // Get featured news
        $featuredNews = News::with('category')
                           ->published()
                           ->featured()
                           ->select('id', 'title', 'slug', 'category_id', 'content', 'image_path', 'published_at', 'is_featured')
                           ->first();

        // Get 3 latest news (excluding featured if exists)
        $latestNewsQuery = News::with('category')
                              ->published()
                              ->select('id', 'title', 'slug', 'category_id', 'content', 'image_path', 'published_at', 'is_featured')
                              ->orderBy('published_at', 'desc')
                              ->orderBy('created_at', 'desc');

        if ($featuredNews) {
            $latestNewsQuery->where('id', '!=', $featuredNews->id);
        }

        $latestNews = $latestNewsQuery->limit(3)->get();

        $formatNews = function ($news) {
            return [
                'id' => $news->id,
                'title' => $news->title,
                'slug' => $news->slug,
                'excerpt' => $news->excerpt,
                'image_url' => $news->image_url,
                'category' => [
                    'id' => $news->category->id,
                    'name' => $news->category->name,
                    'slug' => $news->category->slug,
                ],
                'published_at' => $news->published_at->format('d F Y'),
                'published_at_formatted' => $news->published_at->diffForHumans(),
                'is_featured' => $news->is_featured,
            ];
        };

        return [
            'featured' => $featuredNews ? $formatNews($featuredNews) : null,
            'latest' => $latestNews->map($formatNews)->toArray(),
        ];
    }

    /**
     * Get active promos
     */
    private function getPromosData(): array
    {
        $promos = Promo::active()
                      ->ordered()
                      ->select('id', 'title', 'description', 'image_path', 'order')
                      ->get()
                      ->map(function ($promo) {
                          return [
                              'id' => $promo->id,
                              'title' => $promo->title_display,
                              'description' => $promo->description_display,
                              'image_url' => $promo->image_url,
                              'order' => $promo->order,
                          ];
                      });

        return $promos->toArray();
    }

    /**
     * Get neighborhood guide section from new residents page
     */
    private function getNeighborhoodGuideData(): ?array
    {
        $newResidentsPage = NewResidentsPageSetting::first();
        
        if (!$newResidentsPage || !$newResidentsPage->is_active) {
            return null;
        }

        return [
            'title' => $newResidentsPage->neighborhood_title,
            'description' => $newResidentsPage->neighborhood_description,
            'image_url' => $newResidentsPage->neighborhood_image_url,
            'image_alt_text' => $newResidentsPage->neighborhood_image_alt_text,
        ];
    }

    /**
     * Get E-Town section data
     */
    private function getETownSectionData(): ?array
    {
        $etownSection = ETownSection::first();
        
        if (!$etownSection) {
            return null;
        }

        return [
            'title' => $etownSection->section_title,
            'description' => $etownSection->description,
            'app_mockup_image_url' => $etownSection->app_mockup_image_url,
            'app_mockup_alt_text' => $etownSection->app_mockup_alt_text,
            'google_play_url' => $etownSection->google_play_url,
            'app_store_url' => $etownSection->app_store_url,
            'has_app_store_links' => $etownSection->has_app_store_links,
        ];
    }

    /**
     * Get About Us data for homepage section
     */
    private function getAboutUsData(): ?array
    {
        $aboutPage = AboutUsPageSetting::first();
        
        if (!$aboutPage || !$aboutPage->is_active) {
            return null;
        }

        return [
            'thumbnail_image_url' => $aboutPage->home_thumbnail_image_url,
            'thumbnail_alt_text' => $aboutPage->home_thumbnail_alt_text,
            'company_name' => $aboutPage->company_name,
            'company_description' => $aboutPage->company_description,
            'website_url' => $aboutPage->website_url,
        ];
    }
}