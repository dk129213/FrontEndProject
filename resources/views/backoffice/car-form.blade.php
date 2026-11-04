<x-layouts.backoffice :area="$area" nav="add" title="Add a car">
    <div class="bo-head">
        <div class="stack" style="--stack: var(--space-1)">
            <h1 class="heading">Add a car</h1>
            <p class="muted">Fill in what is on the registration papers. The preview shows how the car will look in the shop.</p>
        </div>
    </div>

    <form class="bo-grid" novalidate data-car-form data-area="{{ $area }}" aria-label="New car">
        <div class="stack" style="--stack: var(--space-5)">
            <section class="form-section" aria-labelledby="sec-car">
                <h2 class="title" id="sec-car">The car</h2>
                <div class="form-grid">
                    <div class="field">
                        <label for="c-brand">Make</label>
                        <input class="input" id="c-brand" name="brand" required placeholder="Škoda" data-preview="brand" data-error="Enter the make.">
                    </div>
                    <div class="field">
                        <label for="c-model">Model</label>
                        <input class="input" id="c-model" name="model" required placeholder="Octavia Combi" data-preview="model" data-error="Enter the model.">
                    </div>
                    <div class="field span-2">
                        <label for="c-variant">Trim and engine</label>
                        <input class="input" id="c-variant" name="variant" placeholder="Style 2.0 TDI" data-preview="variant">
                    </div>
                    <fieldset class="span-2">
                        <legend>Condition</legend>
                        <div class="segmented">
                            <label><input type="radio" name="is_used" value="1" checked data-preview="is_used"><span>Used</span></label>
                            <label><input type="radio" name="is_used" value="0" data-preview="is_used"><span>New</span></label>
                        </div>
                    </fieldset>
                    <div class="field">
                        <label for="c-category">Body</label>
                        <select class="select" id="c-category" name="car_category" required data-preview="car_category" data-error="Choose a body type.">
                            <option value="">Choose</option>
                            @foreach (\App\Support\Catalog::BODY as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="c-colour">Colour</label>
                        <input class="input" id="c-colour" name="colour" placeholder="Moon White">
                    </div>
                    <div class="field">
                        <label for="c-year">First registered</label>
                        <input class="input" id="c-year" name="production_year" inputmode="numeric" maxlength="4" required placeholder="2021" data-min="1990" data-max="2027" data-preview="production_year" data-error="Enter a year between 1990 and 2027.">
                    </div>
                    <div class="field">
                        <label for="c-km">Mileage in km</label>
                        <input class="input" id="c-km" name="mileage" inputmode="numeric" required placeholder="48000" data-min="0" data-max="900000" data-preview="mileage" data-error="Enter the mileage, numbers only.">
                    </div>
                    <div class="field span-2">
                        <label for="c-vin">VIN</label>
                        <input class="input mono" id="c-vin" name="vin" required maxlength="17" placeholder="17 characters" style="text-transform: uppercase" data-pattern="^[A-HJ-NPR-Za-hj-npr-z0-9]{17}$" data-error="A VIN has 17 letters and numbers, without I, O or Q." aria-describedby="c-vin-hint">
                        <span class="hint" id="c-vin-hint">Printed on the registration papers and at the bottom of the windscreen.</span>
                    </div>
                </div>
            </section>

            <section class="form-section" aria-labelledby="sec-engine">
                <h2 class="title" id="sec-engine">Engine and gearbox</h2>
                <div class="form-grid">
                    <fieldset class="span-2">
                        <legend>Fuel</legend>
                        <div class="segmented">
                            @foreach (\App\Support\Catalog::FUEL as $key => $label)
                                <label><input type="radio" name="engine_type" value="{{ $key }}" @checked($loop->first) data-preview="engine_type"><span>{{ $label }}</span></label>
                            @endforeach
                        </div>
                    </fieldset>
                    <fieldset class="span-2">
                        <legend>Gearbox</legend>
                        <div class="segmented">
                            <label><input type="radio" name="transmission" value="manual" checked data-preview="transmission"><span>Manual</span></label>
                            <label><input type="radio" name="transmission" value="automatic" data-preview="transmission"><span>Automatic</span></label>
                        </div>
                    </fieldset>
                    <div class="field">
                        <label for="c-gears">Number of gears</label>
                        <input class="input" id="c-gears" name="number_of_gears" inputmode="numeric" placeholder="6" data-min="1" data-max="10" data-error="Enter 1 to 10 gears.">
                    </div>
                    <div class="field">
                        <label for="c-hp">Power in hp</label>
                        <input class="input" id="c-hp" name="horsepower" inputmode="numeric" required placeholder="150" data-min="40" data-max="1200" data-preview="horsepower" data-error="Enter the power in horsepower.">
                    </div>
                    <div class="field span-2">
                        <label for="c-engine">Engine</label>
                        <input class="input" id="c-engine" name="engine" placeholder="2.0 TDI">
                    </div>
                </div>
            </section>

            <section class="form-section" aria-labelledby="sec-sale">
                <h2 class="title" id="sec-sale">Price and listing</h2>
                <div class="form-grid">
                    <div class="field">
                        <label for="c-price">Price in euro, with VAT</label>
                        <input class="input" id="c-price" name="price" inputmode="numeric" required placeholder="24900" data-min="500" data-max="500000" data-preview="price" data-error="Enter a price between €500 and €500,000.">
                    </div>
                    <div class="field">
                        <label for="c-status">Status</label>
                        <select class="select" id="c-status" name="status">
                            <option value="available">Available</option>
                            <option value="reserved">Reserved</option>
                        </select>
                    </div>
                    @if ($area === 'admin')
                        <div class="field span-2">
                            <label for="c-salesman">Salesperson</label>
                            <select class="select" id="c-salesman" name="salesman_id">
                                @foreach ($team as $person)
                                    <option value="{{ $person['id'] }}">{{ $person['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="field span-2">
                        <label for="c-desc">Description</label>
                        <textarea class="textarea" id="c-desc" name="description" placeholder="History, condition and who it suits, in two or three sentences."></textarea>
                    </div>
                    <fieldset class="span-2">
                        <legend>Extras customers can add</legend>
                        @foreach ($additions as $addition)
                            <label class="check"><input type="checkbox" name="additions" value="{{ $addition['id'] }}" checked> {{ $addition['name'] }} <span class="caption">{{ euro($addition['price']) }}</span></label>
                        @endforeach
                    </fieldset>
                </div>
            </section>

            <section class="form-section" aria-labelledby="sec-photos">
                <h2 class="title" id="sec-photos">Photos</h2>
                <label class="dropzone" for="c-photos">
                    <x-icon name="upload" :size="28" />
                    <strong>Choose photos</strong>
                    <span class="caption">16:10, side or three-quarter view, plain background. JPG or PNG.</span>
                </label>
                <input class="sr-only" id="c-photos" name="photos" type="file" accept="image/*" multiple data-photo-input>
                <div class="photo-previews" data-photo-previews></div>
            </section>

            <div class="form-actions">
                <a class="btn btn--secondary btn--lg" href="{{ page($area.'.cars') }}">Cancel</a>
                <button type="submit" class="btn btn--primary btn--lg">Publish car</button>
            </div>
        </div>

        <aside class="stack" style="position: sticky; top: var(--space-5)" aria-label="Shop preview">
            <h2 class="label muted">Preview in the shop</h2>
            <article class="car-card" data-car-preview>
                <div class="car-card__media">
                    <img src="{{ img('images/placeholder-car.svg') }}" alt="" data-preview-image width="800" height="500">
                    <div class="car-card__badges"><span class="badge badge--signal" data-preview-new hidden>New</span></div>
                </div>
                <div class="car-card__body">
                    <h3 class="car-card__title" data-preview-name>Make and model</h3>
                    <p class="car-card__variant" data-preview-variant>Year and trim</p>
                    <ul class="car-card__specs">
                        <li data-preview-km>Mileage</li>
                        <li data-preview-fuel>Petrol</li>
                        <li data-preview-gearbox>Manual</li>
                        <li data-preview-hp>Power</li>
                    </ul>
                    <div class="car-card__foot">
                        <span class="price" data-preview-price>€0</span>
                        <span class="caption" data-preview-body>Body</span>
                    </div>
                </div>
            </article>
        </aside>
    </form>
</x-layouts.backoffice>
