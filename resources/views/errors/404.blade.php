<x-layouts.app title="Page not found" page="not-found">
    <div class="container section">
        <div class="stack" style="--stack: var(--space-5); max-width: 560px">
            <span class="lane" aria-hidden="true"></span>
            <h1 class="display-l">This road does not go anywhere</h1>
            <p class="body-l muted">The page you asked for is not here. The car may have been sold, or the link may be old.</p>
            <div class="row">
                <a class="btn btn--primary btn--lg" href="{{ page('cars.index') }}">See cars for sale</a>
                <a class="btn btn--secondary btn--lg" href="{{ page('home') }}">Go to the homepage</a>
            </div>
        </div>
    </div>
</x-layouts.app>
