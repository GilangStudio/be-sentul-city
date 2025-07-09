<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HomePageSetting;
use App\Models\HomePageBanner;
use App\Models\News;
use App\Models\Promo;
use App\Models\NewResidentsPageSetting;
use App\Models\ETownSection;
use App\Models\AboutUsPageSetting;
use App\Models\AboutExecutiveSummaryItem;
use App\Models\AboutFunctionItem;
use App\Models\AboutServiceItem;
use Illuminate\Http\JsonResponse;

class HomePageApiController extends Controller
{
    /**
     * Get complete homepage data for landing page
     */
    public function getHomepageData(): JsonResponse
    {
        try {
            $data = [
                'seo' => $this->getSeoData(),
                'banners' => $this->getBannersData(),
                'news' => $this->getNewsData(),
                'promos' => $this->getPromosData(),
                'neighborhood_guide' => $this->getNeighborhoodGuideData(),
                'etown_section' => $this->getETownSectionData(),
                'about_us' => $this->getAboutUsData(),
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
        ];
    }

    /**
     * Get complete About Us data
     */
    private function getAboutUsData(): ?array
    {
        $aboutPage = AboutUsPageSetting::first();
        
        if (!$aboutPage || !$aboutPage->is_active) {
            return null;
        }

        // Get executive summary items
        $executiveSummaryItems = AboutExecutiveSummaryItem::active()
                                                        ->ordered()
                                                        ->select('id', 'title', 'description', 'icon_path', 'icon_alt_text', 'order')
                                                        ->get()
                                                        ->map(function ($item) {
                                                            return [
                                                                'id' => $item->id,
                                                                'title' => $item->title,
                                                                'description' => $item->description,
                                                                'icon_url' => $item->icon_url,
                                                                'icon_alt_text' => $item->icon_alt_text,
                                                                'order' => $item->order,
                                                            ];
                                                        });

        // Get function items
        $functionItems = AboutFunctionItem::active()
                                         ->ordered()
                                         ->select('id', 'title', 'description', 'image_path', 'image_alt_text', 'order')
                                         ->get()
                                         ->map(function ($item) {
                                             return [
                                                 'id' => $item->id,
                                                 'title' => $item->title,
                                                 'description' => $item->description,
                                                 'image_url' => $item->image_url,
                                                 'image_alt_text' => $item->image_alt_text,
                                                 'order' => $item->order,
                                             ];
                                         });

        // Get service items
        $serviceItems = AboutServiceItem::active()
                                       ->ordered()
                                       ->select('id', 'title', 'description', 'icon_path', 'icon_alt_text', 'order')
                                       ->get()
                                       ->map(function ($item) {
                                           return [
                                               'id' => $item->id,
                                               'title' => $item->title,
                                               'description' => $item->description,
                                               'icon_url' => $item->icon_url,
                                               'icon_alt_text' => $item->icon_alt_text,
                                               'order' => $item->order,
                                           ];
                                       });

        return [
            'banner' => [
                'image_url' => $aboutPage->banner_image_url,
                'alt_text' => $aboutPage->banner_alt_text,
            ],
            'company_info' => [
                'name' => $aboutPage->company_name,
                'description' => $aboutPage->company_description,
                'vision' => $aboutPage->vision,
                'mission' => $aboutPage->mission,
            ],
            'statistics' => [
                'total_houses' => $aboutPage->total_houses,
                'houses_label' => $aboutPage->houses_label,
                'daily_visitors' => $aboutPage->daily_visitors,
                'visitors_label' => $aboutPage->visitors_label,
                'commercial_areas' => $aboutPage->commercial_areas,
                'commercial_label' => $aboutPage->commercial_label,
            ],
            'main_sections' => [
                'section_1' => [
                    'title' => $aboutPage->main_section1_title,
                    'description' => $aboutPage->main_section1_description,
                    'image_url' => $aboutPage->main_section1_image_url,
                    'image_alt_text' => $aboutPage->main_section1_image_alt_text,
                ],
                'section_2' => [
                    'title' => $aboutPage->main_section2_title,
                    'description' => $aboutPage->main_section2_description,
                    'image_url' => $aboutPage->main_section2_image_url,
                    'image_alt_text' => $aboutPage->main_section2_image_alt_text,
                ],
            ],
            'contact_info' => [
                'phone' => $aboutPage->phone,
                'email' => $aboutPage->email,
                'address' => $aboutPage->address,
                'website_url' => $aboutPage->website_url,
            ],
            'executive_summary_items' => $executiveSummaryItems->toArray(),
            'function_items' => $functionItems->toArray(),
            'service_items' => $serviceItems->toArray(),
            'seo' => [
                'meta_title' => $aboutPage->meta_title_display,
                'meta_description' => $aboutPage->meta_description_display,
                'meta_keywords' => $aboutPage->meta_keywords_display,
            ],
        ];
    }

    /**
     * Get individual section data endpoints for more granular access
     */

    /**
     * Get only banners data
     */
    public function getBanners(): JsonResponse
    {
        try {
            $banners = $this->getBannersData();

            return response()->json([
                'success' => true,
                'data' => $banners,
                'message' => 'Homepage banners retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve banners',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get only news data
     */
    public function getNews(): JsonResponse
    {
        try {
            $news = $this->getNewsData();

            return response()->json([
                'success' => true,
                'data' => $news,
                'message' => 'Homepage news retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve news',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get only promos data
     */
    public function getPromos(): JsonResponse
    {
        try {
            $promos = $this->getPromosData();

            return response()->json([
                'success' => true,
                'data' => $promos,
                'message' => 'Homepage promos retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve promos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get only neighborhood guide data
     */
    public function getNeighborhoodGuide(): JsonResponse
    {
        try {
            $neighborhoodGuide = $this->getNeighborhoodGuideData();

            return response()->json([
                'success' => true,
                'data' => $neighborhoodGuide,
                'message' => 'Neighborhood guide data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve neighborhood guide data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get only E-Town section data
     */
    public function getETownSection(): JsonResponse
    {
        try {
            $etownSection = $this->getETownSectionData();

            return response()->json([
                'success' => true,
                'data' => $etownSection,
                'message' => 'E-Town section data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve E-Town section data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get only About Us data
     */
    public function getAboutUs(): JsonResponse
    {
        try {
            $aboutUs = $this->getAboutUsData();

            return response()->json([
                'success' => true,
                'data' => $aboutUs,
                'message' => 'About Us data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve About Us data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}