<x-layouts.backoffice :area="$area" nav="dashboard" title="Dashboard">
    <div class="bo-head">
        <div class="stack" style="--stack: var(--space-1)">
            <h1 class="heading">Good to see you, <span data-user-first></span></h1>
            <p class="muted">{{ $area === 'admin' ? 'Everything on the lot and every sale, at a glance.' : 'Your cars and your customers, at a glance.' }}</p>
        </div>
        <a class="btn btn--primary" href="{{ page($area.'.cars.create') }}"><x-icon name="plus" /> Add a car</a>
    </div>

    <div class="stats">
        @foreach ($stats as $stat)
            <div class="stat-tile">
                <span class="label">{{ $stat['label'] }}</span>
                <span class="stat">{{ $stat['value'] }}</span>
            </div>
        @endforeach
    </div>

    <div class="bo-grid">
        <section class="stack" aria-labelledby="recent-sales">
            <div class="section-head" style="margin: 0">
                <h2 class="title" id="recent-sales">Latest sales</h2>
                <a class="text-link" href="{{ page($area === 'admin' ? 'admin.sales' : 'sales.orders') }}">All sales</a>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th scope="col">Order</th><th scope="col">Car</th><th scope="col" class="right">Total</th><th scope="col">Payment</th></tr></thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr>
                                <td class="nowrap"><span class="mono">{{ $sale['id'] }}</span><br><span class="caption">{{ $sale['buyer']['name'] }}</span></td>
                                <td>{{ $sale['car']['name'] }}</td>
                                <td class="right num nowrap" style="font-weight: 700">{{ euro($sale['total_price']) }}</td>
                                <td><x-status-badge :status="$sale['payment_status']" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="muted">No sales yet. They appear here as soon as a customer pays.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="stack" aria-labelledby="recent-cars">
            <div class="section-head" style="margin: 0">
                <h2 class="title" id="recent-cars">{{ $area === 'admin' ? 'Recently listed' : 'My latest cars' }}</h2>
                <a class="text-link" href="{{ page($area.'.cars') }}">All cars</a>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <tbody>
                        @foreach ($cars as $car)
                            <tr>
                                <td>
                                    <div class="cell-car" style="min-width: 0">
                                        <img src="{{ $car['image'] }}" alt="" width="144" height="90">
                                        <span><strong>{{ $car['name'] }}</strong><span class="caption">{{ euro($car['price']) }}</span></span>
                                    </div>
                                </td>
                                <td class="right"><x-status-badge :status="$car['status']" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.backoffice>
