<x-layouts.app :title="'Pay for the '.$car['name']" nav="buy" page="pay">
    <div class="container">
        <div class="stack" style="--stack: var(--space-5); padding-block: var(--space-6) var(--space-5)">
            <x-stepper current="pay" :car="$car" />
            <div class="stack" style="--stack: var(--space-2)">
                <h1 class="heading">Sign in and pay</h1>
                <p class="muted">Paying reserves the car in your name. We call you within one working day to arrange collection.</p>
            </div>
        </div>

        <div class="checkout">
            <div class="stack" style="--stack: var(--space-5)">

                {{-- Shown when nobody is logged in --}}
                <section class="card-form" data-pay-signin hidden aria-labelledby="signin-title">
                    <div class="stack" style="--stack: var(--space-1)">
                        <h2 class="title" id="signin-title">Log in to continue</h2>
                        <p class="muted">The car is reserved under your account, so you can follow the order afterwards.</p>
                    </div>
                    <form class="stack" novalidate data-login-form data-stay>
                        <div class="field">
                            <label for="pay-email">Email</label>
                            <input class="input" id="pay-email" name="email" type="email" autocomplete="email" required>
                        </div>
                        <div class="field">
                            <label for="pay-password">Password</label>
                            <input class="input" id="pay-password" name="password" type="password" autocomplete="current-password" required>
                        </div>
                        <p class="field-error" data-login-error hidden></p>
                        <button type="submit" class="btn btn--dark btn--lg">Log in</button>
                    </form>
                    <div class="divider">or</div>
                    <button type="button" class="demo-account" data-demo-login="customer">
                        <span class="avatar" aria-hidden="true">AH</span>
                        <span class="stack" style="--stack: 0"><strong>Use the demo customer</strong><span class="caption">Ana Horvat, customer@rivamotors.example</span></span>
                        <x-icon name="arrow-right" />
                    </button>
                    <p class="caption">No account yet? <a href="{{ page('login') }}?tab=register">Create one</a>, it takes a minute.</p>
                </section>

                {{-- Shown when a customer is logged in --}}
                <form class="card-form" novalidate data-pay-form data-pay-car="{{ $car['slug'] }}" hidden aria-labelledby="pay-title">
                    <div class="row" style="justify-content: space-between">
                        <h2 class="title" id="pay-title">Your details</h2>
                        <span class="caption" data-pay-user></span>
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="first">First name</label>
                            <input class="input" id="first" name="first_name" autocomplete="given-name" required data-error="Enter your first name.">
                        </div>
                        <div class="field">
                            <label for="last">Last name</label>
                            <input class="input" id="last" name="last_name" autocomplete="family-name" required data-error="Enter your last name.">
                        </div>
                        <div class="field">
                            <label for="email">Email</label>
                            <input class="input" id="email" name="email" type="email" autocomplete="email" required data-error="Enter an email like name@example.com.">
                        </div>
                        <div class="field">
                            <label for="phone">Phone</label>
                            <input class="input" id="phone" name="phone" type="tel" autocomplete="tel" required data-error="Enter a phone number so we can arrange collection.">
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px solid var(--line); margin: var(--space-2) 0">

                    <div class="row" style="justify-content: space-between">
                        <h2 class="title">Card payment</h2>
                        <span class="secure-note"><x-icon name="lock" :size="16" /> Test mode, no money moves</span>
                    </div>

                    <div class="field">
                        <label for="card-name">Name on card</label>
                        <input class="input" id="card-name" name="card_name" autocomplete="cc-name" required data-error="Enter the name printed on the card.">
                    </div>
                    <div class="field">
                        <label for="card-number">Card number</label>
                        <div class="card-input">
                            <input class="input input--lg" id="card-number" name="card_number" inputmode="numeric" autocomplete="cc-number" placeholder="1234 1234 1234 1234" maxlength="19" required aria-describedby="card-number-error">
                            <span class="card-input__brand" data-card-brand></span>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="card-exp">Expiry date</label>
                            <input class="input" id="card-exp" name="card_exp" inputmode="numeric" autocomplete="cc-exp" placeholder="MM / YY" maxlength="7" required>
                        </div>
                        <div class="field">
                            <label for="card-cvc">Security code</label>
                            <input class="input" id="card-cvc" name="card_cvc" inputmode="numeric" autocomplete="cc-csc" placeholder="3 digits on the back" maxlength="4" required>
                        </div>
                    </div>

                    <div class="alert alert--danger" data-pay-error hidden role="alert">
                        <x-icon name="alert" />
                        <span data-pay-error-text></span>
                    </div>

                    <details class="test-cards">
                        <summary class="label" style="cursor: pointer">Test cards for this demo</summary>
                        <div class="test-cards" style="margin-top: var(--space-2)">
                            <button type="button" data-test-card="4242 4242 4242 4242"><span class="mono">4242 4242 4242 4242</span><span>Payment succeeds</span></button>
                            <button type="button" data-test-card="4000 0000 0000 0002"><span class="mono">4000 0000 0000 0002</span><span>Card is declined</span></button>
                        </div>
                    </details>

                    <button type="submit" class="btn btn--primary btn--lg btn--block" data-pay-button><span>Pay <span data-summary-total>{{ euro($car['price'], true) }}</span></span></button>
                    <p class="caption">By paying you agree to reserve this car. You can cancel free of charge until it is collected.</p>
                </form>
            </div>

            <x-order-summary :car="$car" />
        </div>
    </div>
</x-layouts.app>
