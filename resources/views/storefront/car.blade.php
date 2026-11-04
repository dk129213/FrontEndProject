@php($forSale = $car['status'] === 'available')
<x-layouts.app :title="$car['title']" nav="buy" page="car" :body-class="$forSale ? 'has-buy-bar' : ''"
    :description="$car['title'].' '.$car['variant'].', '.$car['specs_line'].'. '.euro($car['price']).' including VAT.'">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb" style="padding-block: var(--space-5) var(--space-4)">
            <a href="{{ page('cars.index') }}">Cars for sale</a>
            <span aria-hidden="true">/</span>
            <a href="{{ page('cars.index') }}?body={{ $car['car_category'] }}">{{ $car['body_label'] }}</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ $car['name'] }}</span>
        </nav>

        <div class="detail">
            <div class="stack" style="--stack: var(--space-6)">
                <div class="gallery" data-gallery>
                    <div class="gallery__stage">
                        <img src="{{ $car['gallery'][0]['src'] }}" alt="{{ $car['gallery'][0]['alt'] }}" data-gallery-main @class(['is-studio' => $car['gallery'][0]['studio']]) fetchpriority="high" width="1600" height="1000">
                        @if (count($car['gallery']) > 1)
                            <button type="button" class="gallery__nav gallery__nav--prev" data-gallery-prev aria-label="Previous photo"><x-icon name="chevron-left" /></button>
                            <button type="button" class="gallery__nav gallery__nav--next" data-gallery-next aria-label="Next photo"><x-icon name="chevron-right" /></button>
                            <button type="button" class="gallery__expand" data-lightbox-open><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg> View full screen</button>
                            <span class="gallery__count" aria-live="polite"><span data-gallery-index>1</span> of {{ count($car['gallery']) }}</span>
                        @endif
                    </div>
                    @if (count($car['gallery']) > 1)
                        <div class="gallery__thumbs">
                            @foreach ($car['gallery'] as $photo)
                                <button type="button" data-gallery-thumb="{{ $loop->index }}" data-studio="{{ $photo['studio'] ? '1' : '0' }}" data-src="{{ $photo['src'] }}" data-alt="{{ $photo['alt'] }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}" aria-label="Show photo {{ $loop->iteration }}">
                                    <img src="{{ $photo['src'] }}" alt="" loading="lazy" width="320" height="200">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="key-specs">
                    <div class="key-spec"><x-icon name="gauge" /><span class="caption">Mileage</span><strong>{{ $car['is_used'] ? km($car['mileage']) : 'New' }}</strong></div>
                    <div class="key-spec"><x-icon name="{{ $car['engine_type'] === 'electric' ? 'bolt' : 'fuel' }}" /><span class="caption">Fuel</span><strong>{{ $car['fuel_label'] }}</strong></div>
                    <div class="key-spec"><x-icon name="gearbox" /><span class="caption">Gearbox</span><strong>{{ $car['gearbox_label'] }}</strong></div>
                    <div class="key-spec"><x-icon name="calendar" /><span class="caption">First registered</span><strong>{{ $car['production_year'] }}</strong></div>
                </div>
            </div>

            <aside class="buy-box" aria-label="Price and next step">
                <div class="row" style="--row: var(--space-2)">
                    <x-status-badge :status="$car['status']" />
                    <span class="badge">{{ $car['condition_label'] }}</span>
                </div>
                <div class="stack" style="--stack: var(--space-1)">
                    <h1 class="display-l">{{ $car['title'] }}</h1>
                    <p class="muted">{{ $car['variant'] }}, {{ $car['horsepower'] }} hp</p>
                </div>
                <div class="buy-box__price">
                    <span class="stat">{{ euro($car['price']) }}</span>
                    <span class="caption">Price includes {{ config('riva.vat_rate') }}% VAT</span>
                    <a class="text-link" href="#finance" style="margin-top: var(--space-2)">or {{ euro($car['monthly']) }} a month with finance</a>
                </div>

                @if ($forSale)
                    <a class="btn btn--primary btn--lg btn--block" href="{{ page('checkout.extras', $car['slug']) }}">Reserve this car</a>
                    <a class="btn btn--secondary btn--lg btn--block" href="#ask">Ask about this car</a>
                @elseif ($car['status'] === 'reserved')
                    <div class="alert alert--warning" role="status">
                        <x-icon name="clock" />
                        <span>Another customer has reserved this car. If their purchase falls through, it goes back on sale. Ask us to keep you posted.</span>
                    </div>
                    <a class="btn btn--secondary btn--lg btn--block" href="#ask">Ask about this car</a>
                @else
                    <div class="alert" role="status">
                        <x-icon name="info" />
                        <span>This car was just sold. The cars below are similar and still available.</span>
                    </div>
                    <a class="btn btn--primary btn--lg btn--block" href="#similar">See similar cars</a>
                @endif

                <div class="row" style="--row: var(--space-2); justify-content: space-between">
                    <button type="button" class="btn btn--ghost" data-fav="{{ $car['slug'] }}" data-fav-text aria-pressed="false">
                        <x-icon name="heart" /> <span data-fav-label>Save to favourites</span>
                    </button>
                    <label class="compare-check"><input type="checkbox" data-compare="{{ $car['slug'] }}"> <span>Compare</span></label>
                </div>

                <ul class="ticks">
                    <li><x-icon name="check" /> {{ $car['is_used'] ? 'Inspected and serviced' : 'Full factory warranty' }}</li>
                    <li><x-icon name="check" /> 12-month Riva warranty included</li>
                    <li><x-icon name="check" /> Trade-in accepted</li>
                </ul>

                @if ($car['salesman'])
                    <div class="salesperson">
                        <span class="avatar" aria-hidden="true">{{ $car['salesman']['initials'] }}</span>
                        <span class="stack" style="--stack: 0">
                            <strong>{{ $car['salesman']['name'] }}</strong>
                            <span class="caption">Sales, looks after this car</span>
                        </span>
                    </div>
                @endif
            </aside>
        </div>

        <div class="detail-lower" style="margin-top: var(--space-7)">
            <div class="stack" style="--stack: var(--space-7)">
                <section class="stack" aria-labelledby="about-title">
                    <h2 class="heading" id="about-title">About this car</h2>
                    <p class="body-l prose">{{ $car['description'] }}</p>
                </section>

                <section class="stack" style="--stack: var(--space-5)" id="finance" aria-labelledby="finance-title">
                    <div class="stack" style="--stack: var(--space-2)">
                        <h2 class="heading" id="finance-title">Spread the cost</h2>
                        <p class="muted">Move the deposit and the term to see what this car costs each month.</p>
                    </div>
                    <div class="calc" data-calc data-price="{{ $car['price'] }}" data-apr="{{ \App\Support\Catalog::FINANCE['apr'] }}">
                        <div class="calc__controls">
                            <div class="range-field">
                                <div class="range-field__head">
                                    <label class="label" for="calc-deposit">Deposit</label>
                                    <output for="calc-deposit" data-calc-deposit-out></output>
                                </div>
                                <input class="range" id="calc-deposit" type="range" min="0" max="50" step="5" value="{{ \App\Support\Catalog::FINANCE['deposit'] }}" data-calc-deposit>
                                <span class="hint">0 to 50% of the price</span>
                            </div>
                            <fieldset>
                                <legend>Term</legend>
                                <div class="term-options" role="radiogroup">
                                    @foreach ([24, 36, 48, 60, 72] as $months)
                                        <button type="button" class="chip" data-calc-term="{{ $months }}" aria-pressed="{{ $months === \App\Support\Catalog::FINANCE['months'] ? 'true' : 'false' }}">{{ $months }} months</button>
                                    @endforeach
                                </div>
                            </fieldset>
                            <p class="caption">Representative example at {{ \App\Support\Catalog::FINANCE['apr'] }}% APR fixed. For illustration only, not a finance offer.</p>
                        </div>
                        <div class="calc__result" aria-live="polite">
                            <span class="label" style="color: var(--on-sea-band-muted)">Monthly payment</span>
                            <span class="calc__monthly" data-calc-monthly>{{ euro($car['monthly']) }}</span>
                            <dl>
                                <dt>Deposit</dt><dd data-calc-deposit-eur></dd>
                                <dt>Amount borrowed</dt><dd data-calc-loan></dd>
                                <dt>Total of payments</dt><dd data-calc-total></dd>
                            </dl>
                        </div>
                    </div>
                </section>

                <section class="stack" style="--stack: var(--space-5)" aria-labelledby="specs-title">
                    <h2 class="heading" id="specs-title">Specifications</h2>
                    <div class="spec-groups">
                        <div class="spec-group">
                            <h3>Overview</h3>
                            <dl>
                                <dt>Condition</dt><dd>{{ $car['condition_label'] }}</dd>
                                <dt>First registered</dt><dd>{{ $car['production_year'] }}</dd>
                                <dt>Mileage</dt><dd class="num">{{ km($car['mileage']) }}</dd>
                                <dt>Body</dt><dd>{{ $car['body_label'] }}</dd>
                                <dt>Colour</dt><dd>{{ $car['colour'] }}</dd>
                                <dt>Previous owners</dt><dd>{{ $car['owners'] ?: 'None' }}</dd>
                            </dl>
                        </div>
                        <div class="spec-group">
                            <h3>Engine and drive</h3>
                            <dl>
                                <dt>Engine</dt><dd>{{ $car['engine'] }}</dd>
                                <dt>Fuel</dt><dd>{{ $car['fuel_label'] }}</dd>
                                <dt>Power</dt><dd class="num">{{ $car['horsepower'] }} hp</dd>
                                <dt>Gearbox</dt><dd>{{ $car['gearbox_label'] }}{{ $car['number_of_gears'] > 1 ? ', '.$car['number_of_gears'].' gears' : ', single speed' }}</dd>
                                <dt>Drive</dt><dd>{{ $car['drivetrain'] }}</dd>
                                <dt>{{ $car['engine_type'] === 'electric' ? 'Range' : 'Consumption' }}</dt><dd>{{ $car['consumption'] }}</dd>
                            </dl>
                        </div>
                        <div class="spec-group">
                            <h3>Identification</h3>
                            <dl>
                                <dt>VIN</dt><dd class="mono">{{ $car['vin'] }}</dd>
                                <dt>Stock number</dt><dd class="mono">RM-CAR-{{ str_pad($car['id'], 4, '0', STR_PAD_LEFT) }}</dd>
                                <dt>Listed</dt><dd>{{ $car['listed_label'] }}</dd>
                            </dl>
                        </div>
                        @if ($additions->isNotEmpty() && $forSale)
                            <div class="spec-group">
                                <h3>Extras you can add</h3>
                                <dl>
                                    @foreach ($additions->take(4) as $addition)
                                        <dt>{{ $addition['name'] }}</dt><dd class="num">{{ euro($addition['price']) }}</dd>
                                    @endforeach
                                </dl>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="stack" aria-labelledby="equip-title">
                    <h2 class="heading" id="equip-title">Equipment</h2>
                    <ul class="equipment">
                        @foreach ($car['equipment'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </section>
            </div>

            <section class="panel stack" id="ask" aria-labelledby="ask-title">
                <h2 class="title" id="ask-title">Ask about this car</h2>
                <p class="muted">{{ $car['salesman']['first_name'] ?? 'Our team' }} usually replies within two working hours.</p>
                <form class="stack" novalidate data-validate data-success="Thanks. {{ $car['salesman']['first_name'] ?? 'We' }} will call you back about the {{ $car['name'] }}.">
                    <div class="field">
                        <label for="q-name">Your name</label>
                        <input class="input" id="q-name" name="name" autocomplete="name" required data-error="Enter your name so we know who to ask for.">
                    </div>
                    <div class="field">
                        <label for="q-phone">Phone</label>
                        <input class="input" id="q-phone" name="phone" type="tel" autocomplete="tel" required data-error="Enter a phone number, for example +385 91 234 5678.">
                    </div>
                    <div class="field">
                        <label for="q-msg">Message</label>
                        <textarea class="textarea" id="q-msg" name="message" rows="4">Is the {{ $car['name'] }} still available for a test drive this week?</textarea>
                    </div>
                    <button type="submit" class="btn btn--secondary">Send question</button>
                </form>
            </section>
        </div>

        @if ($similar->isNotEmpty())
            <section class="section" id="similar" aria-labelledby="similar-title">
                <div class="section-head">
                    <h2 class="heading" id="similar-title">Similar cars</h2>
                    <a class="text-link" href="{{ page('cars.index') }}?body={{ $car['car_category'] }}">See all {{ ['suv' => 'SUVs', 'estate' => 'estates', 'hatchback' => 'hatchbacks', 'saloon' => 'saloons'][$car['car_category']] }}</a>
                </div>
                <div class="car-grid">
                    @foreach ($similar as $other)
                        <x-car-card :car="$other" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    @if (count($car['gallery']) > 1)
        <dialog class="lightbox" data-lightbox aria-label="Photos of the {{ $car['name'] }}">
            <div class="lightbox__bar">
                <strong>{{ $car['title'] }} <span class="muted" style="color: #a9bec6; font-weight: 500"><span data-lightbox-index>1</span> of {{ count($car['gallery']) }}</span></strong>
                <button type="button" class="icon-btn" data-lightbox-close aria-label="Close photos"><x-icon name="close" :size="24" /></button>
            </div>
            <div class="lightbox__stage">
                <img src="{{ $car['gallery'][0]['src'] }}" alt="{{ $car['gallery'][0]['alt'] }}" data-lightbox-img @class(['is-studio' => $car['gallery'][0]['studio']])>
                @if (count($car['gallery']) > 1)
                    <button type="button" class="gallery__nav gallery__nav--prev" data-lightbox-prev aria-label="Previous photo"><x-icon name="chevron-left" /></button>
                    <button type="button" class="gallery__nav gallery__nav--next" data-lightbox-next aria-label="Next photo"><x-icon name="chevron-right" /></button>
                @endif
            </div>
            <div class="lightbox__thumbs">
                @foreach ($car['gallery'] as $photo)
                    <button type="button" data-lightbox-thumb="{{ $loop->index }}" data-studio="{{ $photo['studio'] ? '1' : '0' }}" data-src="{{ $photo['src'] }}" data-alt="{{ $photo['alt'] }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}" aria-label="Show photo {{ $loop->iteration }}"><img src="{{ $photo['src'] }}" alt="" loading="lazy"></button>
                @endforeach
            </div>
        </dialog>
    @endif

    @if ($forSale)
        <div class="mobile-buy-bar">
            <span class="stack" style="--stack: 0">
                <span class="price">{{ euro($car['price']) }}</span>
                <span class="caption">Incl. {{ config('riva.vat_rate') }}% VAT</span>
            </span>
            <a class="btn btn--primary btn--lg" href="{{ page('checkout.extras', $car['slug']) }}">Reserve this car</a>
        </div>
    @endif
</x-layouts.app>
