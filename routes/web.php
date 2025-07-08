<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ETownController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CareerPageController;
use App\Http\Controllers\NewResidentsController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\ServicesPageController;
use App\Http\Controllers\AboutServicesController;
use App\Http\Controllers\AboutFunctionsController;
use App\Http\Controllers\CareerPositionController;
use App\Http\Controllers\ServiceSectionController;
use App\Http\Controllers\PartnershipItemController;
use App\Http\Controllers\PartnershipPageController;
use App\Http\Controllers\CareerApplicationController;
use App\Http\Controllers\PracticalInfoPlaceController;
use App\Http\Controllers\TransportationItemController;
use App\Http\Controllers\AboutExecutiveSummaryController;
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
        Route::put('/', [HomePageController::class, 'updateOrCreate'])->name('updateOrCreate');
    });

    // About Us Management Routes
    Route::prefix('about-us')->name('about-us.')->group(function () {
        Route::get('/', [AboutUsController::class, 'index'])->name('index');
        Route::post('/', [AboutUsController::class, 'updateOrCreate'])->name('updateOrCreate');
        
        // Executive Summary Items
        Route::prefix('executive-summary')->name('executive-summary.')->group(function () {
            Route::get('/', [AboutExecutiveSummaryController::class, 'index'])->name('index');
            Route::post('/', [AboutExecutiveSummaryController::class, 'store'])->name('store');
            Route::put('/{item}', [AboutExecutiveSummaryController::class, 'update'])->name('update');
            Route::delete('/{item}', [AboutExecutiveSummaryController::class, 'destroy'])->name('destroy');
            Route::post('/update-order', [AboutExecutiveSummaryController::class, 'updateOrder'])->name('update-order');
        });
        
        // Function Items
        Route::prefix('functions')->name('functions.')->group(function () {
            Route::get('/', [AboutFunctionsController::class, 'index'])->name('index');
            Route::post('/', [AboutFunctionsController::class, 'store'])->name('store');
            Route::put('/{item}', [AboutFunctionsController::class, 'update'])->name('update');
            Route::delete('/{item}', [AboutFunctionsController::class, 'destroy'])->name('destroy');
            Route::post('/update-order', [AboutFunctionsController::class, 'updateOrder'])->name('update-order');
        });
        
        // Service Items
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/', [AboutServicesController::class, 'index'])->name('index');
            Route::post('/', [AboutServicesController::class, 'store'])->name('store');
            Route::put('/{item}', [AboutServicesController::class, 'update'])->name('update');
            Route::delete('/{item}', [AboutServicesController::class, 'destroy'])->name('destroy');
            Route::post('/update-order', [AboutServicesController::class, 'updateOrder'])->name('update-order');
        });
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

    Route::prefix('partnerships')->name('partnerships.')->group(function () {
        // Partnership Page Settings
        Route::get('/', [PartnershipPageController::class, 'index'])->name('index');
        Route::post('/', [PartnershipPageController::class, 'updateOrCreate'])->name('updateOrCreate');
        Route::delete('/', [PartnershipPageController::class, 'destroy'])->name('destroy');
        
        // Partnership Items
        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [PartnershipItemController::class, 'index'])->name('index');
            Route::get('/create', [PartnershipItemController::class, 'create'])->name('create');
            Route::post('/', [PartnershipItemController::class, 'store'])->name('store');
            Route::get('/{item}/edit', [PartnershipItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [PartnershipItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [PartnershipItemController::class, 'destroy'])->name('destroy');
            Route::post('/update-order', [PartnershipItemController::class, 'updateOrder'])->name('update-order');
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
        
        // Service Sections Routes - Using dedicated controller
        Route::prefix('sections')->name('sections.')->group(function () {
            Route::get('/', [ServiceSectionController::class, 'index'])->name('index');
            Route::get('/create', [ServiceSectionController::class, 'create'])->name('create');
            Route::post('/create', [ServiceSectionController::class, 'store'])->name('store');
            Route::get('/{section}/edit', [ServiceSectionController::class, 'edit'])->name('edit');
            Route::put('/{section}', [ServiceSectionController::class, 'update'])->name('update');
            Route::delete('/{section}', [ServiceSectionController::class, 'destroy'])->name('destroy');
            Route::post('/update-order', [ServiceSectionController::class, 'updateOrder'])->name('update-order');
        });
    });

    Route::prefix('e-town')->name('e-town.')->group(function () {
        Route::get('/', [ETownController::class, 'index'])->name('index');
        Route::post('/', [ETownController::class, 'updateOrCreate'])->name('updateOrCreate');
        // Route::delete('/', [ETownController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('careers')->name('careers.')->group(function () {
        // Career Page Settings
        Route::get('/', [CareerPageController::class, 'index'])->name('index');
        Route::post('/', [CareerPageController::class, 'updateOrCreate'])->name('updateOrCreate');
        Route::delete('/', [CareerPageController::class, 'destroy'])->name('destroy');
        
        // Career Positions
        Route::prefix('positions')->name('positions.')->group(function () {
            Route::get('/', [CareerPositionController::class, 'index'])->name('index');
            Route::get('/create', [CareerPositionController::class, 'create'])->name('create');
            Route::post('/', [CareerPositionController::class, 'store'])->name('store');
            Route::get('/{position}/edit', [CareerPositionController::class, 'edit'])->name('edit');
            Route::put('/{position}', [CareerPositionController::class, 'update'])->name('update');
            Route::delete('/{position}', [CareerPositionController::class, 'destroy'])->name('destroy');
            Route::post('/update-order', [CareerPositionController::class, 'updateOrder'])->name('update-order');
            
            // Career Applications
            Route::prefix('{position}/applications')->name('applications.')->group(function () {
                Route::get('/', [CareerApplicationController::class, 'index'])->name('index');
                Route::get('/{application}', [CareerApplicationController::class, 'show'])->name('show');
                Route::patch('/{application}/status', [CareerApplicationController::class, 'updateStatus'])->name('update-status');
                Route::delete('/{application}', [CareerApplicationController::class, 'destroy'])->name('destroy');
                Route::get('/{application}/download-cv', [CareerApplicationController::class, 'downloadCv'])->name('download-cv');
                Route::post('/bulk-update-status', [CareerApplicationController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
            });
        });
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [SettingsController::class, 'updatePassword'])->name('password.update');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
