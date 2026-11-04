<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Built files keep fixed names (see vite.config.js) so a page cached by
        // the browser still finds its stylesheet after a deploy. A version from
        // the build manifest is added to the links, so fresh pages always load
        // the newest CSS and JS instead of an older cached copy.
        $built = glob(public_path('build/assets/*.{css,js}'), GLOB_BRACE) ?: [];
        $version = $built ? substr(md5(implode('', array_map('md5_file', $built))), 0, 10) : null;

        Vite::createAssetPathsUsing(fn (string $path, ?bool $secure = null) => asset($path, $secure).($version ? '?v='.$version : ''));
    }
}
