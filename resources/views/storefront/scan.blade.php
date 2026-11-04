<x-layouts.app title="Open on your phone" page="scan">
    <div class="container section">
        <div class="scan">
            <div class="stack" style="--stack: var(--space-5)">
                <span class="lane" aria-hidden="true"></span>
                <h1 class="display-l">Scan to open Riva Motors on your phone</h1>
                <p class="body-l muted prose">Point your phone camera at the code. It opens the login page, so you can sign in and carry on browsing from your pocket.</p>
                <ol class="next-steps">
                    <li><span><strong>Open the camera</strong><br><span class="muted">Most phones read QR codes straight from the camera app.</span></span></li>
                    <li><span><strong>Tap the link that appears</strong><br><span class="muted">It opens the Riva Motors login page.</span></span></li>
                    <li><span><strong>Log in</strong><br><span class="muted">Use your own account or one of the demo accounts.</span></span></li>
                </ol>
                <div class="row">
                    <button type="button" class="btn btn--secondary" data-copy="{{ $target }}"><x-icon name="external" /> Copy link</button>
                    <a class="btn btn--ghost" href="{{ page('login') }}">Go to the login page</a>
                </div>
            </div>
            <figure class="qr-card">
                {!! $qr !!}
                <figcaption class="qr-card__url">{{ $target }}</figcaption>
            </figure>
        </div>
    </div>
</x-layouts.app>
