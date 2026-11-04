// Small shared helpers: storage that never throws, page data, formatting, toasts.

const memory = new Map();

export const store = {
    get(key, fallback = null) {
        try {
            const raw = localStorage.getItem('riva.' + key);
            return raw === null ? fallback : JSON.parse(raw);
        } catch {
            return memory.has(key) ? memory.get(key) : fallback;
        }
    },
    set(key, value) {
        memory.set(key, value);
        try {
            localStorage.setItem('riva.' + key, JSON.stringify(value));
        } catch {
            // Private mode or blocked storage: keep it in memory for this page.
        }
    },
    remove(key) {
        memory.delete(key);
        try {
            localStorage.removeItem('riva.' + key);
        } catch {
            // ignore
        }
    },
};

let cached;

export function data() {
    if (!cached) {
        const el = document.getElementById('riva-data');
        cached = el ? JSON.parse(el.textContent) : { cars: [], routes: {}, orders: [] };
    }
    return cached;
}

export function carBySlug(slug) {
    return data().cars.find((car) => car.slug === slug);
}

export function euro(amount, cents = false) {
    return (
        '€' +
        Number(amount).toLocaleString('en-US', {
            minimumFractionDigits: cents ? 2 : 0,
            maximumFractionDigits: cents ? 2 : 0,
        })
    );
}

export function $(selector, root = document) {
    return root.querySelector(selector);
}

export function $$(selector, root = document) {
    return Array.from(root.querySelectorAll(selector));
}

export function toast(message) {
    const region = $('[data-toasts]');
    if (!region) return;
    const el = document.createElement('div');
    el.className = 'toast';
    el.innerHTML =
        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l4 4 10-10"/></svg>';
    const text = document.createElement('span');
    text.textContent = message;
    el.append(text);
    region.append(el);
    setTimeout(() => {
        el.style.transition = 'opacity .3s ease';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 320);
    }, 3600);
}

export function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}
