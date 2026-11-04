<?php

use App\Http\Controllers\BackofficeController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

/*
| Every page is a plain GET so the whole site can be exported to static
| HTML for GitHub Pages. Login, favourites, checkout and backoffice edits
| run in the browser (resources/js) against the hard-coded data.
*/

Route::controller(StorefrontController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/cinematic', 'cinematic')->name('cinematic');
    Route::get('/cars', 'inventory')->name('cars.index');
    Route::get('/cars/{slug}', 'show')->name('cars.show');
    Route::get('/compare', 'compare')->name('compare');
    Route::get('/cars/{slug}/extras', 'extras')->name('checkout.extras');
    Route::get('/cars/{slug}/pay', 'pay')->name('checkout.pay');
    Route::get('/order-confirmed', 'confirmed')->name('checkout.confirmed');
    Route::get('/sell-your-car', 'sell')->name('sell');
    Route::get('/login', 'login')->name('login');
    Route::get('/scan', 'scan')->name('scan');
    Route::get('/account', 'account')->name('account');
    Route::get('/photo-credits', 'credits')->name('credits');
    Route::get('/not-found', 'notFound')->name('not-found');
});

Route::controller(BackofficeController::class)->group(function () {
    Route::get('/sales', 'salesDashboard')->name('sales.dashboard');
    Route::get('/sales/cars', 'salesCars')->name('sales.cars');
    Route::get('/sales/cars/new', 'salesAddCar')->name('sales.cars.create');
    Route::get('/sales/orders', 'salesOrders')->name('sales.orders');

    Route::get('/admin', 'adminDashboard')->name('admin.dashboard');
    Route::get('/admin/cars', 'adminCars')->name('admin.cars');
    Route::get('/admin/cars/new', 'adminAddCar')->name('admin.cars.create');
    Route::get('/admin/sales', 'adminSales')->name('admin.sales');
    Route::get('/admin/users', 'adminUsers')->name('admin.users');
    Route::get('/admin/extras', 'adminExtras')->name('admin.extras');
});
