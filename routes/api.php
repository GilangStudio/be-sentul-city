<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\NewsApiController;
use App\Http\Controllers\API\PageApiController;
use App\Http\Controllers\API\HomePageApiController;
use App\Http\Controllers\API\CareerApplicationApiController;

//route for get header and footer
Route::get('/layout', [PageApiController::class, 'getLayoutData']);

Route::get('/homepage', [HomePageApiController::class, 'getHomepageData']);
Route::get('/about', [PageApiController::class, 'getAboutUsPage']);
Route::get('/partnership', [PageApiController::class, 'getPartnershipPage']);

Route::get('/services', [PageApiController::class, 'getServicesPage']);
Route::get('/new-residents', [PageApiController::class, 'getNewResidentsPage']);
// Route::get('/etown', [PageApiController::class, 'getETownPage']);
Route::get('/promo', [PageApiController::class, 'getPromosPage']);

Route::prefix('career')->group(function () {
    Route::get('/', [CareerApplicationApiController::class, 'getCareerPage']);
    Route::get('/positions', [CareerApplicationApiController::class, 'getPositions']);
    Route::get('/positions/{position}', [CareerApplicationApiController::class, 'getPosition']);
    Route::post('/apply', [CareerApplicationApiController::class, 'submitApplication']);
});

Route::prefix('news')->group(function () {
    Route::get('/', [NewsApiController::class, 'getNewsPage']);
    Route::get('/{slug}', [NewsApiController::class, 'getNewsArticle']);
});