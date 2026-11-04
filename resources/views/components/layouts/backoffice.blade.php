@props(['area', 'nav', 'title'])
@php
    $links = $area === 'admin'
        ? [
            'dashboard' => ['Dashboard', page('admin.dashboard'), 'dashboard'],
            'cars' => ['Cars', page('admin.cars'), 'car'],
            'add' => ['Add a car', page('admin.cars.create'), 'plus'],
            'sales' => ['Sales', page('admin.sales'), 'receipt'],
            'users' => ['Users', page('admin.users'), 'users'],
            'extras' => ['Extras', page('admin.extras'), 'tag'],
        ]
        : [
            'dashboard' => ['Dashboard', page('sales.dashboard'), 'dashboard'],
            'cars' => ['My cars', page('sales.cars'), 'car'],
            'add' => ['Add a car', page('sales.cars.create'), 'plus'],
            'sales' => ['My sales', page('sales.orders'), 'receipt'],
        ];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex">
    <title>{{ $title }} | Riva Motors {{ $area === 'admin' ? 'Admin' : 'Sales' }}</title>
    <link rel="icon" href="{{ img('favicon.svg') }}" type="image/svg+xml">
    <x-theme-script />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-page="backoffice" data-area="{{ $area }}" data-base="{{ rtrim(config('riva.base_path'), '/') }}" data-require-role="{{ $area === 'admin' ? 'admin' : 'salesman,admin' }}">
    <a class="skip-link" href="#main">Skip to content</a>
    <div class="bo">
        <nav class="side-nav" id="side-nav" aria-label="Backoffice" data-side-nav>
            <a class="brand" href="{{ page('home') }}" style="padding: 0 var(--space-2) var(--space-4)">
                <x-logo :size="34" />
                <span class="stack" style="--stack: 0">
                    <span class="brand__name" style="font-size: 16px">Riva Motors</span>
                    <span class="caption">{{ $area === 'admin' ? 'Admin' : 'Sales team' }}</span>
                </span>
            </a>
            @foreach ($links as $key => [$label, $href, $icon])
                <a class="side-nav__link" href="{{ $href }}" @if ($key === $nav) aria-current="page" @endif><x-icon :name="$icon" /> {{ $label }}</a>
            @endforeach
            <span class="side-nav__group">Switch</span>
            <a class="side-nav__link" href="{{ page('home') }}"><x-icon name="store" /> View the shop</a>
            <a class="side-nav__link" href="{{ page('admin.dashboard') }}" data-admin-only hidden><x-icon name="shield" /> Admin area</a>
            <a class="side-nav__link" href="{{ page('sales.dashboard') }}" data-sales-switch hidden><x-icon name="users" /> Sales area</a>
            <div class="side-nav__user">
                <span class="avatar" aria-hidden="true" data-user-initials></span>
                <span class="stack" style="--stack: 0; flex: 1; min-width: 0">
                    <span class="label" data-user-name></span>
                    <span class="caption" data-user-role></span>
                </span>
                <x-theme-toggle />
                <button type="button" class="icon-btn" data-logout aria-label="Log out"><x-icon name="logout" /></button>
            </div>
        </nav>

        <div>
            <div class="bo-topbar">
                <button type="button" class="icon-btn" data-side-toggle aria-controls="side-nav" aria-expanded="false" aria-label="Open navigation"><x-icon name="menu" :size="24" /></button>
                <x-logo :size="30" />
                <strong>{{ $title }}</strong>
            </div>
            <main class="bo-main" id="main">
                {{ $slot }}
            </main>
        </div>
    </div>

    <div class="toast-region" aria-live="polite" data-toasts></div>
    <script type="application/json" id="riva-data">{!! \App\Support\Catalog::pageJson(['carsList' => $area === 'admin' ? page('admin.cars') : page('sales.cars')]) !!}</script>
</body>
</html>
