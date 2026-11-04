<x-layouts.app title="Cars for sale" nav="buy" page="inventory">
    <div class="container">
        <div class="page-head">
            <h1 class="heading">Cars for sale</h1>
            <p class="muted"><span data-result-count>{{ $cars->count() }}</span> <span data-result-noun>cars</span> on the lot. Prices include {{ config('riva.vat_rate') }}% VAT.</p>
        </div>

        <div class="chip-row" role="group" aria-label="Body type" style="margin-bottom: var(--space-5)">
            <button type="button" class="chip" data-body-chip="" aria-pressed="true">All types</button>
            @foreach (\App\Support\Catalog::BODY as $key => $label)
                <button type="button" class="chip" data-body-chip="{{ $key }}" aria-pressed="false">
                    {{ $label }} <span class="count">{{ $cars->where('car_category', $key)->count() }}</span>
                </button>
            @endforeach
        </div>

        <div class="inventory">
            <form class="filters" id="filters" aria-label="Filters" data-filters>
                <div class="filters__head">
                    <h2 class="title">Filters</h2>
                    <div class="row" style="--row: var(--space-1)">
                        <button type="reset" class="btn btn--ghost btn--sm">Clear all</button>
                        <button type="button" class="icon-btn filters__close" data-filters-close aria-label="Close filters"><x-icon name="close" /></button>
                    </div>
                </div>

                <input type="hidden" name="body" value="">

                <div class="field">
                    <label for="f-brand">Make</label>
                    <select class="select" id="f-brand" name="brand">
                        <option value="">Any make</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand }}">{{ $brand }}</option>
                        @endforeach
                    </select>
                </div>

                <fieldset>
                    <legend>Condition</legend>
                    <div class="segmented">
                        <label><input type="radio" name="condition" value="" checked><span>All</span></label>
                        <label><input type="radio" name="condition" value="used"><span>Used</span></label>
                        <label><input type="radio" name="condition" value="new"><span>New</span></label>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Price</legend>
                    <div class="range-pair">
                        <label class="sr-only" for="f-min">Minimum price in euro</label>
                        <input class="input" id="f-min" name="min" inputmode="numeric" placeholder="Min €">
                        <label class="sr-only" for="f-max">Maximum price in euro</label>
                        <input class="input" id="f-max" name="max" inputmode="numeric" placeholder="Max €">
                    </div>
                </fieldset>

                <div class="field">
                    <label for="f-km">Mileage</label>
                    <select class="select" id="f-km" name="km">
                        <option value="">Any mileage</option>
                        <option value="25000">Up to 25,000 km</option>
                        <option value="50000">Up to 50,000 km</option>
                        <option value="100000">Up to 100,000 km</option>
                    </select>
                </div>

                <div class="field">
                    <label for="f-year">First registered</label>
                    <select class="select" id="f-year" name="year">
                        <option value="">Any year</option>
                        @for ($y = $years[1]; $y >= $years[0]; $y--)
                            <option value="{{ $y }}">{{ $y }} or newer</option>
                        @endfor
                    </select>
                </div>

                <fieldset>
                    <legend>Fuel</legend>
                    @foreach (\App\Support\Catalog::FUEL as $key => $label)
                        <label class="check"><input type="checkbox" name="fuel" value="{{ $key }}"> {{ $label }}</label>
                    @endforeach
                </fieldset>

                <fieldset>
                    <legend>Gearbox</legend>
                    <div class="segmented">
                        <label><input type="radio" name="gearbox" value="" checked><span>Any</span></label>
                        <label><input type="radio" name="gearbox" value="automatic"><span>Automatic</span></label>
                        <label><input type="radio" name="gearbox" value="manual"><span>Manual</span></label>
                    </div>
                </fieldset>

                <div class="filters__apply">
                    <button type="button" class="btn btn--primary btn--lg btn--block" data-filters-close><span>Show <span data-result-count>{{ $cars->count() }}</span> cars</span></button>
                </div>
            </form>

            <div>
                <div class="toolbar">
                    <button type="button" class="btn btn--secondary filter-open" data-filters-open aria-controls="filters" aria-expanded="false">
                        <x-icon name="filter" /> Filters
                    </button>
                    <div class="row" style="--row: var(--space-2); margin-left: auto">
                        <label for="sort" class="label">Sort by</label>
                        <select class="select" id="sort" data-sort>
                            <option value="newest">Newest arrivals</option>
                            <option value="price-asc">Price, low to high</option>
                            <option value="price-desc">Price, high to low</option>
                            <option value="mileage-asc">Mileage, lowest first</option>
                            <option value="year-desc">Year, newest first</option>
                        </select>
                    </div>
                </div>

                <div class="car-grid" data-car-grid>
                    @foreach ($cars as $car)
                        <x-car-card :car="$car" :eager="$loop->index < 3" />
                    @endforeach
                </div>

                <div class="empty" data-empty hidden>
                    <h2 class="title">No cars match these filters</h2>
                    <p class="muted">Try a higher price, a different fuel or any gearbox. New cars arrive every week.</p>
                    <button type="button" class="btn btn--secondary" data-filters-reset>Clear all filters</button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
