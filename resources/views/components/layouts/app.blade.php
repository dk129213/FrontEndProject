@props([
    'title' => null,
    'description' => 'Inspected used and new cars with prices up front. Browse the stock, choose extras and reserve online.',
    'nav' => null,
    'page' => null,
    'requireRole' => null,
    'bodyClass' => '',
    'footer' => true,
])
@php($dealer = config('riva.dealer'))
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $title ? $title.' | Riva Motors' : 'Riva Motors | Used and new cars' }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="theme-color" content="#0a2a37">
    <link rel="icon" href="{{ img('favicon.svg') }}" type="image/svg+xml">
    <x-theme-script />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body class="{{ $bodyClass }}" data-page="{{ $page }}" data-base="{{ rtrim(config('riva.base_path'), '/') }}" @if ($requireRole) data-require-role="{{ $requireRole }}" @endif>
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="site-header">
        <div class="container site-header__inner">
            <a class="brand" href="{{ page('home') }}" aria-label="Riva Motors home">
                <x-logo :size="36" />
                <span class="brand__name">Riva Motors</span>
            </a>

            <nav class="main-nav" aria-label="Main">
                <a href="{{ page('cars.index') }}" @if ($nav === 'buy') aria-current="page" @endif>Buy a car</a>
                <a href="{{ page('compare') }}" @if ($nav === 'compare') aria-current="page" @endif>Compare</a>
                <a href="{{ page('sell') }}" @if ($nav === 'sell') aria-current="page" @endif>Sell your car</a>
                <a href="{{ page('home') }}#visit">Visit us</a>
            </nav>

            <div class="header-actions">
                <a class="account-link header-cta" href="tel:{{ str_replace(' ', '', $dealer['phone']) }}">
                    <x-icon name="phone" :size="18" />
                    <span class="num">{{ $dealer['phone'] }}</span>
                </a>
                <x-theme-toggle />
                <a class="account-link" href="{{ page('login') }}" data-account-link>
                    <x-icon name="user" />
                    <span data-account-label>Log in</span>
                </a>
                <button type="button" class="icon-btn menu-toggle" aria-expanded="false" aria-controls="mobile-nav" data-menu-toggle>
                    <x-icon name="menu" :size="24" data-icon-open />
                    <x-icon name="close" :size="24" data-icon-close hidden />
                    <span class="sr-only">Menu</span>
                </button>
            </div>
        </div>

        <nav class="mobile-nav" id="mobile-nav" aria-label="Main">
            <a class="mobile-nav__link" href="{{ page('cars.index') }}">Buy a car <x-icon name="chevron-right" /></a>
            <a class="mobile-nav__link" href="{{ page('compare') }}">Compare cars <x-icon name="chevron-right" /></a>
            <a class="mobile-nav__link" href="{{ page('sell') }}">Sell your car <x-icon name="chevron-right" /></a>
            <a class="mobile-nav__link" href="{{ page('home') }}#visit">Visit us <x-icon name="chevron-right" /></a>
            <a class="mobile-nav__link" href="{{ page('login') }}" data-account-link><span data-account-label>Log in</span> <x-icon name="chevron-right" /></a>
            <div class="mobile-nav__footer">
                <a class="btn btn--secondary btn--lg btn--block" href="tel:{{ str_replace(' ', '', $dealer['phone']) }}"><x-icon name="phone" /> Call {{ $dealer['phone'] }}</a>
                <p class="caption">{{ $dealer['hours'][0][0] }} {{ $dealer['hours'][0][1] }}, {{ $dealer['hours'][1][0] }} {{ $dealer['hours'][1][1] }}</p>
            </div>
        </nav>
    </header>

    <main id="main">
        {{ $slot }}
    </main>

    @if ($footer)
        <footer class="site-footer">
            <div class="container">
                <div class="site-footer__top">
                    <p class="site-footer__line">Find it, configure it,<br> drive it home.</p>
                    <a class="btn btn--primary btn--lg" href="{{ page('cars.index') }}">Browse the lot <x-icon name="arrow-right" /></a>
                </div>
                <div class="site-footer__grid">
                    <div class="stack" style="--stack: var(--space-3)">
                        <a class="brand" href="{{ page('home') }}">
                            <x-logo :size="36" />
                            <span class="brand__name">Riva Motors</span>
                        </a>
                        <p class="site-footer__muted" style="max-width: 300px">Hand-picked used and new cars, inspected and priced up front.</p>
                    </div>
                    <div>
                        <h2>Cars</h2>
                        <ul>
                            <li><a href="{{ page('cars.index') }}">All cars for sale</a></li>
                            <li><a href="{{ page('cars.index') }}?condition=new">New cars</a></li>
                            <li><a href="{{ page('cars.index') }}?fuel=electric,hybrid">Hybrid and electric</a></li>
                            <li><a href="{{ page('compare') }}">Compare cars</a></li>
                            <li><a href="{{ page('sell') }}">Sell your car</a></li>
                        </ul>
                    </div>
                    <div>
                        <h2>Your account</h2>
                        <ul>
                            <li><a href="{{ page('login') }}">Log in</a></li>
                            <li><a href="{{ page('account') }}">Favourites and orders</a></li>
                            <li><a href="{{ page('scan') }}">Open on your phone</a></li>
                            <li><a href="{{ page('sales.dashboard') }}">Staff area</a></li>
                        </ul>
                    </div>
                    <div>
                        <h2>Visit the lot</h2>
                        <ul>
                            <li>{{ $dealer['street'] }}, {{ $dealer['postcode'] }} {{ $dealer['city'] }}</li>
                            <li><a href="tel:{{ str_replace(' ', '', $dealer['phone']) }}" class="num">{{ $dealer['phone'] }}</a></li>
                            <li><a href="mailto:{{ $dealer['email'] }}">{{ $dealer['email'] }}</a></li>
                            <li class="num">{{ $dealer['hours'][0][0] }}, {{ $dealer['hours'][0][1] }}</li>
                        </ul>
                    </div>
                </div>
                <div class="site-footer__base">
                    <span>Prices include {{ config('riva.vat_rate') }}% VAT. Student project: payments are simulated and no real cars are sold.</span>
                    <span><a href="{{ page('credits') }}">Photo credits</a></span>
                </div>
            </div>
            <div class="site-footer__wordmark" aria-hidden="true">Riva Motors</div>
        </footer>
    @endif

    <aside class="compare-tray" data-compare-tray hidden aria-label="Cars to compare">
        <div class="compare-tray__cars" data-compare-list></div>
        <span class="compare-tray__hint" data-compare-hint></span>
        <button type="button" class="btn btn--ghost btn--sm compare-tray__clear" data-compare-clear>Clear</button>
        <a class="btn btn--primary" href="{{ page('compare') }}" data-compare-go>Compare</a>
    </aside>

    <div class="toast-region" aria-live="polite" data-toasts></div>

    <script type="application/json" id="riva-data">{!! \App\Support\Catalog::pageJson() !!}</script>
    {{ $scripts ?? '' }}
</body>
</html>
