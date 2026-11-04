@php($dealer = config('riva.dealer'))
<x-layouts.app page="home" :title="$cinematic ? 'Cinematic' : null">

    <x-version-switch :cinematic="$cinematic" />

    @if ($cinematic)
        @include('storefront.partials.cinematic')
    @else
    {{-- Hero: the showroom turntable, one car on stage at a time --}}
    <section class="showroom" aria-labelledby="hero-title">
        <div class="container">
            <div class="showroom__stage" data-showroom>
                <div class="showroom__copy">
                    <span class="lane" aria-hidden="true"></span>
                    <h1 class="display-xl" id="hero-title">Find your next car.<br> Drive it home.</h1>
                    <p class="body-l muted">Hand-picked used and new cars, inspected and priced up front. Reserve online in minutes.</p>
                </div>

                <div class="showroom__cars" aria-live="polite">
                    @foreach ($showroom as $car)
                        <figure class="showroom__slide" data-slide @if (! $loop->first) hidden @endif>
                            <img src="{{ $car['cutout'] }}" alt="{{ $car['title'] }} in {{ $car['colour'] }}" width="1400" height="800" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                        </figure>
                    @endforeach
                </div>

                <div class="showroom__bar">
                    @foreach ($showroom as $car)
                        <a class="showroom__plate" href="{{ $car['url'] }}" data-plate @if (! $loop->first) hidden @endif>
                            <span class="stack" style="--stack: 2px">
                                <span class="caption">{{ $car['is_used'] ? 'Used, '.km($car['mileage']) : 'New' }}, {{ $car['fuel_label'] }}, {{ $car['gearbox_label'] }}</span>
                                <strong class="title">{{ $car['title'] }}</strong>
                            </span>
                            <span class="stack" style="--stack: 0; text-align: right">
                                <span class="price">{{ euro($car['price']) }}</span>
                                <span class="caption">or {{ euro($car['monthly']) }} a month</span>
                            </span>
                        </a>
                    @endforeach
                    @if ($showroom->count() > 1)
                        <div class="showroom__nav">
                            <span class="showroom__count num" aria-hidden="true"><span data-showroom-index>1</span> / {{ $showroom->count() }}</span>
                            <button type="button" class="round-btn" data-showroom-prev aria-label="Previous car"><x-icon name="arrow-left" /></button>
                            <button type="button" class="round-btn round-btn--dark" data-showroom-next aria-label="Next car"><x-icon name="arrow-right" /></button>
                        </div>
                    @endif
                </div>
            </div>

            <x-hero-search :makes="$makes" :bodies="$bodies" :count="$stockCount" />
        </div>
    </section>
    @endif

    {{-- Shop by make --}}
    <section class="section--tight" aria-labelledby="makes-title">
        <div class="container">
            <div class="section-head">
                <h2 class="heading" id="makes-title" tabindex="-1">Shop by make</h2>
            </div>
            <ul class="makes">
                @foreach ($makes as $brand => $count)
                    <li>
                        <a class="make" href="{{ page('cars.index') }}?brand={{ urlencode($brand) }}">
                            <x-brand-logo :brand="$brand" :size="34" />
                            <span class="make__name">{{ $brand }}</span>
                            <span class="caption">{{ $count }} {{ Str::plural('car', $count) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section--tight" aria-labelledby="latest-title">
        <div class="container">
            <div class="section-head">
                <h2 class="heading" id="latest-title">Just arrived</h2>
                <a class="text-link arrow-link" href="{{ page('cars.index') }}">See all {{ $stockCount }} cars <x-icon name="arrow-right" :size="18" /></a>
            </div>
            <div class="car-grid">
                @foreach ($latest as $car)
                    <x-car-card :car="$car" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- How buying works: a real sequence, so it is numbered --}}
    <section class="section" aria-labelledby="how-title">
        <div class="container">
            <div class="how">
                <div class="stack" style="--stack: var(--space-3)">
                    <h2 class="heading" id="how-title">Buying a car takes three steps</h2>
                    <p class="muted">No haggling and no surprises at the end. The price you see includes VAT.</p>
                </div>
                <ol class="how__steps">
                    <li data-reveal>
                        <span class="how__num">1</span>
                        <h3 class="title">Choose a car</h3>
                        <p class="muted">Filter by fuel, gearbox, mileage and price. Compare up to three side by side.</p>
                    </li>
                    <li data-reveal>
                        <span class="how__num">2</span>
                        <h3 class="title">Add extras</h3>
                        <p class="muted">Winter tyres, a longer warranty or a tow bar. The total updates as you go.</p>
                    </li>
                    <li data-reveal>
                        <span class="how__num">3</span>
                        <h3 class="title">Pay and collect</h3>
                        <p class="muted">Reserve with a card online. We call you within a day to agree a handover time.</p>
                    </li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section--tight" aria-labelledby="types-title">
        <div class="container">
            <div class="section-head">
                <h2 class="heading" id="types-title">Browse by type</h2>
            </div>
            <div class="type-rail">
                @foreach ($bodies as $body)
                    <a class="type-tile" href="{{ page('cars.index') }}?body={{ $body['key'] }}">
                        @if ($body['car'])
                            <x-car-stage :car="$body['car']" size="tile" />
                        @endif
                        <span class="type-tile__label">
                            <strong>{{ $body['label'] }}</strong>
                            <span>{{ $body['count'] }} {{ Str::plural('car', $body['count']) }} <x-icon name="arrow-right" :size="16" /></span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Riva --}}
    <section class="section" aria-labelledby="why-title">
        <div class="container why">
            <div class="why__intro" data-reveal>
                <h2 class="heading" id="why-title">Every car is one we would sell to our own family</h2>
                <x-car-stage :car="\App\Support\Catalog::car('toyota-rav4-hybrid-2021')" size="wide" />
            </div>
            <ul class="why__list">
                <li data-reveal>
                    <span class="icon-tile"><x-icon name="shield" :size="24" /></span>
                    <h3 class="title">Inspected before it is listed</h3>
                    <p class="muted">A full check and a service before a car goes on sale. You see the history before you visit.</p>
                </li>
                <li data-reveal>
                    <span class="icon-tile"><x-icon name="check" :size="24" /></span>
                    <h3 class="title">12-month warranty</h3>
                    <p class="muted">Every used car comes with a year of cover. New cars keep the full factory warranty.</p>
                </li>
                <li data-reveal>
                    <span class="icon-tile"><x-icon name="swap" :size="24" /></span>
                    <h3 class="title">Trade in your old car</h3>
                    <p class="muted">We value it on the spot and take it straight off the price of your next one.</p>
                </li>
                <li data-reveal>
                    <span class="icon-tile"><x-icon name="card" :size="24" /></span>
                    <h3 class="title">Pay how you like</h3>
                    <p class="muted">Card, bank transfer or monthly payments. Every car page shows the monthly cost.</p>
                </li>
            </ul>
        </div>
    </section>

    {{-- Two tools, side by side --}}
    <section aria-label="Tools">
        <div class="container tools">
            <a class="tool tool--sea" href="{{ page('compare') }}" data-reveal>
                <span class="tool__icon"><x-icon name="dashboard" :size="26" /></span>
                <span class="stack" style="--stack: var(--space-2)">
                    <strong class="title">Compare up to three cars</strong>
                    <span>Tick Compare on any car to line up price, power, fuel and running costs side by side.</span>
                </span>
                <x-icon name="arrow-right" :size="22" />
            </a>
            <a class="tool tool--signal" href="{{ $featured['url'] }}#finance" data-reveal>
                <span class="tool__icon"><x-icon name="calendar" :size="26" /></span>
                <span class="stack" style="--stack: var(--space-2)">
                    <strong class="title">See the monthly cost</strong>
                    <span>Every car page has a payment calculator. Move the deposit and the term and watch the price change.</span>
                </span>
                <x-icon name="arrow-right" :size="22" />
            </a>
        </div>
    </section>

    <section class="section" aria-labelledby="sell-title">
        <div class="container">
            <div class="sell-band" data-reveal>
                <div class="stack" style="--stack: var(--space-4)">
                    <h2 class="heading" id="sell-title">Selling your car?</h2>
                    <p class="body-l">Tell us what you drive and we will call you back with a fair offer, usually the same day. No obligation.</p>
                    <div><a class="btn btn--primary btn--lg" href="{{ page('sell') }}">Get an offer</a></div>
                </div>
                @php($sellCar = \App\Support\Catalog::car('volkswagen-golf-2022'))
                @if ($sellCar['cutout'])
                    <img class="sell-band__car" src="{{ $sellCar['cutout'] }}" alt="" loading="lazy" width="1400" height="800">
                @endif
            </div>
        </div>
    </section>

    <section class="section--tight" id="visit" aria-labelledby="visit-title">
        <div class="container visit">
            <div class="stack" style="--stack: var(--space-5)">
                <h2 class="heading" id="visit-title">Visit the showroom</h2>
                <p class="muted">Come for a test drive or just a look around. No appointment needed, free parking for visitors.</p>
                <dl class="hours">
                    <dt>Address</dt>
                    <dd>{{ $dealer['street'] }}, {{ $dealer['postcode'] }} {{ $dealer['city'] }}</dd>
                    @foreach ($dealer['hours'] as [$days, $time])
                        <dt>{{ $days }}</dt>
                        <dd class="num">{{ $time }}</dd>
                    @endforeach
                    <dt>Phone</dt>
                    <dd><a href="tel:{{ str_replace(' ', '', $dealer['phone']) }}" class="num">{{ $dealer['phone'] }}</a></dd>
                </dl>
                <div>
                    <a class="btn btn--secondary" href="https://www.openstreetmap.org/?mlat=42.6614&amp;mlon=18.0832#map=15/42.6614/18.0832" target="_blank" rel="noopener">
                        <x-icon name="pin" /> Get directions
                    </a>
                </div>
            </div>
            <div class="map">
                <div class="map__fallback" aria-hidden="true">
                    <x-icon name="pin" :size="32" />
                    <strong style="color: var(--ink)">{{ $dealer['street'] }}, {{ $dealer['city'] }}</strong>
                </div>
                <iframe title="Map showing the showroom" loading="lazy" src="https://www.openstreetmap.org/export/embed.html?bbox=18.0682%2C42.6534%2C18.0982%2C42.6694&amp;layer=mapnik&amp;marker=42.6614%2C18.0832"></iframe>
            </div>
        </div>
    </section>

</x-layouts.app>
