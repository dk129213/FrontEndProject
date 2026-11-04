<x-layouts.app :title="'Choose extras for the '.$car['name']" nav="buy" page="extras" body-class="has-pay-bar">
    <div class="container">
        <div class="stack" style="--stack: var(--space-5); padding-block: var(--space-6) var(--space-5)">
            <x-stepper current="extras" :car="$car" />
            <div class="stack" style="--stack: var(--space-2)">
                <h1 class="heading">Choose extras</h1>
                <p class="muted">Everything is optional. The total on the right updates as you choose.</p>
            </div>
        </div>

        <div class="checkout">
            <form class="stack" style="--stack: var(--space-5)" data-extras-form data-next="{{ page('checkout.pay', $car['slug']) }}" aria-label="Extras">
                <ul class="extra-list">
                    @foreach ($additions as $addition)
                        <li>
                            <label class="extra">
                                <input type="checkbox" name="extras" value="{{ $addition['id'] }}" data-name="{{ $addition['name'] }}" data-price="{{ $addition['price'] }}">
                                <span class="extra__icon"><x-icon :name="$addition['icon']" :size="22" /></span>
                                <span class="stack" style="--stack: 2px">
                                    <strong>{{ $addition['name'] }}</strong>
                                    <span class="muted">{{ $addition['description'] }}</span>
                                </span>
                                <span class="extra__side">
                                    <span class="title num">{{ euro($addition['price']) }}</span>
                                    <span class="extra__check"><x-icon name="check" :size="16" /></span>
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
                <div class="alert">
                    <x-icon name="info" />
                    <span>Extras are fitted before handover. You can change them with your salesperson until the car is collected.</span>
                </div>
            </form>

            <x-order-summary :car="$car">
                <x-slot:cta>
                    <button type="button" class="btn btn--primary btn--lg btn--block" data-extras-continue>Continue to payment</button>
                </x-slot:cta>
            </x-order-summary>
        </div>
    </div>

    <div class="mobile-pay-bar">
        <span class="stack" style="--stack: 0">
            <span class="caption">Total</span>
            <span class="price" data-summary-total>{{ euro($car['price'], true) }}</span>
        </span>
        <button type="button" class="btn btn--primary btn--lg" data-extras-continue>Continue to payment</button>
    </div>
</x-layouts.app>
