// Heart buttons on cards and the car page. Saved per browser.

import { $$, store, toast } from './lib';

export function favourites() {
    return store.get('favourites', []);
}

function paint() {
    const saved = new Set(favourites());
    $$('[data-fav]').forEach((button) => {
        const on = saved.has(button.dataset.fav);
        button.setAttribute('aria-pressed', on);
        const label = button.querySelector('[data-fav-label]');
        if (label) label.textContent = on ? 'Saved to favourites' : 'Save to favourites';
    });
}

export function initFavourites() {
    paint();
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-fav]');
        if (!button) return;
        event.preventDefault();
        const slug = button.dataset.fav;
        const saved = favourites();
        const on = !saved.includes(slug);
        store.set('favourites', on ? [...saved, slug] : saved.filter((s) => s !== slug));
        paint();
        button.classList.remove('pop');
        void button.offsetWidth;
        if (on) button.classList.add('pop');
        toast(on ? 'Saved to favourites' : 'Removed from favourites');
        document.dispatchEvent(new CustomEvent('riva:favourites'));
    });
}
