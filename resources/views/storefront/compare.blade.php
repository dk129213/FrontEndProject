<x-layouts.app title="Compare cars" nav="compare" page="compare">
    <div class="container">
        <div class="page-head">
            <h1 class="heading">Compare cars</h1>
            <p class="muted">Line up to three cars side by side. The best value in each row is marked.</p>
        </div>

        <section class="stack" style="--stack: var(--space-3); margin-bottom: var(--space-6)" aria-labelledby="pick-title">
            <h2 class="label" id="pick-title">Choose up to three</h2>
            <div class="compare-picker">
                @foreach ($cars as $car)
                    <button type="button" class="chip" data-compare-pick="{{ $car['slug'] }}" aria-pressed="false">{{ $car['name'] }}</button>
                @endforeach
            </div>
        </section>

        {{-- Filled in by resources/js/compare.js --}}
        <div class="compare-table-wrap" data-compare-table hidden></div>

        <div class="empty" data-compare-empty>
            <h2 class="title">Pick two or three cars to compare</h2>
            <p class="muted">Use the buttons above, or tick Compare on any car in the list.</p>
            <a class="btn btn--secondary" href="{{ page('cars.index') }}">Browse cars</a>
        </div>
    </div>
</x-layouts.app>
