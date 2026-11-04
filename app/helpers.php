<?php

if (! function_exists('page')) {
    /**
     * Root-relative URL of a named route, including the base path.
     */
    function page(string $name, mixed $parameters = []): string
    {
        return rtrim(config('riva.base_path'), '/').route($name, $parameters, false);
    }
}

if (! function_exists('img')) {
    /**
     * Root-relative URL of a file in /public, including the base path.
     */
    function img(string $path): string
    {
        return rtrim(config('riva.base_path'), '/').'/'.ltrim($path, '/');
    }
}

if (! function_exists('euro')) {
    /**
     * €24,900 in listings, €24,900.00 in summaries and receipts.
     */
    function euro(int|float $amount, bool $cents = false): string
    {
        return '€'.number_format($amount, $cents ? 2 : 0, '.', ',');
    }
}

if (! function_exists('km')) {
    function km(int $value): string
    {
        return number_format($value, 0, '.', ',').' km';
    }
}
