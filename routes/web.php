<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NewResidentsController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\ServicesPageController;
use App\Http\Controllers\PracticalInfoPlaceController;
use App\Http\Controllers\TransportationItemController;
use App\Http\Controllers\PracticalInfoCategoryController;

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProcess']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('pages.dashboard.index');
    })->name('dashboard');

    // Home Page Management Routes
    Route::prefix('home-page')->name('home-page.')->group(function () {
        Route::get('/', [HomePageController::class, 'index'])->name('index');
        Route::post('/', [HomePageController::class, 'store'])->name('store');
        Route::put('/', [HomePageController::class, 'update'])->name('update');
        Route::delete('/', [HomePageController::class, 'destroy'])->name('destroy');
    });

    // New Residents Management Routes
    Route::prefix('new-residents')->name('new-residents.')->group(function () {
        Route::get('/', [NewResidentsController::class, 'index'])->name('index');
        Route::post('/', [NewResidentsController::class, 'updateOrCreate'])->name('updateOrCreate');
        Route::delete('/', [NewResidentsController::class, 'destroy'])->name('destroy');

        // Practical Info Categories
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [PracticalInfoCategoryController::class, 'index'])->name('index');
            Route::post('/', [PracticalInfoCategoryController::class, 'store'])->name('store');
            Route::put('/{category}', [PracticalInfoCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [PracticalInfoCategoryController::class, 'destroy'])->name('destroy');
            Route::post('/update-order', [PracticalInfoCategoryController::class, 'updateOrder'])->name('update-order');
        });

        // Practical Info Places - UPDATE: Gunakan parameter name yang konsisten
        Route::prefix('places')->name('places.')->group(function () {
            Route::get('/', [PracticalInfoPlaceController::class, 'index'])->name('index');
            Route::get('/create', [PracticalInfoPlaceController::class, 'create'])->name('create');
            Route::post('/', [PracticalInfoPlaceController::class, 'store'])->name('store');
            Route::get('/{place}/edit', [PracticalInfoPlaceController::class, 'edit'])->name('edit');
            Route::put('/{place}', [PracticalInfoPlaceController::class, 'update'])->name('update');
            Route::delete('/{place}', [PracticalInfoPlaceController::class, 'destroy'])->name('destroy');
        });

        // Transportation Items
        Route::prefix('transportation')->name('transportation.')->group(function () {
            Route::get('/', [TransportationItemController::class, 'index'])->name('index');
            Route::post('/', [TransportationItemController::class, 'store'])->name('store');
            Route::put('/{transportationItem}', [TransportationItemController::class, 'update'])->name('update');
            Route::delete('/{transportationItem}', [TransportationItemController::class, 'destroy'])->name('destroy');
            Route::post('/update-order', [TransportationItemController::class, 'updateOrder'])->name('update-order');
        });
    });

    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
        Route::put('/{news}', [NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');

        Route::prefix('category')->name('categories.')->group(function () {
            Route::get('/', [NewsCategoryController::class, 'index'])->name('index');
            Route::post('/', [NewsCategoryController::class, 'store'])->name('store');
            Route::put('/{category}', [NewsCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [NewsCategoryController::class, 'destroy'])->name('destroy');
            Route::post('/update-order', [NewsCategoryController::class, 'updateOrder'])->name('update-order');
        });
    });

    Route::prefix('promos')->name('promos.')->group(function () {
        Route::get('/', [PromoController::class, 'index'])->name('index');
        Route::post('/', [PromoController::class, 'store'])->name('store');
        Route::put('/{promo}', [PromoController::class, 'update'])->name('update');
        Route::delete('/{promo}', [PromoController::class, 'destroy'])->name('destroy');
        Route::post('/update-order', [PromoController::class, 'updateOrder'])->name('update-order');
    });

    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServicesPageController::class, 'index'])->name('index');
        Route::post('/', [ServicesPageController::class, 'updateOrCreate'])->name('updateOrCreate');
        
        // Service Sections Routes - Updated to use separate pages
        Route::prefix('sections')->name('sections.')->group(function () {
            Route::get('/create', [ServicesPageController::class, 'createSections'])->name('create');
            Route::post('/', [ServicesPageController::class, 'storeSections'])->name('store');
            Route::get('/{section}/edit', [ServicesPageController::class, 'editSections'])->name('edit');
            Route::put('/{section}', [ServicesPageController::class, 'updateSections'])->name('update');
            Route::delete('/{section}', [ServicesPageController::class, 'destroySections'])->name('destroy');
            Route::post('/update-order', [ServicesPageController::class, 'updateSectionsOrder'])->name('update-order');
        });
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [SettingsController::class, 'updatePassword'])->name('password.update');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
