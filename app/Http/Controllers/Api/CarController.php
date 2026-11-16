<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiFormat;
use App\Support\Catalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CarController extends Controller
{
    private const SORTS = [
        'newest' => ['created_at', true],
        'price_asc' => ['price', false],
        'price_desc' => ['price', true],
        'mileage_asc' => ['mileage', false],
        'year_desc' => ['production_year', true],
    ];

    /** GET /api/v1/cars.json */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'brand' => ['sometimes', 'string', 'max:40'],
            'body' => ['sometimes', Rule::in(array_keys(Catalog::BODY))],
            'fuel' => ['sometimes', 'string'],
            'fuel.*' => [Rule::in(array_keys(Catalog::FUEL))],
            'gearbox' => ['sometimes', Rule::in(array_keys(Catalog::GEARBOX))],
            'condition' => ['sometimes', Rule::in(['new', 'used'])],
            'status' => ['sometimes', Rule::in(['for_sale', 'available', 'reserved', 'sold', 'all'])],
            'min_price' => ['sometimes', 'integer', 'min:0'],
            'max_price' => ['sometimes', 'integer', 'min:0'],
            'max_mileage' => ['sometimes', 'integer', 'min:0'],
            'year_from' => ['sometimes', 'integer', 'between:1990,2100'],
            'sort' => ['sometimes', Rule::in(array_keys(self::SORTS))],
        ]);

        // fuel accepts a comma-separated list: fuel=hybrid,electric
        $fuels = $request->filled('fuel') ? explode(',', strtolower($request->query('fuel'))) : [];
        $badFuel = array_diff($fuels, array_keys(Catalog::FUEL));

        if ($validator->fails() || $badFuel) {
            $details = $validator->errors()->toArray();
            if ($badFuel) {
                $details['fuel'] = ['Use one or more of: '.implode(', ', array_keys(Catalog::FUEL)).'.'];
            }

            return ApiFormat::error(422, 'invalid_query', 'Some filters are not valid.', $details);
        }

        $status = $request->query('status', 'for_sale');
        $cars = Catalog::cars()
            ->filter(fn ($c) => match ($status) {
                'for_sale' => $c['status'] !== 'sold',
                'all' => true,
                default => $c['status'] === $status,
            })
            ->when($request->filled('brand'), fn ($q) => $q->filter(fn ($c) => mb_strtolower($c['brand']) === mb_strtolower($request->query('brand'))))
            ->when($request->filled('body'), fn ($q) => $q->where('car_category', $request->query('body')))
            ->when($fuels, fn ($q) => $q->whereIn('engine_type', $fuels))
            ->when($request->filled('gearbox'), fn ($q) => $q->where('transmission', $request->query('gearbox')))
            ->when($request->filled('condition'), fn ($q) => $q->where('is_used', $request->query('condition') === 'used'))
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', (int) $request->query('min_price')))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', (int) $request->query('max_price')))
            ->when($request->filled('max_mileage'), fn ($q) => $q->where('mileage', '<=', (int) $request->query('max_mileage')))
            ->when($request->filled('year_from'), fn ($q) => $q->where('production_year', '>=', (int) $request->query('year_from')));

        $sort = $request->query('sort', 'newest');
        [$field, $descending] = self::SORTS[$sort];
        $cars = $cars->sortBy($field, SORT_REGULAR, $descending)->values();

        return response()->json([
            'data' => $cars->map(fn ($c) => ApiFormat::carSummary($c))->all(),
            'meta' => [
                'total' => $cars->count(),
                'sort' => $sort,
                'filters' => array_filter($request->only(['brand', 'body', 'fuel', 'gearbox', 'condition', 'min_price', 'max_price', 'max_mileage', 'year_from'])),
                'status' => $status,
            ],
        ]);
    }

    /** GET /api/v1/cars/{slug}.json */
    public function show(string $slug): JsonResponse
    {
        $car = Catalog::cars()->firstWhere('slug', $slug);

        return $car
            ? response()->json(['data' => ApiFormat::carDetail($car)])
            : $this->notFound($slug);
    }

    /** GET /api/v1/cars/{slug}/additions.json */
    public function additions(string $slug): JsonResponse
    {
        $car = Catalog::cars()->firstWhere('slug', $slug);
        if (! $car) {
            return $this->notFound($slug);
        }
        $additions = Catalog::additionsFor($car)->map(fn ($a) => ApiFormat::addition($a))->values();

        return response()->json(['data' => $additions->all(), 'meta' => ['car' => $slug, 'total' => $additions->count()]]);
    }

    /** GET /api/v1/cars/{slug}/finance.json?deposit_percent=20&months=60 */
    public function finance(Request $request, string $slug): JsonResponse
    {
        $car = Catalog::cars()->firstWhere('slug', $slug);
        if (! $car) {
            return $this->notFound($slug);
        }

        $validator = Validator::make($request->query(), [
            'deposit_percent' => ['sometimes', 'integer', 'between:0,50'],
            'months' => ['sometimes', 'integer', Rule::in([24, 36, 48, 60, 72])],
        ]);
        if ($validator->fails()) {
            return ApiFormat::error(422, 'invalid_query', 'The finance terms are not valid.', $validator->errors()->toArray());
        }

        return response()->json([
            'data' => ApiFormat::finance(
                $car['price'],
                (int) $request->query('deposit_percent', Catalog::FINANCE['deposit']),
                (int) $request->query('months', Catalog::FINANCE['months']),
            ),
        ]);
    }

    private function notFound(string $slug): JsonResponse
    {
        return ApiFormat::error(404, 'car_not_found', "No car with the slug \"{$slug}\".");
    }
}
