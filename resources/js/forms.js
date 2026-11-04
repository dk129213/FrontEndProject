// Inline validation before anything is "sent". Messages come from
// data-error on each field; the form gets a riva:valid event when clean.

import { $$, toast } from './lib';

const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

function messageFor(input) {
    const value = input.value.trim();
    if (input.required && !value) return input.dataset.error || 'This field is required.';
    if (!value) return '';
    if (input.type === 'email' && !EMAIL.test(value)) return input.dataset.error || 'Enter an email like name@example.com.';
    if (input.type === 'tel' && value.replace(/\D/g, '').length < 8) return input.dataset.error || 'Enter a full phone number.';
    if (input.minLength > 0 && value.length < input.minLength) return input.dataset.error;
    if (input.dataset.pattern && !new RegExp(input.dataset.pattern).test(value)) return input.dataset.error;
    if (input.dataset.min !== undefined) {
        const number = Number(value.replace(/[\s,.]/g, ''));
        if (!Number.isFinite(number) || number < Number(input.dataset.min) || number > Number(input.dataset.max)) return input.dataset.error;
    }
    return '';
}

export function showError(input, message) {
    const field = input.closest('.field') || input.parentElement;
    let el = field.querySelector('.field-error[data-for]');
    if (!message) {
        field.classList.remove('has-error');
        input.removeAttribute('aria-invalid');
        el?.remove();
        return;
    }
    field.classList.add('has-error');
    input.setAttribute('aria-invalid', 'true');
    if (!el) {
        el = document.createElement('p');
        el.className = 'field-error';
        el.dataset.for = input.id;
        el.id = input.id + '-error';
        field.append(el);
        const described = (input.getAttribute('aria-describedby') || '').split(' ').filter(Boolean);
        if (!described.includes(el.id)) input.setAttribute('aria-describedby', [...described, el.id].join(' '));
    }
    el.textContent = message;
}

export function validate(form, extra = () => ({})) {
    const inputs = $$('input, select, textarea', form).filter((el) => el.id && el.type !== 'file' && el.type !== 'radio' && el.type !== 'checkbox');
    const custom = extra();
    let first = null;
    inputs.forEach((input) => {
        const message = custom[input.id] ?? messageFor(input);
        showError(input, message);
        if (message && !first) first = input;
    });
    if (first) first.focus();
    return !first;
}

export function initForms() {
    $$('form[data-validate], form[data-register-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (!validate(form)) return;
            form.dispatchEvent(new CustomEvent('riva:valid'));
            if (form.dataset.success) {
                toast(form.dataset.success);
                if (form.hasAttribute('data-reset-on-success')) form.reset();
            }
        });
        // Clear a message as soon as the field is fixed.
        form.addEventListener('input', (event) => {
            const input = event.target;
            if (input.closest('.has-error') && !messageFor(input)) showError(input, '');
        });
    });
}
