{{--
    Cinematic hero: a 12-second car film (public/video). It stops at five
    chapters (data-at, 0 to 1 through the film): cabin, out through the
    window, front, side, final three-quarter view. Scrolling down plays the
    real video to the next chapter; scrolling up plays a reversed copy.
--}}
<section class="cine" aria-labelledby="cine-title" data-cine
    data-desktop="{{ img('video/film-1600.mp4') }}"
    data-desktop-reverse="{{ img('video/film-1600-reverse.mp4') }}"
    data-mobile="{{ img('video/film-960.mp4') }}"
    data-mobile-reverse="{{ img('video/film-960-reverse.mp4') }}">
    <div class="cine__sticky">
        <img class="cine__poster" src="{{ img('video/poster.webp') }}" alt="" fetchpriority="high" width="1600" height="902">
        <video class="cine__video" data-cine-forward muted playsinline preload="auto" aria-hidden="true" tabindex="-1"></video>
        <video class="cine__video" data-cine-reverse muted playsinline preload="none" aria-hidden="true" tabindex="-1"></video>
        <div class="cine__shade" aria-hidden="true"></div>

        <div class="container cine__captions">
            <div class="cine__caption cine__caption--intro" data-at="0">
                <span class="lane" aria-hidden="true"></span>
                <h1 class="display-xl" id="cine-title">Find your next car.<br> Drive it home.</h1>
                <p class="body-l">Hand-picked used and new cars, inspected and priced up front.</p>
            </div>
            <div class="cine__caption" data-at="0.35">
                <h2 class="heading">Inspected before it is listed</h2>
                <p>A full check and a service before any car goes on sale.</p>
            </div>
            <div class="cine__caption cine__caption--right" data-at="0.6">
                <h2 class="heading">The price you see is the price you pay</h2>
                <p>VAT included, no haggling, and a 12-month warranty on every used car.</p>
            </div>
            <div class="cine__caption" data-at="0.79">
                <h2 class="heading">Reserve online in minutes</h2>
                <p>Choose extras, see the monthly cost and pay by card. We call you within a day.</p>
            </div>
            <div class="cine__caption cine__caption--final" data-at="1">
                <h2 class="display-l">{{ $stockCount }} cars are ready for you</h2>
                <x-hero-search :makes="$makes" :bodies="$bodies" :count="$stockCount" class="cine__search" />
            </div>
        </div>

        <button type="button" class="cine__skip" data-cine-skip>Skip intro <x-icon name="arrow-down" :size="18" /></button>

        <nav class="cine__dots" aria-label="Film chapters">
            @foreach (['Find your next car', 'Inspected', 'Honest prices', 'Reserve online', 'Search the stock'] as $label)
                <button type="button" data-cine-dot aria-label="{{ $label }}" @if ($loop->first) aria-current="step" @endif></button>
            @endforeach
        </nav>

        <div class="cine__progress" aria-hidden="true"><span data-cine-bar></span></div>
        <p class="cine__note">Film for illustration. Browse the real stock below.</p>
    </div>
</section>
