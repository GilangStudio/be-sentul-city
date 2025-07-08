<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Promo;
use App\Models\User;
use App\Models\HomePageBanner;
use App\Models\CareerPosition;
use App\Models\CareerApplication;
use App\Models\NewsCategory;
use App\Models\PartnershipItem;
use App\Models\ServiceSection;
use App\Models\PracticalInfoPlace;
use App\Models\TransportationItem;
use App\Models\AboutFunctionItem;
use App\Models\AboutServiceItem;
use App\Models\AboutExecutiveSummaryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Content Statistics
        $stats = [
            'total_news' => News::count(),
            'published_news' => News::published()->count(),
            'draft_news' => News::draft()->count(),
            'news_categories' => NewsCategory::active()->count(),
            'total_promos' => Promo::count(),
            'active_promos' => Promo::active()->count(),
            'homepage_banners' => HomePageBanner::active()->count(),
            'career_positions' => CareerPosition::open()->count(),
            'pending_applications' => CareerApplication::pending()->count(),
            'partnership_items' => PartnershipItem::active()->count(),
            'service_sections' => ServiceSection::active()->count(),
            'practical_places' => PracticalInfoPlace::active()->count(),
            'transportation_items' => TransportationItem::active()->count(),
            'about_functions' => AboutFunctionItem::active()->count(),
            'about_services' => AboutServiceItem::active()->count(),
            'executive_summary_items' => AboutExecutiveSummaryItem::active()->count(),
        ];

        // Recent Activities
        $recentNews = News::with('category')
                         ->orderBy('created_at', 'desc')
                         ->limit(5)
                         ->get();

        $recentApplications = CareerApplication::with('position')
                                             ->orderBy('applied_at', 'desc')
                                             ->limit(5)
                                             ->get();

        // News by Category Chart Data
        $newsByCategory = NewsCategory::withCount(['news' => function($query) {
            $query->published();
        }])
        ->having('news_count', '>', 0)
        ->orderBy('news_count', 'desc')
        ->limit(5)
        ->get();

        // Monthly content creation chart (last 6 months)
        $monthlyData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData->push([
                'month' => $date->format('M Y'),
                'news' => News::whereYear('created_at', $date->year)
                             ->whereMonth('created_at', $date->month)
                             ->count(),
                'promos' => Promo::whereYear('created_at', $date->year)
                               ->whereMonth('created_at', $date->month)
                               ->count(),
            ]);
        }

        return view('pages.dashboard.index', compact(
            'stats',
            'recentNews', 
            'recentApplications',
            'newsByCategory',
            'monthlyData'
        ));
    }
}