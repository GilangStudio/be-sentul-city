<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CareerApplicationApiController;


Route::prefix('career')->group(function () {
    Route::get('/page', [CareerApplicationApiController::class, 'getPageSettings']);
    Route::get('/positions', [CareerApplicationApiController::class, 'getPositions']);
    Route::get('/positions/{position}', [CareerApplicationApiController::class, 'getPosition']);
    Route::post('/apply', [CareerApplicationApiController::class, 'submitApplication']);
});