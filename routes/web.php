<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\HomePageController;

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

    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
        Route::put('/{news}', [NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
