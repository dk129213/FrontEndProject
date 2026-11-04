// Choose extras, sign in and pay (simulated, Stripe-style test cards),
// then show the confirmation. Nothing leaves the browser.

import { currentUser } from './auth';
import { validate, showError } from './forms';
import { $, $$, carBySlug, data, escapeHtml, euro, store } from './lib';

const DECLINED = '4000000000000002';

function vatOf(total) {
    const rate = data().vat / 100;
    return total - total / (1 + rate);
}

function renderSummary(car, chosen) {
    const box = $('[data-summary]');
    if (!box) return 0;
    const lines = $('[data-summary-extras]', box);
    lines.innerHTML = chosen
        .map((a) => `<div class="summary__line"><span>${escapeHtml(a.name)}</span><span>${euro(a.price, true)}</span></div>`)
        .join('');
    const total = car.price + chosen.reduce((sum, a) => sum + a.price, 0);
    $$('[data-summary-total]').forEach((el) => (el.textContent = euro(total, true)));
    $('[data-summary-vat]', box).textContent = euro(vatOf(total), true);
    return total;
}

function initExtras() {
    const form = $('[data-extras-form]');
    if (!form) return;
    const car = carBySlug($('[data-summary]').dataset.car);
    const cart = store.get('cart');
    if (cart?.slug === car.slug) {
        $$('input[name="extras"]', form).forEach((box) => (box.checked = cart.extras.includes(Number(box.value))));
    }
    const chosen = () =>
        $$('input[name="extras"]:checked', form).map((box) => ({ id: Number(box.value), name: box.dataset.name, price: Number(box.dataset.price) }));
    const update = () => {
        renderSummary(car, chosen());
        store.set('cart', { slug: car.slug, extras: chosen().map((a) => a.id) });
    };
    form.addEventListener('change', update);
    update();
    $$('[data-extras-continue]').forEach((button) =>
        button.addEventListener('click', () => {
            update();
            window.location.href = form.dataset.next;
        }),
    );
}

function luhn(number) {
    let sum = 0;
    let double = false;
    for (let i = number.length - 1; i >= 0; i--) {
        let digit = Number(number[i]);
        if (double) {
            digit *= 2;
            if (digit > 9) digit -= 9;
        }
        sum += digit;
        double = !double;
    }
    return sum % 10 === 0;
}

function brandOf(digits) {
    if (/^4/.test(digits)) return 'VISA';
    if (/^(5[1-5]|2[2-7])/.test(digits)) return 'MASTERCARD';
    if (/^3[47]/.test(digits)) return 'AMEX';
    return '';
}

function initPay() {
    const form = $('[data-pay-form]');
    if (!form) return;
    const signin = $('[data-pay-signin]');
    const user = currentUser();
    const car = carBySlug(form.dataset.payCar);
    const cart = store.get('cart');
    const chosen = cart?.slug === car.slug ? car.additions.filter((a) => cart.extras.includes(a.id)) : [];
    const total = renderSummary(car, chosen);

    if (!user) {
        signin.hidden = false;
        return;
    }
    form.hidden = false;
    $('[data-pay-user]', form).textContent = 'Logged in as ' + user.email;
    form.elements.first_name.value = user.first_name;
    form.elements.last_name.value = user.last_name;
    form.elements.email.value = user.email;
    form.elements.phone.value = user.phone || '';
    form.elements.card_name.value = user.first_name + ' ' + user.last_name;

    const number = form.elements.card_number;
    const exp = form.elements.card_exp;
    const cvc = form.elements.card_cvc;
    const brand = $('[data-card-brand]', form);
    const errorBox = $('[data-pay-error]', form);
    const button = $('[data-pay-button]', form);

    number.addEventListener('input', () => {
        const digits = number.value.replace(/\D/g, '').slice(0, 16);
        number.value = digits.replace(/(.{4})/g, '$1 ').trim();
        brand.textContent = brandOf(digits);
    });
    exp.addEventListener('input', () => {
        const digits = exp.value.replace(/\D/g, '').slice(0, 4);
        exp.value = digits.length > 2 ? digits.slice(0, 2) + ' / ' + digits.slice(2) : digits;
    });
    cvc.addEventListener('input', () => (cvc.value = cvc.value.replace(/\D/g, '').slice(0, 4)));

    $$('[data-test-card]').forEach((b) =>
        b.addEventListener('click', () => {
            number.value = b.dataset.testCard;
            number.dispatchEvent(new Event('input'));
            const next = new Date();
            exp.value = '12 / ' + String((next.getFullYear() + 2) % 100).padStart(2, '0');
            cvc.value = '123';
            showError(number, '');
            showError(exp, '');
            showError(cvc, '');
            number.focus();
        }),
    );

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        errorBox.hidden = true;
        const digits = number.value.replace(/\D/g, '');
        const [mm, yy] = exp.value.split('/').map((p) => Number(p.trim()));
        const now = new Date();
        const expired = !mm || mm > 12 || !yy || 2000 + yy < now.getFullYear() || (2000 + yy === now.getFullYear() && mm < now.getMonth() + 1);
        const ok = validate(form, () => ({
            'card-number':
                digits.length === 0
                    ? 'Enter the card number.'
                    : digits.length < 16
                      ? `Card number looks incomplete (${digits.length} of 16 digits).`
                      : !luhn(digits)
                        ? 'This card number is not valid. Check the digits.'
                        : '',
            'card-exp': !exp.value ? 'Enter the expiry date as MM / YY.' : expired ? 'This card has expired. Use another card.' : '',
            'card-cvc': cvc.value.length < 3 ? 'Enter the 3 digits on the back of the card.' : '',
        }));
        if (!ok) return;

        button.classList.add('is-loading');
        button.setAttribute('aria-disabled', 'true');
        const label = button.innerHTML;
        button.textContent = 'Confirming payment';

        setTimeout(() => {
            if (digits === DECLINED) {
                button.classList.remove('is-loading');
                button.removeAttribute('aria-disabled');
                button.innerHTML = label;
                $('[data-pay-error-text]', form).textContent = 'Your card was declined and nothing was charged. Try another card or contact your bank.';
                errorBox.hidden = false;
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            const orders = store.get('orders', []);
            const order = {
                id: 'RM-2026-' + String(430 + orders.length + Math.floor(Math.random() * 40)).padStart(5, '0'),
                slug: car.slug,
                email: form.elements.email.value.trim().toLowerCase(),
                name: form.elements.first_name.value.trim(),
                buyer: form.elements.first_name.value.trim() + ' ' + form.elements.last_name.value.trim(),
                phone: form.elements.phone.value.trim(),
                extras: chosen.map((a) => a.name),
                total,
                status: 'requested',
                payment: 'paid',
                ref: 'pi_' + Math.random().toString(36).slice(2, 12),
                date: now.toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }),
            };
            store.set('orders', [order, ...orders]);
            store.set('lastOrder', order);
            store.set('carStatus', { ...store.get('carStatus', {}), [car.slug]: 'reserved' });
            store.remove('cart');
            window.location.href = data().routes.confirmed;
        }, 1600);
    });
}

function initConfirmed() {
    const root = $('[data-confirmation]');
    if (!root) return;
    const order = store.get('lastOrder');
    const car = order && carBySlug(order.slug);
    if (!car) {
        $('[data-confirmation-empty]').hidden = false;
        return;
    }
    root.hidden = false;
    $('[data-c-name]', root).textContent = order.name;
    $('[data-c-email]', root).textContent = order.email;
    $('[data-c-order]', root).textContent = order.id;
    $('[data-c-car]', root).textContent = car.title;
    $('[data-c-extras]', root).textContent = order.extras.length ? order.extras.join(', ') : 'None';
    $('[data-c-total]', root).textContent = euro(order.total, true);
    $('[data-c-ref]', root).textContent = order.ref;
    const img = $('[data-c-image]', root);
    img.src = car.image;
    img.alt = car.title;
}

export function initCheckout() {
    initExtras();
    initPay();
    initConfirmed();
}
