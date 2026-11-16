<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiFormat;
use App\Support\Catalog;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    /** GET /api/v1/additions.json: every extra that is currently offered. */
    public function additions(): JsonResponse
    {
        $additions = Catalog::additions()->where('is_active', true)->map(fn ($a) => ApiFormat::addition($a))->values();

        return response()->json(['data' => $additions->all(), 'meta' => ['total' => $additions->count()]]);
    }

    /** GET /api/v1/filters.json: the values the car list can be filtered by, with counts. */
    public function filters(): JsonResponse
    {
        $stock = Catalog::forSale();
        $count = fn (string $field, array $labels) => collect($labels)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label, 'count' => $stock->where($field, $value)->count()])
            ->values()->all();

        return response()->json([
            'data' => [
                'makes' => $stock->groupBy('brand')->map(fn ($cars, $brand) => ['value' => $brand, 'label' => $brand, 'count' => $cars->count()])->sortKeys()->values()->all(),
                'body' => $count('car_category', Catalog::BODY),
                'fuel' => $count('engine_type', Catalog::FUEL),
                'gearbox' => $count('transmission', Catalog::GEARBOX),
                'condition' => [
                    ['value' => 'used', 'label' => 'Used', 'count' => $stock->where('is_used', true)->count()],
                    ['value' => 'new', 'label' => 'New', 'count' => $stock->where('is_used', false)->count()],
                ],
                'price' => ['min' => $stock->min('price'), 'max' => $stock->max('price'), 'currency' => 'EUR'],
                'production_year' => ['min' => $stock->min('production_year'), 'max' => $stock->max('production_year')],
                'sort' => ['newest', 'price_asc', 'price_desc', 'mileage_asc', 'year_desc'],
            ],
            'meta' => ['cars_for_sale' => $stock->count()],
        ]);
    }

    /** GET /api/v1/index.json: what the API offers, for anyone exploring it. */
    public function index(): JsonResponse
    {
        return response()->json([
            'name' => 'Riva Motors API',
            'version' => ApiFormat::VERSION,
            'documentation' => ApiFormat::url('api-docs'),
            'openapi' => ApiFormat::url('api/openapi.yaml'),
            'endpoints' => [
                'GET /api/v1/cars.json',
                'GET /api/v1/cars/{slug}.json',
                'GET /api/v1/cars/{slug}/additions.json',
                'GET /api/v1/cars/{slug}/finance.json',
                'GET /api/v1/additions.json',
                'GET /api/v1/filters.json',
                'POST /api/v1/sales',
            ],
        ]);
    }
}
