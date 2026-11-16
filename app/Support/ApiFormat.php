<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

/**
 * Turns catalogue data into the public JSON shapes of the Riva Motors API
 * (documented in public/api/openapi.yaml). Links are absolute and point at
 * the public site, so the same JSON works from the PHP server and from the
 * static copy on GitHub Pages.
 */
class ApiFormat
{
    public const VERSION = '1.0.0';

    /** Absolute URL of a page or file on the public site. */
    public static function url(string $path = ''): string
    {
        return rtrim(config('riva.public_url'), '/').'/'.ltrim($path, '/');
    }

    public static function price(int $amount): array
    {
        return [
            'amount' => $amount,
            'currency' => 'EUR',
            'vat_included' => true,
            'vat_rate' => config('riva.vat_rate'),
        ];
    }

    /** The short form used in lists. */
    public static function carSummary(array $car): array
    {
        return [
            'id' => $car['id'],
            'slug' => $car['slug'],
            'brand' => $car['brand'],
            'model' => $car['model'],
            'variant' => $car['variant'],
            'title' => $car['title'],
            'production_year' => $car['production_year'],
            'condition' => $car['is_used'] ? 'used' : 'new',
            'mileage_km' => $car['mileage'],
            'engine_type' => $car['engine_type'],
            'transmission' => $car['transmission'],
            'horsepower' => $car['horsepower'],
            'car_category' => $car['car_category'],
            'colour' => $car['colour'],
            'price' => self::price($car['price']),
            'monthly_from' => $car['monthly'],
            'status' => $car['status'],
            'listed_at' => date(DATE_ATOM, strtotime($car['created_at'])),
            'images' => [
                'main' => self::url("images/cars/{$car['slug']}/1.jpg"),
                'cutout' => $car['cutout'] ? self::url("images/cars/{$car['slug']}/cutout.webp") : null,
            ],
            'links' => [
                'self' => self::url("api/v1/cars/{$car['slug']}.json"),
                'web' => self::url("cars/{$car['slug']}"),
            ],
        ];
    }

    /** The full form used for a single car. */
    public static function carDetail(array $car): array
    {
        $summary = self::carSummary($car);
        $summary['images']['gallery'] = array_map(
            fn ($n) => self::url("images/cars/{$car['slug']}/{$n}.jpg"),
            range(1, $car['images']),
        );

        return $summary + [
            'vin' => $car['vin'],
            'engine' => $car['engine'],
            'number_of_gears' => $car['number_of_gears'],
            'drivetrain' => $car['drivetrain'],
            'previous_owners' => $car['owners'],
            'consumption' => $car['consumption'],
            'description' => $car['description'],
            'equipment' => $car['equipment'],
            'salesperson' => $car['salesman'] ? [
                'id' => $car['salesman']['id'],
                'name' => $car['salesman']['name'],
            ] : null,
            'additions' => Catalog::additionsFor($car)->map(fn ($a) => self::addition($a))->values()->all(),
            'finance_example' => self::finance($car['price'], Catalog::FINANCE['deposit'], Catalog::FINANCE['months']),
        ];
    }

    public static function addition(array $addition): array
    {
        return [
            'id' => $addition['id'],
            'name' => $addition['name'],
            'description' => $addition['description'],
            'price' => self::price($addition['price']),
        ];
    }

    /** A loan quote: same formula as Catalog::monthly() and the calculator on the site. */
    public static function finance(int $price, int $depositPercent, int $months): array
    {
        $deposit = (int) round($price * $depositPercent / 100);
        $monthly = Catalog::monthly($price, $depositPercent, $months);
        $totalPayable = $monthly * $months + $deposit;

        return [
            'car_price' => $price,
            'currency' => 'EUR',
            'apr_percent' => Catalog::FINANCE['apr'],
            'deposit_percent' => $depositPercent,
            'deposit' => $deposit,
            'loan_amount' => $price - $deposit,
            'months' => $months,
            'monthly_payment' => $monthly,
            'total_payable' => $totalPayable,
            'total_interest' => $totalPayable - $price,
            'representative_example' => true,
        ];
    }

    /** Every error has the same shape: { "error": { "code", "message", "details" } }. */
    public static function error(int $status, string $code, string $message, array $details = []): JsonResponse
    {
        return response()->json([
            'error' => array_filter([
                'code' => $code,
                'message' => $message,
                'details' => $details ?: null,
            ], fn ($v) => $v !== null),
        ], $status);
    }
}
