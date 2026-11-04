// Header menu, backoffice side nav, gallery, copy-to-clipboard.

import { $, $$, store, toast } from './lib';

function drawer(toggle, panel, { onOpen, onClose } = {}) {
    let scrim;
    const set = (open) => {
        toggle.setAttribute('aria-expanded', open);
        panel.classList.toggle('is-open', open);
        document.documentElement.style.overflow = open ? 'hidden' : '';
        if (open) {
            onOpen?.();
            if (panel.dataset.scrim !== 'none') {
                scrim = document.createElement('div');
                scrim.className = 'scrim';
                scrim.addEventListener('click', () => set(false));
                document.body.append(scrim);
            }
            panel.querySelector('a, button, input, select')?.focus();
        } else {
            onClose?.();
            scrim?.remove();
        }
    };
    toggle.addEventListener('click', () => set(toggle.getAttribute('aria-expanded') !== 'true'));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && panel.classList.contains('is-open')) {
            set(false);
            toggle.focus();
        }
    });
    return set;
}

function initMenu() {
    const toggle = $('[data-menu-toggle]');
    const panel = $('#mobile-nav');
    if (!toggle || !panel) return;
    panel.dataset.scrim = 'none';
    drawer(toggle, panel, {
        onOpen: () => {
            $('[data-icon-open]', toggle).setAttribute('hidden', '');
            $('[data-icon-close]', toggle).removeAttribute('hidden');
        },
        onClose: () => {
            $('[data-icon-open]', toggle).removeAttribute('hidden');
            $('[data-icon-close]', toggle).setAttribute('hidden', '');
        },
    });
    $$('a', panel).forEach((a) => a.addEventListener('click', () => toggle.click()));
}

function initSideNav() {
    const toggle = $('[data-side-toggle]');
    const panel = $('[data-side-nav]');
    if (toggle && panel) drawer(toggle, panel);
}

function initGallery() {
    const root = $('[data-gallery]');
    if (!root) return;
    const main = $('[data-gallery-main]', root);
    const thumbs = $$('[data-gallery-thumb]', root);
    const index = $('[data-gallery-index]', root);
    if (thumbs.length < 2) return;
    let current = 0;
    const show = (i) => {
        current = (i + thumbs.length) % thumbs.length;
        const t = thumbs[current];
        main.src = t.dataset.src;
        main.alt = t.dataset.alt;
        main.classList.toggle('is-studio', t.dataset.studio === '1');
        main.style.animation = 'none';
        void main.offsetWidth;
        main.style.animation = '';
        thumbs.forEach((b, n) => b.setAttribute('aria-pressed', n === current));
        if (index) index.textContent = current + 1;
    };
    thumbs.forEach((b, n) => b.addEventListener('click', () => show(n)));
    $('[data-gallery-prev]', root)?.addEventListener('click', () => show(current - 1));
    $('[data-gallery-next]', root)?.addEventListener('click', () => show(current + 1));

    // Swipe on touch screens
    let startX = null;
    main.addEventListener('touchstart', (e) => (startX = e.touches[0].clientX), { passive: true });
    main.addEventListener('touchend', (e) => {
        if (startX === null) return;
        const dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) > 40) show(current + (dx < 0 ? 1 : -1));
        startX = null;
    });
}

function initCopy() {
    $$('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                toast('Link copied');
            } catch {
                toast(button.dataset.copy);
            }
        });
    });
}

// A missing photo shows the "Photo coming soon" well instead of a broken image.
function initImageFallback() {
    const placeholder = document.body.dataset.base + '/images/placeholder-car.svg';
    const swap = (img) => {
        if (img.dataset.fallback || !img.closest('.car-card__media, .gallery, .cell-car, .summary__car, .order-row, .type-tile, .hero__media')) return;
        img.dataset.fallback = '1';
        img.src = placeholder;
        img.classList.add('img-missing');
    };
    document.addEventListener('error', (event) => event.target.tagName === 'IMG' && swap(event.target), true);
    $$('img').forEach((img) => img.complete && img.naturalWidth === 0 && img.getAttribute('src') && swap(img));
}

// Light and dark mode. The head script already applied the saved choice or
// the device setting; this keeps the toggle in sync and remembers the choice.
function initTheme() {
    const root = document.documentElement;
    const paint = () => {
        const dark = root.dataset.theme === 'dark';
        $$('[data-theme-toggle]').forEach((b) => b.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode'));
    };
    $$('[data-theme-toggle]').forEach((button) =>
        button.addEventListener('click', () => {
            const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
            root.classList.add('theme-switching');
            root.dataset.theme = next;
            store.set('theme', next);
            paint();
            setTimeout(() => root.classList.remove('theme-switching'), 350);
        }),
    );
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
        if (store.get('theme')) return;
        root.dataset.theme = event.matches ? 'dark' : 'light';
        paint();
    });
    paint();
}

// Full-screen photo viewer on the car page: arrows, swipe, Esc to close.
function initLightbox() {
    const box = $('[data-lightbox]');
    if (!box) return;
    const img = $('[data-lightbox-img]', box);
    const thumbs = $$('[data-lightbox-thumb]', box);
    const index = $('[data-lightbox-index]', box);
    let current = 0;
    const show = (i) => {
        current = (i + thumbs.length) % thumbs.length;
        img.src = thumbs[current].dataset.src;
        img.alt = thumbs[current].dataset.alt;
        img.classList.toggle('is-studio', thumbs[current].dataset.studio === '1');
        img.style.animation = 'none';
        void img.offsetWidth;
        img.style.animation = '';
        thumbs.forEach((t, n) => t.setAttribute('aria-pressed', n === current));
        index.textContent = current + 1;
    };
    const open = () => {
        const pressed = $$('[data-gallery-thumb]').findIndex((t) => t.getAttribute('aria-pressed') === 'true');
        show(Math.max(0, pressed));
        box.showModal();
    };
    $('[data-lightbox-open]')?.addEventListener('click', (e) => {
        e.stopPropagation();
        open();
    });
    $('[data-gallery-main]')?.addEventListener('click', open);
    $('[data-lightbox-close]', box).addEventListener('click', () => box.close());
    $('[data-lightbox-prev]', box)?.addEventListener('click', () => show(current - 1));
    $('[data-lightbox-next]', box)?.addEventListener('click', () => show(current + 1));
    thumbs.forEach((t, n) => t.addEventListener('click', () => show(n)));
    box.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') show(current + 1);
        if (e.key === 'ArrowLeft') show(current - 1);
    });
    let startX = null;
    img.addEventListener('touchstart', (e) => (startX = e.touches[0].clientX), { passive: true });
    img.addEventListener('touchend', (e) => {
        if (startX === null) return;
        const dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) > 40) show(current + (dx < 0 ? 1 : -1));
        startX = null;
    });
}

// Sections rise in once as they scroll into view.
function initReveal() {
    const items = $$('[data-reveal]');
    if (!items.length || !('IntersectionObserver' in window)) return;
    document.documentElement.classList.add('js-reveal');
    const observer = new IntersectionObserver(
        (entries) =>
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }),
        { rootMargin: '0px 0px -10% 0px' },
    );
    items.forEach((item) => observer.observe(item));
}

// Homepage turntable: one car on stage, arrows drive the next one in.
function initShowroom() {
    const root = $('[data-showroom]');
    if (!root) return;
    const slides = $$('[data-slide]', root);
    const plates = $$('[data-plate]', root);
    const index = $('[data-showroom-index]', root);
    if (slides.length < 2) return;
    let current = 0;
    let busy = false;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const go = (step) => {
        if (busy) return;
        const next = (current + step + slides.length) % slides.length;
        const leaving = slides[current];
        const entering = slides[next];
        plates[current].hidden = true;
        plates[next].hidden = false;
        index.textContent = next + 1;
        if (reduced) {
            leaving.hidden = true;
            entering.hidden = false;
            current = next;
            return;
        }
        busy = true;
        leaving.classList.add('is-leaving');
        leaving.classList.toggle('to-right', step < 0);
        setTimeout(() => {
            leaving.hidden = true;
            leaving.classList.remove('is-leaving', 'to-right');
            entering.hidden = false;
            entering.classList.add('is-entering');
            entering.classList.toggle('from-left', step < 0);
            setTimeout(() => {
                entering.classList.remove('is-entering', 'from-left');
                busy = false;
            }, 900);
        }, 450);
        current = next;
    };

    $('[data-showroom-next]', root).addEventListener('click', () => go(1));
    $('[data-showroom-prev]', root).addEventListener('click', () => go(-1));
    root.addEventListener('keydown', (event) => {
        if (event.target.closest('a, input, select')) return;
        if (event.key === 'ArrowRight') go(1);
        if (event.key === 'ArrowLeft') go(-1);
    });
    let startX = null;
    root.addEventListener('touchstart', (e) => (startX = e.touches[0].clientX), { passive: true });
    root.addEventListener('touchend', (e) => {
        if (startX === null) return;
        const dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) > 50) go(dx < 0 ? 1 : -1);
        startX = null;
    });
}

export function initUi() {
    initTheme();
    initShowroom();
    initLightbox();
    initReveal();
    initImageFallback();
    initMenu();
    initSideNav();
    initGallery();
    initCopy();
}
