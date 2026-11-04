// Client-side filters and sorting for the car list. The URL keeps the
// filters, so links from the homepage (?body=suv&max=30000) work.

import { $, $$ } from './lib';

const SORTS = {
    newest: (a, b) => b.dataset.listed.localeCompare(a.dataset.listed),
    'price-asc': (a, b) => a.dataset.price - b.dataset.price,
    'price-desc': (a, b) => b.dataset.price - a.dataset.price,
    'mileage-asc': (a, b) => a.dataset.mileage - b.dataset.mileage,
    'year-desc': (a, b) => b.dataset.year - a.dataset.year,
};

export function initInventory() {
    const form = $('[data-filters]');
    const grid = $('[data-car-grid]');
    if (!form || !grid) return;
    const cards = $$('[data-car-card]', grid);
    const sort = $('[data-sort]');
    const chips = $$('[data-body-chip]');
    const empty = $('[data-empty]');
    const counts = $$('[data-result-count]');
    const noun = $('[data-result-noun]');

    const readUrl = () => {
        const params = new URLSearchParams(window.location.search);
        form.elements.body.value = params.get('body') || '';
        form.elements.brand.value = params.get('brand') || '';
        form.elements.min.value = params.get('min') || '';
        form.elements.max.value = params.get('max') || '';
        form.elements.km.value = params.get('km') || '';
        form.elements.year.value = params.get('year') || '';
        const fuels = (params.get('fuel') || '').split(',').filter(Boolean);
        $$('input[name="fuel"]', form).forEach((box) => (box.checked = fuels.includes(box.value)));
        $$('input[name="gearbox"]', form).forEach((r) => (r.checked = r.value === (params.get('gearbox') || '')));
        $$('input[name="condition"]', form).forEach((r) => (r.checked = r.value === (params.get('condition') || '')));
        if (params.get('sort') && SORTS[params.get('sort')]) sort.value = params.get('sort');
    };

    const state = () => ({
        body: form.elements.body.value,
        brand: form.elements.brand.value,
        min: Number(form.elements.min.value.replace(/\D/g, '')) || 0,
        max: Number(form.elements.max.value.replace(/\D/g, '')) || Infinity,
        km: Number(form.elements.km.value) || Infinity,
        year: Number(form.elements.year.value) || 0,
        fuel: $$('input[name="fuel"]:checked', form).map((b) => b.value),
        gearbox: form.querySelector('input[name="gearbox"]:checked')?.value || '',
        condition: form.querySelector('input[name="condition"]:checked')?.value || '',
    });

    const writeUrl = (s) => {
        const params = new URLSearchParams();
        if (s.body) params.set('body', s.body);
        if (s.brand) params.set('brand', s.brand);
        if (s.min) params.set('min', s.min);
        if (Number.isFinite(s.max)) params.set('max', s.max);
        if (Number.isFinite(s.km)) params.set('km', s.km);
        if (s.year) params.set('year', s.year);
        if (s.fuel.length) params.set('fuel', s.fuel.join(','));
        if (s.gearbox) params.set('gearbox', s.gearbox);
        if (s.condition) params.set('condition', s.condition);
        if (sort.value !== 'newest') params.set('sort', sort.value);
        const query = params.toString();
        history.replaceState(null, '', window.location.pathname + (query ? '?' + query : ''));
    };

    const apply = () => {
        const s = state();
        let shown = 0;
        cards.forEach((card) => {
            const d = card.dataset;
            const match =
                (!s.body || d.body === s.body) &&
                (!s.brand || d.brand === s.brand) &&
                Number(d.price) >= s.min &&
                Number(d.price) <= s.max &&
                Number(d.mileage) <= s.km &&
                Number(d.year) >= s.year &&
                (!s.fuel.length || s.fuel.includes(d.fuel)) &&
                (!s.gearbox || d.gearbox === s.gearbox) &&
                (!s.condition || d.condition === s.condition);
            card.hidden = !match;
            if (match) shown++;
        });
        cards.sort(SORTS[sort.value]).forEach((card) => grid.append(card));
        counts.forEach((el) => (el.textContent = shown));
        if (noun) noun.textContent = shown === 1 ? 'car' : 'cars';
        empty.hidden = shown > 0;
        grid.hidden = shown === 0;
        chips.forEach((chip) => chip.setAttribute('aria-pressed', chip.dataset.bodyChip === s.body));
        writeUrl(s);
    };

    readUrl();
    apply();

    form.addEventListener('input', apply);
    form.addEventListener('change', apply);
    sort.addEventListener('change', apply);
    form.addEventListener('submit', (e) => e.preventDefault());
    form.addEventListener('reset', () => setTimeout(apply));
    chips.forEach((chip) =>
        chip.addEventListener('click', () => {
            form.elements.body.value = chip.dataset.bodyChip;
            apply();
        }),
    );
    $('[data-filters-reset]')?.addEventListener('click', () => {
        form.reset();
        form.elements.body.value = '';
        setTimeout(apply);
    });

    // Bottom sheet on small screens
    const open = $('[data-filters-open]');
    let scrim;
    const setOpen = (on) => {
        form.classList.toggle('is-open', on);
        open?.setAttribute('aria-expanded', on);
        document.documentElement.style.overflow = on ? 'hidden' : '';
        if (on) {
            scrim = document.createElement('div');
            scrim.className = 'scrim';
            scrim.addEventListener('click', () => setOpen(false));
            document.body.append(scrim);
            form.querySelector('select, input')?.focus();
        } else {
            scrim?.remove();
        }
    };
    open?.addEventListener('click', () => setOpen(true));
    $$('[data-filters-close]').forEach((b) => b.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && form.classList.contains('is-open')) setOpen(false);
    });
}
