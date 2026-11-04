<x-layouts.app title="My account" page="account" require-role="customer,salesman,admin">
    <div class="container">
        <div class="page-head" style="padding-block: var(--space-6)">
            <div class="account-head">
                <span class="avatar" aria-hidden="true" data-user-initials></span>
                <div class="stack" style="--stack: 2px">
                    <h1 class="heading">Hello, <span data-user-first></span></h1>
                    <p class="muted" data-user-email></p>
                </div>
                <div class="row" style="margin-left: auto">
                    <a class="btn btn--secondary" href="{{ page('sales.dashboard') }}" data-staff-link hidden><x-icon name="dashboard" /> Staff area</a>
                    <button type="button" class="btn btn--ghost" data-logout><x-icon name="logout" /> Log out</button>
                </div>
            </div>
        </div>

        <section class="stack" style="--stack: var(--space-4)" aria-labelledby="orders-title">
            <h2 class="title" id="orders-title">My orders</h2>
            <div class="stack" style="--stack: var(--space-3)" data-orders></div>
            <div class="empty" data-orders-empty hidden>
                <h3 class="title">No orders yet</h3>
                <p class="muted">When you reserve a car it shows up here with its payment status.</p>
                <a class="btn btn--secondary" href="{{ page('cars.index') }}">Browse cars</a>
            </div>
        </section>

        <section class="section" aria-labelledby="fav-title">
            <div class="section-head">
                <h2 class="title" id="fav-title">Favourites</h2>
            </div>
            <div class="car-grid" data-favourites>
                @foreach (\App\Support\Catalog::cars() as $car)
                    <div data-fav-card="{{ $car['slug'] }}" hidden style="display: contents">
                        <x-car-card :car="$car" />
                    </div>
                @endforeach
            </div>
            <div class="empty" data-fav-empty hidden>
                <h3 class="title">No favourites saved</h3>
                <p class="muted">Tap the heart on any car to keep it here and compare later.</p>
                <a class="btn btn--secondary" href="{{ page('cars.index') }}">Browse cars</a>
            </div>
        </section>
    </div>
</x-layouts.app>
