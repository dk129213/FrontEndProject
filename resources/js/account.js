// Customer account: orders (seeded plus ones placed in this browser) and favourites.

import { currentUser } from './auth';
import { favourites } from './favourites';
import { $, $$, carBySlug, data, escapeHtml, euro, store } from './lib';

const TONES = { paid: 'success', pending: 'warning', failed: 'danger', requested: 'sea', completed: 'success', cancelled: '' };
const LABELS = { requested: 'Being prepared', completed: 'Collected', cancelled: 'Cancelled', paid: 'Paid', pending: 'Pending', failed: 'Failed' };

function badge(status) {
    return `<span class="badge ${TONES[status] ? 'badge--' + TONES[status] : ''}">${LABELS[status] || status}</span>`;
}

function renderOrders(user) {
    const list = $('[data-orders]');
    if (!list) return;
    const mine = [...store.get('orders', []), ...data().orders].filter((o) => o.email === user.email);
    $('[data-orders-empty]').hidden = mine.length > 0;
    list.innerHTML = mine
        .map((o) => {
            const car = carBySlug(o.slug);
            if (!car) return '';
            return `<article class="order-row">
                <img src="${car.image}" alt="" width="240" height="150">
                <div class="stack" style="--stack: 4px">
                    <strong>${escapeHtml(car.title)}</strong>
                    <span class="caption"><span class="mono">${o.id}</span>, ${escapeHtml(o.date)}</span>
                    <span class="caption">Extras: ${o.extras.length ? escapeHtml(o.extras.join(', ')) : 'none'}</span>
                </div>
                <div class="stack" style="--stack: 6px; align-items: flex-end">
                    <span class="price">${euro(o.total)}</span>
                    <span class="row" style="--row: 6px">${badge(o.status)}${badge(o.payment)}</span>
                </div>
            </article>`;
        })
        .join('');
}

function renderFavourites() {
    const grid = $('[data-favourites]');
    if (!grid) return;
    const saved = new Set(favourites());
    let count = 0;
    $$('[data-fav-card]', grid).forEach((el) => {
        el.hidden = !saved.has(el.dataset.favCard);
        if (!el.hidden) count++;
    });
    $('[data-fav-empty]').hidden = count > 0;
}

export function initAccount() {
    if (document.body.dataset.page !== 'account') return;
    const user = currentUser();
    if (!user) return;
    renderOrders(user);
    renderFavourites();
    document.addEventListener('riva:favourites', renderFavourites);
}
