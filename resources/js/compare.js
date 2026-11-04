// Compare up to three cars: the Compare tick on cards, the floating tray,
// and the side-by-side table on /compare. The choice is kept per browser.

import { $, $$, carBySlug, escapeHtml, euro, store, toast } from './lib';

const MAX = 3;

const chosen = () => store.get('compare', []).filter((slug) => carBySlug(slug));

function save(list) {
    store.set('compare', list);
    document.dispatchEvent(new CustomEvent('riva:compare'));
}

function toggle(slug, on) {
    const list = chosen();
    if (on && !list.includes(slug)) {
        if (list.length >= MAX) {
            toast(`You can compare up to ${MAX} cars. Remove one first.`);
            return false;
        }
        save([...list, slug]);
    } else if (!on) {
        save(list.filter((s) => s !== slug));
    }
    return true;
}

function paintChecks() {
    const list = chosen();
    $$('[data-compare]').forEach((box) => (box.checked = list.includes(box.dataset.compare)));
    $$('[data-compare-pick]').forEach((b) => b.setAttribute('aria-pressed', list.includes(b.dataset.comparePick)));
}

function paintTray() {
    const tray = $('[data-compare-tray]');
    if (!tray) return;
    const list = chosen();
    tray.hidden = list.length === 0 || document.body.dataset.page === 'compare';
    $('[data-compare-list]', tray).innerHTML = list
        .map((slug) => `<img src="${carBySlug(slug).thumb}" alt="${escapeHtml(carBySlug(slug).name)}">`)
        .join('');
    $('[data-compare-hint]', tray).textContent = list.length === 1 ? 'Add one more car to compare' : `${list.length} cars selected`;
    $('[data-compare-go]', tray).toggleAttribute('aria-disabled', list.length < 2);
}

// Rows of the table: label, value, and which direction is better
const ROWS = [
    ['Price', (c) => euro(c.price), (c) => c.price, 'low'],
    ['Monthly, example', (c) => euro(c.monthly), (c) => c.monthly, 'low'],
    ['Condition', (c) => c.condition],
    ['First registered', (c) => c.year, (c) => c.year, 'high'],
    ['Mileage', (c) => (c.condition === 'New' ? 'New' : c.mileage.toLocaleString('en-US') + ' km'), (c) => c.mileage, 'low'],
    ['Fuel', (c) => c.fuel],
    ['Engine', (c) => c.engine],
    ['Power', (c) => c.hp + ' hp', (c) => c.hp, 'high'],
    ['Gearbox', (c) => c.gearbox + (c.gears > 1 ? `, ${c.gears} gears` : '')],
    ['Drive', (c) => c.drive],
    ['Body', (c) => c.body],
    ['Consumption or range', (c) => c.consumption],
    ['Colour', (c) => c.colour],
    ['Previous owners', (c) => c.owners || 'None', (c) => c.owners, 'low'],
];

function renderTable() {
    const wrap = $('[data-compare-table]');
    if (!wrap) return;
    const cars = chosen().map(carBySlug);
    const empty = $('[data-compare-empty]');
    empty.hidden = cars.length >= 2;
    wrap.hidden = cars.length < 2;
    if (cars.length < 2) return;

    const head = cars
        .map(
            (c) => `<th scope="col"><div class="compare-head">
                <span class="stage stage--card"><img class="stage__car" src="${c.thumb}" alt="" width="320" height="200"></span>
                <a href="${c.url}">${escapeHtml(c.title)}</a>
                <span class="caption">${escapeHtml(c.variant)}</span>
                <div class="row" style="--row: 8px">
                    ${
                        c.status === 'available'
                            ? `<a class="btn btn--primary btn--sm" href="${c.extrasUrl}" style="color: var(--on-signal); font-size: 13px">Reserve</a>`
                            : '<span class="badge badge--warning">Reserved</span>'
                    }
                    <button type="button" class="btn btn--ghost btn--sm" data-compare-remove="${c.slug}">Remove</button>
                </div>
            </div></th>`,
        )
        .join('');

    const body = ROWS.map(([label, show, score, better]) => {
        let best = null;
        if (score) {
            const values = cars.map(score);
            const target = better === 'low' ? Math.min(...values) : Math.max(...values);
            if (values.filter((v) => v === target).length === 1) best = values.indexOf(target);
        }
        const cells = cars.map((c, i) => `<td class="${i === best ? 'is-best' : ''}">${escapeHtml(String(show(c)))}</td>`).join('');
        return `<tr><th scope="row">${label}</th>${cells}</tr>`;
    }).join('');

    wrap.innerHTML = `<table class="compare-table"><colgroup><col style="width: 190px">${cars.map(() => '<col>').join('')}</colgroup>
        <thead><tr><td></td>${head}</tr></thead><tbody>${body}</tbody></table>`;
    $$('[data-compare-remove]', wrap).forEach((b) => b.addEventListener('click', () => toggle(b.dataset.compareRemove, false)));
}

export function initCompare() {
    document.addEventListener('change', (event) => {
        const box = event.target.closest('[data-compare]');
        if (!box) return;
        if (!toggle(box.dataset.compare, box.checked)) box.checked = false;
    });
    $$('[data-compare-pick]').forEach((b) => b.addEventListener('click', () => toggle(b.dataset.comparePick, b.getAttribute('aria-pressed') !== 'true')));
    $('[data-compare-clear]')?.addEventListener('click', () => save([]));
    $('[data-compare-go]')?.addEventListener('click', (event) => {
        if (chosen().length < 2) {
            event.preventDefault();
            toast('Tick Compare on one more car first.');
        }
    });

    const paint = () => {
        paintChecks();
        paintTray();
        renderTable();
    };
    document.addEventListener('riva:compare', paint);
    paint();
}
