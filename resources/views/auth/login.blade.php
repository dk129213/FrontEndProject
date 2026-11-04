<x-layouts.app title="Log in" page="login" :footer="false">
    <div class="auth">
        <div class="auth__media">
            @php($loginCar = \App\Support\Catalog::car('tesla-model-3-2022'))
            <img class="auth__car" src="{{ $loginCar['cutout'] ?? $loginCar['image'] }}" alt="" width="1400" height="800">
            <div class="stack" style="--stack: var(--space-3)">
                <span class="lane" aria-hidden="true"></span>
                <h2 class="display-l" style="color: #f2f0ec">Find it, configure it, drive it home.</h2>
                <p>Save favourites, follow your orders and pick up where you left off on any device.</p>
            </div>
        </div>

        <div class="auth__panel">
            <div class="auth__box" data-auth>
                <div class="tabs" role="tablist" aria-label="Account">
                    <button type="button" role="tab" id="tab-login" aria-controls="panel-login" aria-selected="true" data-tab="login">Log in</button>
                    <button type="button" role="tab" id="tab-register" aria-controls="panel-register" aria-selected="false" data-tab="register" tabindex="-1">Create account</button>
                </div>

                <section id="panel-login" role="tabpanel" aria-labelledby="tab-login" class="stack" style="--stack: var(--space-5)">
                    <div class="stack" style="--stack: var(--space-1)">
                        <h1 class="heading">Welcome back</h1>
                        <p class="muted">Customers, sales team and admins all log in here.</p>
                    </div>
                    <form class="stack" novalidate data-login-form>
                        <div class="field">
                            <label for="login-email">Email</label>
                            <input class="input input--lg" id="login-email" name="email" type="email" autocomplete="email" required>
                        </div>
                        <div class="field">
                            <label for="login-password">Password</label>
                            <input class="input input--lg" id="login-password" name="password" type="password" autocomplete="current-password" required>
                        </div>
                        <p class="field-error" data-login-error hidden role="alert"></p>
                        <button type="submit" class="btn btn--primary btn--lg btn--block">Log in</button>
                    </form>

                    <div class="divider">Demo accounts</div>
                    <div class="demo-accounts">
                        <button type="button" class="demo-account" data-demo-login="customer">
                            <span class="avatar" aria-hidden="true">AH</span>
                            <span class="stack" style="--stack: 0"><strong>Customer</strong><span class="caption">Ana Horvat. Favourites, orders, checkout.</span></span>
                            <x-icon name="arrow-right" />
                        </button>
                        <button type="button" class="demo-account" data-demo-login="salesman">
                            <span class="avatar" aria-hidden="true">MB</span>
                            <span class="stack" style="--stack: 0"><strong>Sales team</strong><span class="caption">Marko Babić. His cars and his sales.</span></span>
                            <x-icon name="arrow-right" />
                        </button>
                        <button type="button" class="demo-account" data-demo-login="admin">
                            <span class="avatar" aria-hidden="true">PK</span>
                            <span class="stack" style="--stack: 0"><strong>Admin</strong><span class="caption">Petra Kovač. All stock, sales and users.</span></span>
                            <x-icon name="arrow-right" />
                        </button>
                    </div>
                    <a class="row text-link" style="--row: var(--space-2)" href="{{ page('scan') }}"><x-icon name="qr" /> Open this page on your phone</a>
                </section>

                <section id="panel-register" role="tabpanel" aria-labelledby="tab-register" class="stack" style="--stack: var(--space-5)" hidden>
                    <div class="stack" style="--stack: var(--space-1)">
                        <h1 class="heading">Create an account</h1>
                        <p class="muted">Save favourites and reserve cars online.</p>
                    </div>
                    <form class="stack" novalidate data-register-form>
                        <div class="form-grid">
                            <div class="field">
                                <label for="r-first">First name</label>
                                <input class="input" id="r-first" name="first_name" autocomplete="given-name" required data-error="Enter your first name.">
                            </div>
                            <div class="field">
                                <label for="r-last">Last name</label>
                                <input class="input" id="r-last" name="last_name" autocomplete="family-name" required data-error="Enter your last name.">
                            </div>
                        </div>
                        <div class="field">
                            <label for="r-email">Email</label>
                            <input class="input" id="r-email" name="email" type="email" autocomplete="email" required data-error="Enter an email like name@example.com.">
                        </div>
                        <div class="field">
                            <label for="r-phone">Phone</label>
                            <input class="input" id="r-phone" name="phone" type="tel" autocomplete="tel" required data-error="Enter a phone number.">
                        </div>
                        <div class="field">
                            <label for="r-password">Password</label>
                            <input class="input" id="r-password" name="password" type="password" autocomplete="new-password" required minlength="8" data-error="Use at least 8 characters." aria-describedby="r-password-hint">
                            <span class="hint" id="r-password-hint">At least 8 characters.</span>
                        </div>
                        <button type="submit" class="btn btn--primary btn--lg btn--block">Create account</button>
                    </form>
                    <p class="caption">This demo keeps your account on this device only.</p>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
