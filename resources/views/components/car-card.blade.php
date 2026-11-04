@props(['car', 'eager' => false, 'compact' => false])
<article @class(['car-card', 'car-card--compact' => $compact]) data-car-card
    data-brand="{{ $car['brand'] }}"
    data-body="{{ $car['car_category'] }}"
    data-fuel="{{ $car['engine_type'] }}"
    data-gearbox="{{ $car['transmission'] }}"
    data-condition="{{ $car['is_used'] ? 'used' : 'new' }}"
    data-price="{{ $car['price'] }}"
    data-mileage="{{ $car['mileage'] }}"
    data-year="{{ $car['production_year'] }}"
    data-listed="{{ $car['created_at'] }}"
    data-name="{{ strtolower($car['name'].' '.$car['variant']) }}">
    <div class="car-card__media">
        <x-car-stage :car="$car" :eager="$eager" />
        <div class="car-card__badges">
            @unless ($car['is_used'])
                <span class="badge badge--signal">New</span>
            @endunless
            @if ($car['status'] === 'reserved')
                <x-status-badge status="reserved" />
            @endif
        </div>
    </div>
    <button type="button" class="fav-btn" data-fav="{{ $car['slug'] }}" aria-pressed="false" aria-label="Save {{ $car['title'] }} to favourites">
        <x-icon name="heart" />
    </button>
    <div class="car-card__body">
        <div class="stack" style="--stack: 2px">
            <h3 class="car-card__title"><a href="{{ $car['url'] }}">{{ $car['name'] }}</a></h3>
            <p class="car-card__variant">{{ $car['production_year'] }}, {{ $car['variant'] }}</p>
        </div>
        @unless ($compact)
            <ul class="car-card__specs" aria-label="Key details">
                <li><x-icon name="gauge" :size="16" /> {{ $car['is_used'] ? km($car['mileage']) : 'New' }}</li>
                <li><x-icon :name="$car['engine_type'] === 'electric' ? 'bolt' : 'fuel'" :size="16" /> {{ $car['fuel_label'] }}</li>
                <li><x-icon name="gearbox" :size="16" /> {{ $car['gearbox_label'] }}</li>
                <li><x-icon name="car" :size="16" /> {{ $car['horsepower'] }} hp</li>
            </ul>
        @endunless
        <div class="car-card__foot">
            <span class="stack" style="--stack: 0">
                <span class="price">{{ euro($car['price']) }}</span>
                <span class="caption">or {{ euro($car['monthly']) }} a month</span>
            </span>
            <label class="compare-check">
                <input type="checkbox" data-compare="{{ $car['slug'] }}">
                <span>Compare</span>
            </label>
        </div>
    </div>
</article>
