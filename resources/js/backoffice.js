// Sales team and admin screens: search and filter tables, change statuses
// and roles, add a car with a live shop preview. Changes stay in this browser.

import { validate } from './forms';
import { $, $$, carBySlug, data, escapeHtml, euro, store, toast } from './lib';

const FUEL = { petrol: 'Petrol', diesel: 'Diesel', hybrid: 'Hybrid', electric: 'Electric' };
const GEARBOX = { manual: 'Manual', automatic: 'Automatic' };
const BODY = { suv: 'SUV', estate: 'Estate', hatchback: 'Hatchback', saloon: 'Saloon' };
const STATUS = { available: 'Available', reserved: 'Reserved', sold: 'Sold' };

function km(value) {
    return Number(value).toLocaleString('en-US') + ' km';
}

function addedCarRows() {
    const body = $('[data-cars-table] [data-table-body]');
    if (!body) return;
    const admin = document.body.dataset.area === 'admin';
    store
        .get('addedCars', [])
        .filter((c) => admin || c.area === 'sales')
        .forEach((c) => {
            const row = document.createElement('tr');
            row.dataset.row = '';
            row.dataset.status = c.status;
            row.dataset.search = `${c.brand} ${c.model} ${c.variant} ${c.vin}`.toLowerCase();
            row.innerHTML = `
                <td><div class="cell-car">
                    <img src="${c.image || document.body.dataset.base + '/images/placeholder-car.svg'}" alt="" width="144" height="90">
                    <span><strong>${escapeHtml(c.brand + ' ' + c.model)}</strong><span class="mono caption">${escapeHtml(c.vin)}</span></span>
                </div></td>
                <td class="num">${escapeHtml(c.production_year)}</td>
                <td>${FUEL[c.engine_type]}</td>
                <td>${GEARBOX[c.transmission]}</td>
                <td class="right num nowrap">${c.is_used === '1' ? km(c.mileage) : 'New'}</td>
                <td class="right num nowrap" style="font-weight: 700">${euro(c.price)}</td>
                ${admin ? `<td class="nowrap">${escapeHtml(c.salesman || 'Marko Babić')}</td>` : ''}
                <td><span class="badge badge--sea">Just added</span></td>`;
            body.prepend(row);
        });
}

function initTables() {
    const body = $('[data-table-body]');
    if (!body) return;
    const search = $('[data-table-search]');
    const chips = $$('[data-status-chip]');
    const count = $('[data-table-count]');
    const empty = $('[data-table-empty]');
    let status = '';
    const apply = () => {
        const q = (search?.value || '').trim().toLowerCase();
        const rows = $$('[data-row]', body);
        let shown = 0;
        rows.forEach((row) => {
            const live = row.querySelector('[data-status-select]')?.value || row.dataset.status;
            const match = (!status || live === status) && (!q || row.dataset.search.includes(q));
            row.hidden = !match;
            if (match) shown++;
        });
        chips.forEach((c) => c.setAttribute('aria-pressed', c.dataset.statusChip === status));
        if (count) count.textContent = `Showing ${shown} of ${rows.length}`;
        if (empty) empty.hidden = shown > 0;
    };
    search?.addEventListener('input', apply);
    chips.forEach((chip) =>
        chip.addEventListener('click', () => {
            status = chip.dataset.statusChip;
            apply();
        }),
    );
    apply();
}

function initStatusChanges() {
    const saved = store.get('carStatus', {});
    $$('[data-status-select]').forEach((select) => {
        const slug = select.dataset.statusSelect;
        if (saved[slug]) select.value = saved[slug];
        select.addEventListener('change', () => {
            store.set('carStatus', { ...store.get('carStatus', {}), [slug]: select.value });
            toast(`${carBySlug(slug)?.name ?? 'Car'} is now ${STATUS[select.value].toLowerCase()}`);
        });
    });
    $$('[data-role-select]').forEach((select) => {
        select.addEventListener('change', () => toast(`${select.dataset.roleSelect} is now ${select.selectedOptions[0].textContent.toLowerCase()}`));
    });
    $$('[data-extra-toggle]').forEach((box) => {
        box.addEventListener('change', () => {
            const badge = box.parentElement.querySelector('.badge');
            badge.textContent = box.checked ? 'Offered' : 'Hidden';
            badge.className = 'badge' + (box.checked ? ' badge--success' : '');
            toast(`${box.dataset.extraToggle} is ${box.checked ? 'offered again' : 'hidden from checkout'}`);
        });
    });
}

function initSalesRows() {
    const body = $('[data-sales-body]');
    if (!body) return;
    const admin = document.body.dataset.area === 'admin';
    store.get('orders', []).forEach((o) => {
        const car = carBySlug(o.slug);
        if (!car) return;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="nowrap"><span class="mono">${o.id}</span><br><span class="caption">${escapeHtml(o.date)}</span></td>
            <td><div class="cell-car" style="min-width: 200px"><img src="${car.image}" alt="" width="144" height="90"><span><strong>${escapeHtml(car.name)}</strong><span class="caption">${euro(car.price)}</span></span></div></td>
            <td class="nowrap">${escapeHtml(o.buyer || o.name)}<br><span class="caption">${escapeHtml(o.phone || '')}</span></td>
            <td>${o.extras.length ? escapeHtml(o.extras.join(', ')) : 'None'}</td>
            ${admin ? '<td class="nowrap">Online order</td>' : ''}
            <td class="right num nowrap" style="font-weight: 700">${euro(o.total)}</td>
            <td><span class="badge badge--sea">Requested</span></td>
            <td><span class="badge badge--success">Paid</span><br><span class="mono caption">${o.ref}</span></td>`;
        body.prepend(row);
    });
}

function initCarForm() {
    const form = $('[data-car-form]');
    if (!form) return;
    const el = (name) => form.elements[name];
    const val = (name) => {
        const field = el(name);
        return field instanceof RadioNodeList ? field.value : field?.value?.trim() ?? '';
    };
    const preview = () => {
        const used = val('is_used') === '1';
        $('[data-preview-name]').textContent = [val('brand'), val('model')].filter(Boolean).join(' ') || 'Make and model';
        $('[data-preview-variant]').textContent = [val('production_year'), val('variant')].filter(Boolean).join(' ') || 'Year and trim';
        $('[data-preview-km]').textContent = used ? (val('mileage') ? km(val('mileage').replace(/\D/g, '')) : 'Mileage') : 'New';
        $('[data-preview-fuel]').textContent = FUEL[val('engine_type')];
        $('[data-preview-gearbox]').textContent = GEARBOX[val('transmission')];
        $('[data-preview-hp]').textContent = val('horsepower') ? val('horsepower') + ' hp' : 'Power';
        $('[data-preview-price]').textContent = euro(Number(val('price').replace(/\D/g, '')) || 0);
        $('[data-preview-body]').textContent = BODY[val('car_category')] || 'Body';
        $('[data-preview-new]').hidden = used;
    };
    form.addEventListener('input', preview);
    form.addEventListener('change', preview);
    preview();

    let firstPhoto = '';
    const input = $('[data-photo-input]');
    const previews = $('[data-photo-previews]');
    input.addEventListener('change', () => {
        previews.innerHTML = '';
        firstPhoto = '';
        Array.from(input.files)
            .slice(0, 12)
            .forEach((file, i) => {
                const url = URL.createObjectURL(file);
                const img = document.createElement('img');
                img.src = url;
                img.alt = `Photo ${i + 1}`;
                previews.append(img);
                if (i === 0) {
                    $('[data-preview-image]').src = url;
                    // keep a small copy so the new row has a picture after the redirect
                    const reader = new FileReader();
                    reader.onload = () => {
                        const image = new Image();
                        image.onload = () => {
                            const canvas = document.createElement('canvas');
                            canvas.width = 288;
                            canvas.height = 180;
                            canvas.getContext('2d').drawImage(image, 0, 0, 288, 180);
                            firstPhoto = canvas.toDataURL('image/jpeg', 0.7);
                        };
                        image.src = reader.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (!validate(form)) return;
        const salesman = el('salesman_id');
        const car = {
            area: form.dataset.area,
            brand: val('brand'),
            model: val('model'),
            variant: val('variant'),
            is_used: val('is_used'),
            car_category: val('car_category'),
            production_year: val('production_year'),
            mileage: val('mileage').replace(/\D/g, ''),
            vin: val('vin').toUpperCase(),
            engine_type: val('engine_type'),
            transmission: val('transmission'),
            horsepower: val('horsepower'),
            price: Number(val('price').replace(/\D/g, '')),
            status: val('status'),
            salesman: salesman ? salesman.selectedOptions[0].textContent : '',
            image: firstPhoto,
        };
        store.set('addedCars', [...store.get('addedCars', []), car]);
        store.set('flash', `${car.brand} ${car.model} is published`);
        window.location.href = data().routes.carsList;
    });
}

export function initBackoffice() {
    if (document.body.dataset.page !== 'backoffice') return;
    addedCarRows();
    initSalesRows();
    initTables();
    initStatusChanges();
    initCarForm();
}
