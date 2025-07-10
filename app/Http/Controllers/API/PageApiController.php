<?php

namespace App\Http\Controllers\API;

use App\Models\Promo;
use App\Models\ETownSection;
use App\Models\ServiceSection;
use App\Traits\HasCompanyData;
use App\Models\PartnershipItem;
use App\Models\AboutServiceItem;
use App\Models\AboutFunctionItem;
use Illuminate\Http\JsonResponse;
use App\Models\AboutUsPageSetting;
use App\Models\TransportationItem;
use App\Models\ServicesPageSetting;
use App\Http\Controllers\Controller;
use App\Models\PracticalInfoCategory;
use App\Models\PartnershipPageSetting;
use App\Models\NewResidentsPageSetting;
use App\Models\AboutExecutiveSummaryItem;

class PageApiController extends Controller
{
    use HasCompanyData;
    
    /**
     * Get About Us page complete data
     */
    public function getAboutUsPage(): JsonResponse
    {
        try {
            $aboutPage = AboutUsPageSetting::first();
            
            if (!$aboutPage || !$aboutPage->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'About Us page is not available'
                ], 404);
            }

            $executiveSummaryItems = AboutExecutiveSummaryItem::active()->ordered()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon_url' => $item->icon_url,
                    'icon_alt_text' => $item->icon_alt_text,
                    'order' => $item->order,
                ];
            });

            $functionItems = AboutFunctionItem::active()->ordered()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'image_url' => $item->image_url,
                    'image_alt_text' => $item->image_alt_text,
                    'order' => $item->order,
                ];
            });

            $serviceItems = AboutServiceItem::active()->ordered()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon_url' => $item->icon_url,
                    'icon_alt_text' => $item->icon_alt_text,
                    'order' => $item->order,
                ];
            });

            $data = [
                'page_content' => [
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
                        'total_houses' => [
                            'value' => $aboutPage->total_houses,
                            'label' => $aboutPage->houses_label,
                            'formatted' => number_format($aboutPage->total_houses),
                        ],
                        'daily_visitors' => [
                            'value' => $aboutPage->daily_visitors,
                            'label' => $aboutPage->visitors_label,
                            'formatted' => number_format($aboutPage->daily_visitors),
                        ],
                        'commercial_areas' => [
                            'value' => $aboutPage->commercial_areas,
                            'label' => $aboutPage->commercial_label,
                            'formatted' => number_format($aboutPage->commercial_areas),
                        ],
                    ],
                    'main_sections' => [
                        'section1' => [
                            'title' => $aboutPage->main_section1_title,
                            'description' => $aboutPage->main_section1_description,
                            'image_url' => $aboutPage->main_section1_image_url,
                            'image_alt_text' => $aboutPage->main_section1_image_alt_text,
                        ],
                        'section2' => [
                            'title' => $aboutPage->main_section2_title,
                            'description' => $aboutPage->main_section2_description,
                            'image_url' => $aboutPage->main_section2_image_url,
                            'image_alt_text' => $aboutPage->main_section2_image_alt_text,
                        ],
                    ],
                    'executive_summary' => $executiveSummaryItems,
                    'functions' => $functionItems,
                    'services' => $serviceItems,
                ],
                'seo' => [
                    'meta_title' => $aboutPage->meta_title_display,
                    'meta_description' => $aboutPage->meta_description_display,
                    'meta_keywords' => $aboutPage->meta_keywords_display,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'About Us page data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve About Us page data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Our Services page data
     */
    public function getServicesPage(): JsonResponse
    {
        try {
            $servicesPage = ServicesPageSetting::first();
            
            if (!$servicesPage || !$servicesPage->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Services page is not available'
                ], 404);
            }

            $serviceSections = ServiceSection::active()->ordered()->get()->map(function ($section) {
                return [
                    'id' => $section->id,
                    'title' => $section->title,
                    'description' => $section->description,
                    'image_url' => $section->image_url,
                    'image_alt_text' => $section->image_alt_text,
                    'layout' => $section->layout,
                    'layout_text' => $section->layout_text,
                    'order' => $section->order,
                ];
            });

            $data = [
                'page_content' => [
                    'banner' => [
                        'image_url' => $servicesPage->banner_image_url,
                        'alt_text' => $servicesPage->banner_alt_text,
                    ],
                    'service_sections' => $serviceSections,
                ],
                'seo' => [
                    'meta_title' => $servicesPage->meta_title_display,
                    'meta_description' => $servicesPage->meta_description_display,
                    'meta_keywords' => $servicesPage->meta_keywords_display,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Services page data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Services page data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get New Residents page data
     */
    public function getNewResidentsPage(): JsonResponse
    {
        try {
            $newResidentsPage = NewResidentsPageSetting::first();
            
            if (!$newResidentsPage || !$newResidentsPage->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'New Residents page is not available'
                ], 404);
            }

            $categories = PracticalInfoCategory::active()->ordered()->with('places')->get()->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'title' => $category->title,
                    'description' => $category->description,
                    'places_count' => $category->active_places_count,
                    'places' => $category->places->where('is_active', true)->map(function ($place) {
                        return [
                            'id' => $place->id,
                            'name' => $place->name,
                            'address' => $place->address,
                            'image_url' => $place->image_url,
                            'image_alt_text' => $place->image_alt_text,
                            'tags' => $place->tags,
                            'tags_display' => $place->tags_display,
                            'map_url' => $place->map_url,
                            'description' => $place->description,
                        ];
                    })->values(),
                ];
            });

            $transportationItems = TransportationItem::active()->ordered()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'order' => $item->order,
                ];
            });

            $etownSection = ETownSection::first();

            $data = [
                'page_content' => [
                    'banner' => [
                        'image_url' => $newResidentsPage->banner_image_url,
                        'alt_text' => $newResidentsPage->banner_alt_text,
                    ],
                    'neighborhood_guide' => [
                        'title' => $newResidentsPage->neighborhood_title,
                        'description' => $newResidentsPage->neighborhood_description,
                        'image_url' => $newResidentsPage->neighborhood_image_url,
                        'image_alt_text' => $newResidentsPage->neighborhood_image_alt_text,
                    ],
                    'practical_info_categories' => $categories,
                    'transportation' => $transportationItems,
                    'mobile_apps' => [
                        'google_play' => [
                            'url' => $etownSection?->google_play_url,
                        ],
                        'apple_store' => [
                            'url' => $etownSection?->apple_store_url,
                        ],
                    ],
                ],
                'seo' => [
                    'meta_title' => $newResidentsPage->meta_title_display,
                    'meta_description' => $newResidentsPage->meta_description_display,
                    'meta_keywords' => $newResidentsPage->meta_keywords_display,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'New Residents page data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve New Residents page data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Partnership page data
     */
    public function getPartnershipPage(): JsonResponse
    {
        try {
            $partnershipPage = PartnershipPageSetting::first();
            
            if (!$partnershipPage || !$partnershipPage->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Partnership page is not available'
                ], 404);
            }

            $partnershipItems = PartnershipItem::active()->ordered()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'image_url' => $item->image_url,
                    'image_alt_text' => $item->image_alt_text,
                    'order' => $item->order,
                ];
            });

            $data = [
                'page_content' => [
                    'banner' => [
                        'image_url' => $partnershipPage->banner_image_url,
                        'alt_text' => $partnershipPage->banner_alt_text,
                        'title' => $partnershipPage->banner_title,
                    ],
                    'partnership_items' => $partnershipItems,
                ],
                'seo' => [
                    'meta_title' => $partnershipPage->meta_title_display,
                    'meta_description' => $partnershipPage->meta_description_display,
                    'meta_keywords' => $partnershipPage->meta_keywords_display,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Partnership page data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Partnership page data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get E-Town page data
     */
    // public function getETownPage(): JsonResponse
    // {
    //     try {
    //         $etownSection = ETownSection::first();
            
    //         if (!$etownSection) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'E-Town page is not available'
    //             ], 404);
    //         }

    //         $data = [
    //             'company' => $this->getCompanyData(),
    //             'page_content' => [
    //                 'section_title' => $etownSection->section_title,
    //                 'description' => $etownSection->description,
    //                 'app_mockup_image_url' => $etownSection->app_mockup_image_url,
    //                 'app_mockup_alt_text' => $etownSection->app_mockup_alt_text,
    //                 'google_play_url' => $etownSection->google_play_url,
    //                 'app_store_url' => $etownSection->app_store_url,
    //                 'has_app_store_links' => $etownSection->has_app_store_links,
    //             ],
    //         ];

    //         return response()->json([
    //             'success' => true,
    //             'data' => $data,
    //             'message' => 'E-Town page data retrieved successfully'
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve E-Town page data',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Get News page data with pagination
     */
    // public function getNewsPage(Request $request): JsonResponse
    // {
    //     try {
    //         $perPage = $request->get('per_page', 10);
    //         $categorySlug = $request->get('category');

    //         $query = News::with('category')->published()->orderBy('published_at', 'desc');

    //         if ($categorySlug) {
    //             $category = NewsCategory::where('slug', $categorySlug)->first();
    //             if ($category) {
    //                 $query->where('category_id', $category->id);
    //             }
    //         }

    //         $news = $query->paginate($perPage);
    //         $categories = NewsCategory::active()->ordered()->get();

    //         $newsData = $news->getCollection()->map(function ($article) {
    //             return [
    //                 'id' => $article->id,
    //                 'title' => $article->title,
    //                 'slug' => $article->slug,
    //                 'excerpt' => $article->excerpt,
    //                 'content' => $article->content,
    //                 'image_url' => $article->image_url,
    //                 'category' => [
    //                     'id' => $article->category->id,
    //                     'name' => $article->category->name,
    //                     'slug' => $article->category->slug,
    //                 ],
    //                 'published_at' => $article->published_at->format('d F Y'),
    //                 'published_at_formatted' => $article->published_at->diffForHumans(),
    //                 'is_featured' => $article->is_featured,
    //             ];
    //         });

    //         $categoriesData = $categories->map(function ($category) {
    //             return [
    //                 'id' => $category->id,
    //                 'name' => $category->name,
    //                 'slug' => $category->slug,
    //                 'description' => $category->description,
    //                 'published_news_count' => $category->published_news_count,
    //             ];
    //         });

    //         $data = [
    //             'company' => $this->getCompanyData(),
    //             'page_content' => [
    //                 'news' => $newsData,
    //                 'categories' => $categoriesData,
    //                 'current_category' => $categorySlug ? $categories->firstWhere('slug', $categorySlug) : null,
    //             ],
    //             'pagination' => [
    //                 'current_page' => $news->currentPage(),
    //                 'last_page' => $news->lastPage(),
    //                 'per_page' => $news->perPage(),
    //                 'total' => $news->total(),
    //                 'from' => $news->firstItem(),
    //                 'to' => $news->lastItem(),
    //                 'has_more_pages' => $news->hasMorePages(),
    //             ],
    //         ];

    //         return response()->json([
    //             'success' => true,
    //             'data' => $data,
    //             'message' => 'News page data retrieved successfully'
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve News page data',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Get single news article by slug
     */
    // public function getNewsArticle($slug): JsonResponse
    // {
    //     try {
    //         $article = News::with('category')
    //                       ->published()
    //                       ->where('slug', $slug)
    //                       ->first();

    //         if (!$article) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'News article not found'
    //             ], 404);
    //         }

    //         // Get related news (same category, excluding current article)
    //         $relatedNews = News::with('category')
    //                           ->published()
    //                           ->where('category_id', $article->category_id)
    //                           ->where('id', '!=', $article->id)
    //                           ->orderBy('published_at', 'desc')
    //                           ->limit(3)
    //                           ->get();

    //         $articleData = [
    //             'id' => $article->id,
    //             'title' => $article->title,
    //             'slug' => $article->slug,
    //             'content' => $article->content,
    //             'image_url' => $article->image_url,
    //             'category' => [
    //                 'id' => $article->category->id,
    //                 'name' => $article->category->name,
    //                 'slug' => $article->category->slug,
    //             ],
    //             'published_at' => $article->published_at->format('d F Y'),
    //             'published_at_formatted' => $article->published_at->diffForHumans(),
    //             'is_featured' => $article->is_featured,
    //             'seo' => [
    //                 'meta_title' => $article->meta_title_display,
    //                 'meta_description' => $article->meta_description_display,
    //                 'meta_keywords' => $article->meta_keywords_display,
    //             ],
    //         ];

    //         $relatedNewsData = $relatedNews->map(function ($relatedArticle) {
    //             return [
    //                 'id' => $relatedArticle->id,
    //                 'title' => $relatedArticle->title,
    //                 'slug' => $relatedArticle->slug,
    //                 'excerpt' => $relatedArticle->excerpt,
    //                 'image_url' => $relatedArticle->image_url,
    //                 'published_at' => $relatedArticle->published_at->format('d F Y'),
    //                 'published_at_formatted' => $relatedArticle->published_at->diffForHumans(),
    //             ];
    //         });

    //         $data = [
    //             'company' => $this->getCompanyData(),
    //             'article' => $articleData,
    //             'related_news' => $relatedNewsData,
    //         ];

    //         return response()->json([
    //             'success' => true,
    //             'data' => $data,
    //             'message' => 'News article retrieved successfully'
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve news article',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Get Promos page data
     */
    public function getPromosPage(): JsonResponse
    {
        try {
            $promos = Promo::active()->ordered()->get()->map(function ($promo) {
                return [
                    'id' => $promo->id,
                    'title' => $promo->title_display,
                    'description' => $promo->description_display,
                    'image_url' => $promo->image_url,
                    'order' => $promo->order,
                ];
            });

            $data = [
                'page_content' => [
                    'promos' => $promos,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Promos page data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Promos page data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLayoutData(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->getCompanyData(),
                'message' => 'Layout retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve layout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}