<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\SaleController;
use App\Support\ApiFormat;
use Illuminate\Support\Facades\Route;

/*
| Riva Motors API, version 1. Documented in public/api/openapi.yaml and at
| /api-docs. Read endpoints end in .json so the static copy on GitHub Pages
| has the same URLs as the PHP server. Filtering with query parameters and
| POST requests need the PHP server (php artisan serve).
*/

Route::prefix('v1')->name('api.')->group(function () {
    Route::get('index.json', [CatalogController::class, 'index'])->name('index');
    Route::get('cars.json', [CarController::class, 'index'])->name('cars.index');
    Route::get('cars/{slug}.json', [CarController::class, 'show'])->where('slug', '[a-z0-9-]+')->name('cars.show');
    Route::get('cars/{slug}/additions.json', [CarController::class, 'additions'])->where('slug', '[a-z0-9-]+')->name('cars.additions');
    Route::get('cars/{slug}/finance.json', [CarController::class, 'finance'])->where('slug', '[a-z0-9-]+')->name('cars.finance');
    Route::get('additions.json', [CatalogController::class, 'additions'])->name('additions');
    Route::get('filters.json', [CatalogController::class, 'filters'])->name('filters');
    Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
    Route::match(['get', 'put', 'patch', 'delete'], 'sales', fn () => ApiFormat::error(405, 'method_not_allowed', 'Use POST to create a sale.'));
});

// Anything else under /api gets the API's error shape instead of an HTML page
Route::any('{any}', fn () => ApiFormat::error(404, 'not_found', 'This API endpoint does not exist. See /api-docs.'))->where('any', '.*');
