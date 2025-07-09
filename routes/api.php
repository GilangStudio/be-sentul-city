<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\HomePageApiController;
use App\Http\Controllers\API\CareerApplicationApiController;

// Homepage API Routes
Route::prefix('homepage')->group(function () {
    Route::get('/', [HomePageApiController::class, 'getHomepageData']);
    
    // Individual section endpoints for granular access
    Route::get('/banners', [HomePageApiController::class, 'getBanners']);
    Route::get('/news', [HomePageApiController::class, 'getNews']);
    Route::get('/promos', [HomePageApiController::class, 'getPromos']);
    Route::get('/neighborhood-guide', [HomePageApiController::class, 'getNeighborhoodGuide']);
    Route::get('/etown-section', [HomePageApiController::class, 'getETownSection']);
    Route::get('/about-us', [HomePageApiController::class, 'getAboutUs']);
});

Route::prefix('career')->group(function () {
    Route::get('/page', [CareerApplicationApiController::class, 'getPageSettings']);
    Route::get('/positions', [CareerApplicationApiController::class, 'getPositions']);
    Route::get('/positions/{position}', [CareerApplicationApiController::class, 'getPosition']);
    Route::post('/apply', [CareerApplicationApiController::class, 'submitApplication']);
});