<x-layouts.app title="Sell your car" nav="sell" page="sell">
    <div class="container">
        <div class="checkout" style="padding-block: var(--space-6) 0">
            <div class="stack" style="--stack: var(--space-5)">
                <div class="stack" style="--stack: var(--space-2)">
                    <h1 class="heading">Sell your car to us</h1>
                    <p class="body-l muted prose">Tell us about your car and we will call you back with an offer, usually the same day. You can also trade it in against one from our lot.</p>
                </div>

                <form class="card-form" novalidate data-validate data-reset-on-success data-success="Thanks. We have your details and will call you with an offer today.">
                    <h2 class="title">Your car</h2>
                    <div class="form-grid">
                        <div class="field">
                            <label for="s-brand">Make</label>
                            <select class="select" id="s-brand" name="brand" required data-error="Choose the make of your car.">
                                <option value="">Choose a make</option>
                                @foreach ($brands as $brand)
                                    <option>{{ $brand }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="s-model">Model</label>
                            <input class="input" id="s-model" name="model" placeholder="For example Octavia" required data-error="Enter the model, for example Golf or Octavia.">
                        </div>
                        <div class="field">
                            <label for="s-year">First registered</label>
                            <input class="input" id="s-year" name="year" inputmode="numeric" maxlength="4" placeholder="2019" required data-min="1990" data-max="2026" data-error="Enter a year between 1990 and 2026.">
                        </div>
                        <div class="field">
                            <label for="s-km">Mileage in km</label>
                            <input class="input" id="s-km" name="mileage" inputmode="numeric" placeholder="85000" required data-min="0" data-max="900000" data-error="Enter the mileage in kilometres, numbers only.">
                        </div>
                        <fieldset class="span-2">
                            <legend>Fuel</legend>
                            <div class="segmented">
                                @foreach (\App\Support\Catalog::FUEL as $key => $label)
                                    <label><input type="radio" name="fuel" value="{{ $key }}" @checked($loop->first)><span>{{ $label }}</span></label>
                                @endforeach
                            </div>
                        </fieldset>
                        <fieldset class="span-2">
                            <legend>Gearbox</legend>
                            <div class="segmented">
                                <label><input type="radio" name="gearbox" value="manual" checked><span>Manual</span></label>
                                <label><input type="radio" name="gearbox" value="automatic"><span>Automatic</span></label>
                            </div>
                        </fieldset>
                        <div class="field span-2">
                            <label for="s-notes">Anything we should know</label>
                            <textarea class="textarea" id="s-notes" name="notes" placeholder="Service history, damage, new tyres..."></textarea>
                        </div>
                    </div>

                    <h2 class="title" style="margin-top: var(--space-3)">How we reach you</h2>
                    <div class="form-grid">
                        <div class="field">
                            <label for="s-name">Your name</label>
                            <input class="input" id="s-name" name="name" autocomplete="name" required data-error="Enter your name.">
                        </div>
                        <div class="field">
                            <label for="s-phone">Phone</label>
                            <input class="input" id="s-phone" name="phone" type="tel" autocomplete="tel" required data-error="Enter a phone number so we can call with the offer.">
                        </div>
                    </div>
                    <button type="submit" class="btn btn--primary btn--lg">Get an offer</button>
                </form>
            </div>

            <aside class="stack" style="--stack: var(--space-5)">
                <div class="reasons__photo" style="aspect-ratio: 4 / 3">
                    <img src="{{ img('images/cars/skoda-octavia-combi-2021/2.jpg') }}" alt="Rear of a white Škoda Octavia estate" loading="lazy" width="1600" height="1000">
                </div>
                <div class="panel stack">
                    <h2 class="title">How it works</h2>
                    <ol class="next-steps">
                        <li><span><strong>Send the details</strong><br><span class="muted">Two minutes, no photos needed yet.</span></span></li>
                        <li><span><strong>Get a call with an offer</strong><br><span class="muted">Based on recent sales of the same model.</span></span></li>
                        <li><span><strong>Bring it in</strong><br><span class="muted">We check it, confirm the price and pay by bank transfer the same day.</span></span></li>
                    </ol>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>
