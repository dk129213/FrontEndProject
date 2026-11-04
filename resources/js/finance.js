// Payment calculator on the car page. Same formula as Catalog::monthly().

import { $, $$, euro } from './lib';

export function monthly(price, depositPercent, months, apr) {
    const principal = price * (1 - depositPercent / 100);
    const rate = apr / 100 / 12;
    return principal * rate / (1 - (1 + rate) ** -months);
}

export function initFinance() {
    const root = $('[data-calc]');
    if (!root) return;
    const price = Number(root.dataset.price);
    const apr = Number(root.dataset.apr);
    const slider = $('[data-calc-deposit]', root);
    const terms = $$('[data-calc-term]', root);
    let months = Number(terms.find((t) => t.getAttribute('aria-pressed') === 'true')?.dataset.calcTerm || 60);

    const update = () => {
        const deposit = Number(slider.value);
        const pay = monthly(price, deposit, months, apr);
        const depositEur = (price * deposit) / 100;
        slider.style.setProperty('--fill', (deposit / Number(slider.max)) * 100 + '%');
        $('[data-calc-deposit-out]', root).textContent = `${deposit}%, ${euro(depositEur)}`;
        $('[data-calc-monthly]', root).textContent = euro(Math.round(pay));
        $('[data-calc-deposit-eur]', root).textContent = euro(depositEur);
        $('[data-calc-loan]', root).textContent = euro(price - depositEur);
        $('[data-calc-total]', root).textContent = euro(Math.round(pay * months + depositEur));
    };

    slider.addEventListener('input', update);
    terms.forEach((term) =>
        term.addEventListener('click', () => {
            months = Number(term.dataset.calcTerm);
            terms.forEach((t) => t.setAttribute('aria-pressed', t === term));
            update();
        }),
    );
    update();
}
