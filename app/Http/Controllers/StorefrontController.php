<?php

namespace App\Http\Controllers;

use App\Support\Catalog;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Contracts\View\View;

class StorefrontController extends Controller
{
    public function home(): View
    {
        return view('storefront.home', $this->homeData() + ['cinematic' => false]);
    }

    /** Second homepage: the car film plays chapter by chapter as you scroll. */
    public function cinematic(): View
    {
        return view('storefront.home', $this->homeData() + ['cinematic' => true]);
    }

    private function homeData(): array
    {
        $stock = Catalog::forSale();

        $featured = Catalog::car('skoda-kodiaq-2026');

        return [
            'featured' => $featured,
            // The hero turntable: one car on stage at a time
            'showroom' => collect(['skoda-kodiaq-2026', 'tesla-model-3-2022', 'volkswagen-golf-2022', 'toyota-rav4-hybrid-2021'])
                ->map(fn ($slug) => Catalog::car($slug))
                ->filter(fn ($car) => $car['cutout'])
                ->values(),
            'makes' => $stock->groupBy('brand')->map->count()->sortKeys(),
            'latest' => $stock->where('slug', '!=', $featured['slug'])->where('status', 'available')->take(6),
            'stats' => [
                ['value' => $stock->count(), 'label' => 'cars on the lot today'],
                ['value' => euro($stock->min('price')), 'label' => 'cheapest car, VAT included'],
                ['value' => $stock->where('is_used', false)->count().' new', 'label' => 'with the full factory warranty'],
                ['value' => $stock->whereIn('engine_type', ['hybrid', 'electric'])->count(), 'label' => 'hybrid and electric cars'],
            ],
            'bodies' => collect(Catalog::BODY)->map(fn ($label, $key) => [
                'key' => $key,
                'label' => $label,
                'count' => $stock->where('car_category', $key)->count(),
                'car' => $stock->where('car_category', $key)->firstWhere('cutout', '!=', null) ?? $stock->firstWhere('car_category', $key),
            ]),
            'brands' => $stock->pluck('brand')->unique()->sort()->values(),
            'stockCount' => $stock->count(),
        ];
    }

    public function inventory(): View
    {
        $stock = Catalog::forSale();

        return view('storefront.inventory', [
            'cars' => $stock,
            'brands' => $stock->pluck('brand')->unique()->sort()->values(),
            'years' => [$stock->min('production_year'), $stock->max('production_year')],
        ]);
    }

    public function show(string $slug): View
    {
        $car = Catalog::car($slug);

        return view('storefront.car', [
            'car' => $car,
            'similar' => Catalog::similar($car),
            'additions' => Catalog::additionsFor($car),
        ]);
    }

    public function compare(): View
    {
        return view('storefront.compare', ['cars' => Catalog::forSale()]);
    }

    public function extras(string $slug): View
    {
        $car = Catalog::car($slug);

        return view('checkout.extras', [
            'car' => $car,
            'additions' => Catalog::additionsFor($car),
        ]);
    }

    public function pay(string $slug): View
    {
        return view('checkout.pay', ['car' => Catalog::car($slug)]);
    }

    public function confirmed(): View
    {
        return view('checkout.confirmed');
    }

    public function sell(): View
    {
        return view('storefront.sell', [
            'brands' => ['Audi', 'BMW', 'Dacia', 'Ford', 'Hyundai', 'Kia', 'Mercedes-Benz', 'Opel', 'Peugeot', 'Renault', 'Škoda', 'Tesla', 'Toyota', 'Volkswagen', 'Other'],
        ]);
    }

    public function login(): View
    {
        return view('auth.login');
    }

    public function scan(): View
    {
        $target = rtrim(config('riva.public_url'), '/').route('login', [], false);

        $svg = (new QRCode(new QROptions([
            'outputBase64' => false,
            'eccLevel' => 'M',
            'addQuietzone' => true,
            'quietzoneSize' => 2,
            'svgUseFillAttributes' => true,
            'moduleValues' => [
                // dark modules use the ink colour, light modules stay transparent
                \chillerlan\QRCode\Data\QRMatrix::M_DATA_DARK => '#16181b',
                \chillerlan\QRCode\Data\QRMatrix::M_FINDER_DARK => '#16181b',
                \chillerlan\QRCode\Data\QRMatrix::M_FINDER_DOT => '#16181b',
                \chillerlan\QRCode\Data\QRMatrix::M_ALIGNMENT_DARK => '#16181b',
                \chillerlan\QRCode\Data\QRMatrix::M_TIMING_DARK => '#16181b',
                \chillerlan\QRCode\Data\QRMatrix::M_FORMAT_DARK => '#16181b',
                \chillerlan\QRCode\Data\QRMatrix::M_VERSION_DARK => '#16181b',
                \chillerlan\QRCode\Data\QRMatrix::M_QUIETZONE_DARK => '#16181b',
            ],
        ])))->render($target);

        return view('storefront.scan', ['qr' => $svg, 'target' => $target]);
    }

    public function account(): View
    {
        return view('account.index');
    }

    public function credits(): View
    {
        $path = resource_path('data/photo-credits.json');

        return view('storefront.credits', [
            'credits' => file_exists($path) ? json_decode(file_get_contents($path), true) : [],
            'cars' => Catalog::cars()->keyBy('slug'),
        ]);
    }

    public function notFound(): View
    {
        return view('errors.404');
    }
}
