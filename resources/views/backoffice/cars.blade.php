<x-layouts.backoffice :area="$area" nav="cars" :title="$area === 'admin' ? 'Cars' : 'My cars'">
    <div class="bo-head">
        <div class="stack" style="--stack: var(--space-1)">
            <h1 class="heading">{{ $area === 'admin' ? 'Cars' : 'My cars' }}</h1>
            <p class="muted">{{ $area === 'admin' ? 'Everything on the lot, in one list.' : 'Cars you look after. Change a status when a customer reserves or buys.' }}</p>
        </div>
        <a class="btn btn--primary" href="{{ page($area.'.cars.create') }}"><x-icon name="plus" /> Add a car</a>
    </div>

    <div class="bo-filters" data-table-filters>
        <div class="search-input">
            <x-icon name="search" :size="18" />
            <label class="sr-only" for="car-search">Search cars</label>
            <input class="input" id="car-search" type="search" placeholder="Search make, model or VIN" data-table-search>
        </div>
        <div class="chip-row" role="group" aria-label="Status">
            <button type="button" class="chip" data-status-chip="" aria-pressed="true">All <span class="count">{{ $cars->count() }}</span></button>
            @foreach (\App\Support\Catalog::CAR_STATUS as $key => $label)
                <button type="button" class="chip" data-status-chip="{{ $key }}" aria-pressed="false">{{ $label }} <span class="count">{{ $cars->where('status', $key)->count() }}</span></button>
            @endforeach
        </div>
    </div>

    <div class="table-wrap">
        <table class="table" data-cars-table>
            <thead>
                <tr>
                    <th scope="col">Car</th>
                    <th scope="col">Year</th>
                    <th scope="col">Fuel</th>
                    <th scope="col">Gearbox</th>
                    <th scope="col" class="right">Mileage</th>
                    <th scope="col" class="right">Price</th>
                    @if ($area === 'admin')<th scope="col">Salesperson</th>@endif
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody data-table-body>
                @foreach ($cars as $car)
                    <tr data-row data-status="{{ $car['status'] }}" data-search="{{ strtolower($car['name'].' '.$car['variant'].' '.$car['vin']) }}">
                        <td>
                            <div class="cell-car">
                                <img src="{{ $car['image'] }}" alt="" loading="lazy" width="144" height="90">
                                <span>
                                    <a href="{{ $car['url'] }}" style="color: var(--ink); text-decoration: none"><strong>{{ $car['name'] }}</strong></a>
                                    <span class="mono caption">{{ $car['vin'] }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="num">{{ $car['production_year'] }}</td>
                        <td>{{ $car['fuel_label'] }}</td>
                        <td>{{ $car['gearbox_label'] }}</td>
                        <td class="right num nowrap">{{ $car['is_used'] ? km($car['mileage']) : 'New' }}</td>
                        <td class="right num nowrap" style="font-weight: 700">{{ euro($car['price']) }}</td>
                        @if ($area === 'admin')<td class="nowrap">{{ $car['salesman']['name'] ?? 'Unassigned' }}</td>@endif
                        <td>
                            <label class="sr-only" for="status-{{ $car['id'] }}">Status of {{ $car['name'] }}</label>
                            <select class="select" id="status-{{ $car['id'] }}" style="height: var(--control-sm); width: 140px; font-size: 13px" data-status-select="{{ $car['slug'] }}">
                                @foreach (\App\Support\Catalog::CAR_STATUS as $key => $label)
                                    <option value="{{ $key }}" @selected($car['status'] === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="caption" data-table-count></p>
    <div class="empty" data-table-empty hidden>
        <h2 class="title">No cars match</h2>
        <p class="muted">Try another search or status.</p>
    </div>
</x-layouts.backoffice>
