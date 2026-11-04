<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Read-only access to the hard-coded dealership data. This is the one
 * place to swap for Group 2's API later: pages only talk to this class.
 */
class Catalog
{
    public const FUEL = ['petrol' => 'Petrol', 'diesel' => 'Diesel', 'hybrid' => 'Hybrid', 'electric' => 'Electric'];

    public const GEARBOX = ['automatic' => 'Automatic', 'manual' => 'Manual'];

    public const BODY = ['suv' => 'SUV', 'estate' => 'Estate', 'hatchback' => 'Hatchback', 'saloon' => 'Saloon'];

    /** Representative finance example shown on cards and the calculator. */
    public const FINANCE = ['apr' => 6.9, 'deposit' => 20, 'months' => 60];

    public const CAR_STATUS = ['available' => 'Available', 'reserved' => 'Reserved', 'sold' => 'Sold'];

    public const SALE_STATUS = ['requested' => 'Requested', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];

    public const PAYMENT_STATUS = ['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'];

    private static ?Collection $cars = null;

    private static ?array $backoffice = null;

    /** @return Collection<int, array> */
    public static function cars(): Collection
    {
        return self::$cars ??= collect(require resource_path('data/cars.php'))
            ->map(fn (array $car) => self::present($car))
            ->sortByDesc('created_at')
            ->values();
    }

    /** Cars a customer can still buy, newest first. */
    public static function forSale(): Collection
    {
        return self::cars()->where('status', '!=', 'sold')->values();
    }

    public static function car(string $slug): array
    {
        return self::cars()->firstWhere('slug', $slug) ?? abort(404);
    }

    public static function carById(int $id): ?array
    {
        return self::cars()->firstWhere('id', $id);
    }

    public static function similar(array $car, int $limit = 3): Collection
    {
        return self::forSale()
            ->where('slug', '!=', $car['slug'])
            ->sortBy(fn ($c) => ($c['car_category'] === $car['car_category'] ? 0 : 1) * 100000 + abs($c['price'] - $car['price']))
            ->take($limit)
            ->values();
    }

    public static function additionsFor(array $car): Collection
    {
        $ids = self::backoffice('car_additions')[$car['engine_type'] === 'electric' ? 'electric' : 'combustion'];

        return self::additions()->whereIn('id', $ids)->where('is_active', true)->values();
    }

    public static function additions(): Collection
    {
        return collect(self::backoffice('additions'));
    }

    public static function users(): Collection
    {
        return collect(self::backoffice('users'))->map(fn ($u) => $u + [
            'name' => $u['first_name'].' '.$u['last_name'],
            'initials' => mb_substr($u['first_name'], 0, 1).mb_substr($u['last_name'], 0, 1),
            'role_label' => self::backoffice('roles')[$u['role']],
        ]);
    }

    public static function user(int $id): ?array
    {
        return self::users()->firstWhere('id', $id);
    }

    public static function salesTeam(): Collection
    {
        return self::users()->where('role', 'salesman')->values();
    }

    public static function sales(): Collection
    {
        return collect(self::backoffice('sales'))->map(fn ($s) => $s + [
            'car' => self::carById($s['car_id']),
            'buyer' => self::user($s['buyer_id']),
            'salesman' => self::user($s['salesman_id']),
            'addition_names' => self::additions()->whereIn('id', $s['additions'])->pluck('name')->all(),
            'requested_label' => self::date($s['requested_at']),
        ])->sortByDesc('requested_at')->values();
    }

    public static function roles(): array
    {
        return self::backoffice('roles');
    }

    /** Slim car list for the browser: favourites, checkout and backoffice scripts. */
    public static function clientJson(): array
    {
        return self::cars()->map(fn ($c) => [
            'id' => $c['id'],
            'slug' => $c['slug'],
            'name' => $c['name'],
            'title' => $c['title'],
            'specs' => $c['specs_line'],
            'price' => $c['price'],
            'status' => $c['status'],
            'image' => $c['image'],
            'cutout' => $c['cutout'],
            'thumb' => $c['cutout'] ?? $c['image'],
            'url' => $c['url'],
            'extrasUrl' => page('checkout.extras', $c['slug']),
            'variant' => $c['variant'],
            'year' => $c['production_year'],
            'condition' => $c['condition_label'],
            'mileage' => $c['mileage'],
            'fuel' => $c['fuel_label'],
            'gearbox' => $c['gearbox_label'],
            'gears' => $c['number_of_gears'],
            'hp' => $c['horsepower'],
            'body' => $c['body_label'],
            'drive' => $c['drivetrain'],
            'consumption' => $c['consumption'],
            'colour' => $c['colour'],
            'owners' => $c['owners'],
            'engine' => $c['engine'],
            'monthly' => $c['monthly'],
            'additions' => self::additionsFor($c)->map(fn ($a) => ['id' => $a['id'], 'name' => $a['name'], 'price' => $a['price']])->all(),
        ])->values()->all();
    }

    /** Everything the browser scripts read, as safe inline JSON. */
    public static function pageJson(array $routes = []): string
    {
        return json_encode([
            'cars' => self::clientJson(),
            'routes' => $routes + [
                'home' => page('home'),
                'login' => page('login'),
                'account' => page('account'),
                'confirmed' => page('checkout.confirmed'),
                'sales' => page('sales.dashboard'),
                'admin' => page('admin.dashboard'),
                'compare' => page('compare'),
            ],
            'vat' => config('riva.vat_rate'),
            'finance' => self::FINANCE,
            'orders' => self::sales()->map(fn ($s) => [
                'id' => $s['id'],
                'slug' => $s['car']['slug'],
                'email' => $s['buyer']['email'],
                'extras' => $s['addition_names'],
                'total' => $s['total_price'],
                'status' => $s['status'],
                'payment' => $s['payment_status'],
                'ref' => $s['payment_reference'],
                'date' => $s['requested_label'],
            ])->values(),
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
    }

    /** Monthly payment for the representative example (or the given terms). */
    public static function monthly(int $price, ?int $depositPercent = null, ?int $months = null): int
    {
        $deposit = $depositPercent ?? self::FINANCE['deposit'];
        $months ??= self::FINANCE['months'];
        $principal = $price * (1 - $deposit / 100);
        $rate = self::FINANCE['apr'] / 100 / 12;

        return (int) round($principal * $rate / (1 - (1 + $rate) ** -$months));
    }

    public static function date(string $value, bool $time = true): string
    {
        return date($time ? 'j M Y, H:i' : 'j M Y', strtotime($value));
    }

    private static function present(array $car): array
    {
        $cutout = file_exists(public_path("images/cars/{$car['slug']}/cutout.webp")) ? img("images/cars/{$car['slug']}/cutout.webp") : null;

        $images = collect(range(1, $car['images']))->map(fn ($n) => [
            'src' => img("images/cars/{$car['slug']}/{$n}.jpg"),
            'alt' => "{$car['brand']} {$car['model']}, photo {$n}",
            'studio' => false,
        ])->all();

        // The studio cut-out leads the gallery; real photos follow
        $gallery = $cutout
            ? [['src' => $cutout, 'alt' => "{$car['brand']} {$car['model']}, studio view", 'studio' => true], ...$images]
            : $images;

        return $car + [
            'name' => "{$car['brand']} {$car['model']}",
            'title' => "{$car['production_year']} {$car['brand']} {$car['model']}",
            'fuel_label' => self::FUEL[$car['engine_type']],
            'gearbox_label' => self::GEARBOX[$car['transmission']],
            'body_label' => self::BODY[$car['car_category']],
            'status_label' => self::CAR_STATUS[$car['status']],
            'condition_label' => $car['is_used'] ? 'Used' : 'New',
            'specs_line' => implode(', ', [
                $car['production_year'],
                $car['is_used'] ? km($car['mileage']) : 'New',
                self::FUEL[$car['engine_type']],
                self::GEARBOX[$car['transmission']],
            ]),
            'gallery' => $gallery,
            'image' => $images[0]['src'],
            // Studio cut-out (background removed by tools/cutout.py), if there is one
            'cutout' => $cutout,
            'url' => page('cars.show', $car['slug']),
            'monthly' => self::monthly($car['price']),
            'salesman' => self::user($car['salesman_id']),
            'listed_label' => self::date($car['created_at'], false),
        ];
    }

    private static function backoffice(string $key): array
    {
        self::$backoffice ??= require resource_path('data/backoffice.php');

        return self::$backoffice[$key];
    }
}
