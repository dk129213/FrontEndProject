@props(['car'])
<aside class="summary" aria-labelledby="summary-title" data-summary data-car="{{ $car['slug'] }}" data-base-price="{{ $car['price'] }}">
    <div class="summary__car">
        <x-car-stage :car="$car" class="summary__stage" />
        <div class="stack" style="--stack: 2px">
            <h2 class="title" id="summary-title">{{ $car['title'] }}</h2>
            <span class="caption">{{ $car['variant'] }}, {{ $car['specs_line'] }}</span>
        </div>
    </div>
    <div class="summary__lines">
        <div class="summary__line"><span>Car price</span><span>{{ euro($car['price'], true) }}</span></div>
        <div class="stack" style="--stack: var(--space-2)" data-summary-extras></div>
        <div class="summary__line caption"><span>Includes {{ config('riva.vat_rate') }}% VAT</span><span data-summary-vat></span></div>
    </div>
    <div class="summary__total">
        <span class="title">Total</span>
        <span class="price" data-summary-total>{{ euro($car['price'], true) }}</span>
    </div>
    @isset($cta)
        <div class="summary__cta">{{ $cta }}</div>
    @endisset
</aside>
