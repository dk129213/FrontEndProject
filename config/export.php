<?php

/*
| Static export for GitHub Pages (spatie/laravel-export). Crawling is off
| because links carry the Pages base path; every page is listed instead.
*/

$slugs = array_column(require __DIR__.'/../resources/data/cars.php', 'slug');

$carPages = array_merge(...array_map(fn ($slug) => [
    "cars/{$slug}",
    "cars/{$slug}/extras",
    "cars/{$slug}/pay",
], $slugs));

return [

    'crawl' => false,

    'use_streaming' => false,

    'paths' => array_merge([
        '/',
        'cinematic',
        'cars',
        'compare',
        'order-confirmed',
        'sell-your-car',
        'login',
        'scan',
        'account',
        'photo-credits',
        'not-found',
        'sales',
        'sales/cars',
        'sales/cars/new',
        'sales/orders',
        'admin',
        'admin/cars',
        'admin/cars/new',
        'admin/sales',
        'admin/users',
        'admin/extras',
    ], $carPages),

    'include_files' => [
        'public' => '',
    ],

    'exclude_file_patterns' => [
        '/\.php$/',
        '/mix-manifest\.json$/',
        '/\.htaccess$/',
        '/hot$/',
    ],

    'clean_before_export' => true,

    'disk' => null,

    'before' => [],

    'after' => [],

];
