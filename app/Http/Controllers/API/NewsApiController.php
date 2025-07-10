<?php

namespace App\Http\Controllers\API;

use App\Models\News;
use App\Models\Promo;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class NewsApiController extends Controller
{
    /**
     * Get news page data with pagination and filtering
     */
    public function getNewsPage(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 9), 50); // Max 50 items per page
            $categorySlug = $request->get('category');

            $query = News::with('category')->published()->orderBy('published_at', 'desc');

            // Filter by category if provided
            if ($categorySlug) {
                $category = NewsCategory::where('slug', $categorySlug)->active()->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Category not found or inactive'
                    ], 404);
                }
            }

            // Always get latest news (will appear on every page)
            $latestNewsQuery = clone $query;
            // $latestNews = $latestNewsQuery->first();
            $latestNews = $latestNewsQuery->latest()->first();

            // Get other news (skip the latest, then paginate)
            $otherNewsQuery = clone $query;
            if ($latestNews) {
                $otherNewsQuery->where('id', '!=', $latestNews->id);
            }
            
            // Paginate other news (excluding latest)
            // Since latest news is always shown, we can use full perPage for other news
            $otherNews = $otherNewsQuery->latest()->paginate($perPage);
            $categories = NewsCategory::active()->ordered()->get();

            // Format latest news (always present)
            $latestNewsData = null;
            if ($latestNews) {
                $latestNewsData = [
                    'id' => $latestNews->id,
                    'title' => $latestNews->title,
                    'slug' => $latestNews->slug,
                    'excerpt' => $latestNews->excerpt,
                    'image_url' => $latestNews->image_url,
                    'category' => [
                        'id' => $latestNews->category->id,
                        'name' => $latestNews->category->name,
                        'slug' => $latestNews->category->slug,
                    ],
                    'published_at' => $latestNews->published_at->format('d F Y'),
                    'published_at_formatted' => $latestNews->published_at->diffForHumans(),
                    'is_featured' => $latestNews->is_featured,
                ];
            }

            // Format other news
            $otherNewsData = $otherNews->getCollection()->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'excerpt' => $article->excerpt,
                    'image_url' => $article->image_url,
                    'category' => [
                        'id' => $article->category->id,
                        'name' => $article->category->name,
                        'slug' => $article->category->slug,
                    ],
                    'published_at' => $article->published_at->format('d F Y'),
                    'published_at_formatted' => $article->published_at->diffForHumans(),
                    'is_featured' => $article->is_featured,
                ];
            });

            $categoriesData = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            });

            $currentCategory = null;
            if ($categorySlug) {
                $currentCategory = $categories->firstWhere('slug', $categorySlug);
                if ($currentCategory) {
                    $currentCategory = [
                        'id' => $currentCategory->id,
                        'name' => $currentCategory->name,
                        'slug' => $currentCategory->slug,
                    ];
                }
            }

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

            $data = [
                'page_content' => [
                    'latest_news' => $latestNewsData,
                    'news' => $otherNewsData,
                    'promos' => $promos,
                    'categories' => $categoriesData,
                    'current_category' => $currentCategory,
                ],
                'pagination' => [
                    'current_page' => $otherNews->currentPage(),
                    'last_page' => $otherNews->lastPage(),
                    'per_page' => $otherNews->perPage(),
                    'total' => $otherNews->total(), // Total other news (excluding latest)
                    'total_with_latest' => $otherNews->total() + ($latestNews ? 1 : 0), // Total including latest
                    'from' => $otherNews->firstItem(),
                    'to' => $otherNews->lastItem(),
                    'has_more_pages' => $otherNews->hasMorePages(),
                    'prev_page_url' => $otherNews->previousPageUrl(),
                    'next_page_url' => $otherNews->nextPageUrl(),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'News page data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve news page data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single news article by slug with related news
     */
    public function getNewsArticle($slug): JsonResponse
    {
        try {
            $article = News::with('category')
                          ->published()
                          ->where('slug', $slug)
                          ->first();

            if (!$article) {
                return response()->json([
                    'success' => false,
                    'message' => 'News article not found'
                ], 404);
            }

            // Get related news (same category, excluding current article)
            $relatedNews = News::with('category')
                              ->published()
                              ->where('category_id', $article->category_id)
                              ->where('id', '!=', $article->id)
                              ->orderBy('published_at', 'desc')
                              ->limit(3)
                              ->get();

            // Get previous and next articles
            // $previousArticle = News::published()
            //                       ->where('published_at', '<', $article->published_at)
            //                       ->orderBy('published_at', 'desc')
            //                       ->select('id', 'title', 'slug')
            //                       ->first();

            // $nextArticle = News::published()
            //                   ->where('published_at', '>', $article->published_at)
            //                   ->orderBy('published_at', 'asc')
            //                   ->select('id', 'title', 'slug')
            //                   ->first();

            $articleData = [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'content' => $article->content,
                'excerpt' => $article->excerpt,
                'image_url' => $article->image_url,
                'category' => [
                    'id' => $article->category->id,
                    'name' => $article->category->name,
                    'slug' => $article->category->slug,
                    'description' => $article->category->description,
                ],
                'published_at' => $article->published_at->format('d F Y'),
                'published_at_formatted' => $article->published_at->diffForHumans(),
                'is_featured' => $article->is_featured,
                'seo' => [
                    'meta_title' => $article->meta_title_display,
                    'meta_description' => $article->meta_description_display,
                    'meta_keywords' => $article->meta_keywords_display,
                ],
            ];

            $relatedNewsData = $relatedNews->map(function ($relatedArticle) {
                return [
                    'id' => $relatedArticle->id,
                    'title' => $relatedArticle->title,
                    'slug' => $relatedArticle->slug,
                    'excerpt' => $relatedArticle->excerpt,
                    'image_url' => $relatedArticle->image_url,
                    'category' => [
                        'id' => $relatedArticle->category->id,
                        'name' => $relatedArticle->category->name,
                        'slug' => $relatedArticle->category->slug,
                    ],
                    'published_at' => $relatedArticle->published_at->format('d F Y'),
                    'published_at_formatted' => $relatedArticle->published_at->diffForHumans(),
                ];
            });

            // $navigationData = [
            //     'previous' => $previousArticle ? [
            //         'id' => $previousArticle->id,
            //         'title' => $previousArticle->title,
            //         'slug' => $previousArticle->slug,
            //     ] : null,
            //     'next' => $nextArticle ? [
            //         'id' => $nextArticle->id,
            //         'title' => $nextArticle->title,
            //         'slug' => $nextArticle->slug,
            //     ] : null,
            // ];

            $data = [
                'article' => $articleData,
                'related_news' => $relatedNewsData,
                // 'navigation' => $navigationData,
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'News article retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve news article',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get news categories
     */
    public function getNewsCategories(): JsonResponse
    {
        try {
            $categories = NewsCategory::active()
                                    ->ordered()
                                    ->withCount(['news' => function($query) {
                                        $query->published();
                                    }])
                                    ->get();

            $categoriesData = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'published_news_count' => $category->news_count,
                    'order' => $category->order,
                ];
            });

            $data = [
                'categories' => $categoriesData,
                'total_categories' => $categoriesData->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'News categories retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve news categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // /**
    //  * Get latest news for widgets/components
    //  */
    // public function getLatestNews(Request $request): JsonResponse
    // {
    //     try {
    //         $limit = min($request->get('limit', 5), 20); // Max 20 items
    //         $excludeId = $request->get('exclude_id'); // Exclude specific article ID

    //         $query = News::with('category')
    //                     ->published()
    //                     ->orderBy('published_at', 'desc');

    //         if ($excludeId) {
    //             $query->where('id', '!=', $excludeId);
    //         }

    //         $news = $query->limit($limit)->get();

    //         $newsData = $news->map(function ($article) {
    //             return [
    //                 'id' => $article->id,
    //                 'title' => $article->title,
    //                 'slug' => $article->slug,
    //                 'excerpt' => $article->excerpt,
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

    //         $data = [
    //             'latest_news' => $newsData,
    //             'total_count' => $newsData->count(),
    //         ];

    //         return response()->json([
    //             'success' => true,
    //             'data' => $data,
    //             'message' => 'Latest news retrieved successfully'
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve latest news',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // /**
    //  * Get featured news
    //  */
    // public function getFeaturedNews(): JsonResponse
    // {
    //     try {
    //         $featuredNews = News::with('category')
    //                            ->published()
    //                            ->featured()
    //                            ->first();

    //         if (!$featuredNews) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No featured news found'
    //             ], 404);
    //         }

    //         $newsData = [
    //             'id' => $featuredNews->id,
    //             'title' => $featuredNews->title,
    //             'slug' => $featuredNews->slug,
    //             'excerpt' => $featuredNews->excerpt,
    //             'content' => $featuredNews->content,
    //             'image_url' => $featuredNews->image_url,
    //             'category' => [
    //                 'id' => $featuredNews->category->id,
    //                 'name' => $featuredNews->category->name,
    //                 'slug' => $featuredNews->category->slug,
    //             ],
    //             'published_at' => $featuredNews->published_at->format('d F Y'),
    //             'published_at_formatted' => $featuredNews->published_at->diffForHumans(),
    //             'is_featured' => true,
    //         ];

    //         $data = [
    //             'company' => $this->getMinimalCompanyData(),
    //             'featured_news' => $newsData,
    //         ];

    //         return response()->json([
    //             'success' => true,
    //             'data' => $data,
    //             'message' => 'Featured news retrieved successfully'
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve featured news',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}