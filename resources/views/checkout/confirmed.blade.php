<x-layouts.app title="Order confirmed" page="confirmed">
    <div class="container section">
        {{-- Filled in by resources/js/checkout.js from the order just placed --}}
        <div class="confirm-hero" data-confirmation hidden>
            <div class="stack" style="--stack: var(--space-5)">
                <svg class="done-check" width="56" height="56" viewBox="0 0 56 56" aria-hidden="true"><circle cx="28" cy="28" r="28" fill="var(--success-soft)"/><path d="M18 29l7 7 13-15" fill="none" stroke="var(--success)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div class="stack" style="--stack: var(--space-2)">
                    <h1 class="display-l">Your car is reserved</h1>
                    <p class="body-l muted">Thanks, <span data-c-name></span>. We have sent a receipt to <strong data-c-email></strong>.</p>
                </div>
                <dl class="hours">
                    <dt>Order number</dt><dd class="mono" data-c-order></dd>
                    <dt>Car</dt><dd data-c-car></dd>
                    <dt>Extras</dt><dd data-c-extras></dd>
                    <dt>Paid</dt><dd class="num" style="font-weight: 700" data-c-total></dd>
                    <dt>Payment</dt><dd><span class="badge badge--success">Paid</span> <span class="mono caption" data-c-ref></span></dd>
                </dl>
                <div class="row">
                    <a class="btn btn--primary btn--lg" href="{{ page('account') }}">View my orders</a>
                    <a class="btn btn--secondary btn--lg" href="{{ page('cars.index') }}">Keep browsing</a>
                </div>
            </div>
            <div class="stack" style="--stack: var(--space-5)">
                <img src="" alt="" data-c-image width="1600" height="1000">
                <div class="panel stack">
                    <h2 class="title">What happens next</h2>
                    <ol class="next-steps">
                        <li><span><strong>We call you</strong><br><span class="muted">Your salesperson rings within one working day to agree a collection time.</span></span></li>
                        <li><span><strong>We prepare the car</strong><br><span class="muted">Extras are fitted and the car is cleaned and registered in your name.</span></span></li>
                        <li><span><strong>You drive it home</strong><br><span class="muted">Bring your ID and driving licence to the showroom.</span></span></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="empty" data-confirmation-empty hidden>
            <h1 class="heading">No recent order on this device</h1>
            <p class="muted">Orders you place appear here straight after payment. Your past orders are in your account.</p>
            <div class="row">
                <a class="btn btn--primary" href="{{ page('account') }}">Go to my account</a>
                <a class="btn btn--secondary" href="{{ page('cars.index') }}">Browse cars</a>
            </div>
        </div>
    </div>
</x-layouts.app>
