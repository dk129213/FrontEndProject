// Simulated login. The site is static, so accounts live in the browser.
// Demo accounts match resources/data/backoffice.php (users 1, 2 and 4).

import { $, $$, data, store, toast } from './lib';

export const DEMO_PASSWORD = 'riva-demo';

const DEMO_USERS = {
    admin: { id: 1, first_name: 'Petra', last_name: 'Kovač', email: 'admin@rivamotors.example', phone: '+385 91 000 1101', role: 'admin' },
    salesman: { id: 2, first_name: 'Marko', last_name: 'Babić', email: 'sales@rivamotors.example', phone: '+385 91 000 1102', role: 'salesman' },
    customer: { id: 4, first_name: 'Ana', last_name: 'Horvat', email: 'customer@rivamotors.example', phone: '+385 98 000 2201', role: 'customer' },
};

const ROLE_LABELS = { admin: 'Admin', salesman: 'Sales team', customer: 'Customer' };

export function currentUser() {
    return store.get('user');
}

export function initials(user) {
    return (user.first_name[0] + user.last_name[0]).toUpperCase();
}

function signIn(user) {
    store.set('user', user);
}

export function signOut() {
    store.remove('user');
    window.location.href = data().routes.home;
}

function homeFor(user) {
    const { routes } = data();
    if (user.role === 'admin') return routes.admin;
    if (user.role === 'salesman') return routes.sales;
    return routes.account;
}

function findAccount(email, password) {
    const lower = email.trim().toLowerCase();
    const demo = Object.values(DEMO_USERS).find((u) => u.email === lower);
    if (demo) return password === DEMO_PASSWORD ? demo : null;
    const registered = store.get('accounts', []).find((u) => u.email === lower);
    return registered && registered.password === password ? stripPassword(registered) : null;
}

function stripPassword(account) {
    const { password, ...user } = account;
    return user;
}

function afterLogin(user, form) {
    const next = new URLSearchParams(window.location.search).get('next');
    if (form?.hasAttribute('data-stay')) {
        window.location.reload();
        return;
    }
    window.location.href = next && next.startsWith('/') ? next : homeFor(user);
}

function wireLoginForms() {
    $$('[data-login-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const error = $('[data-login-error]', form);
            const email = form.elements.email.value;
            const password = form.elements.password.value;
            if (!email || !password) {
                error.textContent = 'Enter your email and password.';
                error.hidden = false;
                return;
            }
            const user = findAccount(email, password);
            if (!user) {
                error.textContent = 'That email and password do not match. Check for typos, or use a demo account below.';
                error.hidden = false;
                form.elements.password.focus();
                return;
            }
            signIn(user);
            afterLogin(user, form);
        });
    });

    $$('[data-demo-login]').forEach((button) => {
        button.addEventListener('click', () => {
            const user = DEMO_USERS[button.dataset.demoLogin];
            signIn(user);
            afterLogin(user, button.closest('section')?.querySelector('[data-login-form]'));
        });
    });
}

function wireRegister() {
    const form = $('[data-register-form]');
    if (!form) return;
    form.addEventListener('riva:valid', () => {
        const email = form.elements.email.value.trim().toLowerCase();
        const accounts = store.get('accounts', []);
        const taken = accounts.some((a) => a.email === email) || Object.values(DEMO_USERS).some((u) => u.email === email);
        if (taken) {
            const field = form.elements.email.closest('.field');
            field.classList.add('has-error');
            let msg = field.querySelector('.field-error');
            if (!msg) {
                msg = document.createElement('p');
                msg.className = 'field-error';
                field.append(msg);
            }
            msg.textContent = 'An account with this email already exists. Log in instead.';
            return;
        }
        const account = {
            id: Date.now(),
            first_name: form.elements.first_name.value.trim(),
            last_name: form.elements.last_name.value.trim(),
            email,
            phone: form.elements.phone.value.trim(),
            role: 'customer',
            password: form.elements.password.value,
        };
        store.set('accounts', [...accounts, account]);
        signIn(stripPassword(account));
        afterLogin(account, null);
    });
}

function wireTabs() {
    const root = $('[data-auth]');
    if (!root) return;
    const tabs = $$('[data-tab]', root);
    const select = (name) => {
        tabs.forEach((tab) => {
            const on = tab.dataset.tab === name;
            tab.setAttribute('aria-selected', on);
            tab.tabIndex = on ? 0 : -1;
            document.getElementById(tab.getAttribute('aria-controls')).hidden = !on;
        });
    };
    tabs.forEach((tab) => {
        tab.addEventListener('click', () => select(tab.dataset.tab));
        tab.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
                const other = tabs.find((t) => t !== tab);
                select(other.dataset.tab);
                other.focus();
            }
        });
    });
    if (new URLSearchParams(window.location.search).get('tab') === 'register') select('register');
}

// Fill name, email and role anywhere the page asks for them.
function paintUser(user) {
    $$('[data-account-link]').forEach((link) => {
        link.href = user ? homeFor(user) : data().routes.login;
    });
    $$('[data-account-label]').forEach((el) => {
        el.textContent = user ? (user.role === 'customer' ? 'My account' : user.first_name) : 'Log in';
    });
    if (!user) return;
    $$('[data-user-first]').forEach((el) => (el.textContent = user.first_name));
    $$('[data-user-name]').forEach((el) => (el.textContent = user.first_name + ' ' + user.last_name));
    $$('[data-user-email]').forEach((el) => (el.textContent = user.email));
    $$('[data-user-role]').forEach((el) => (el.textContent = ROLE_LABELS[user.role]));
    $$('[data-user-initials]').forEach((el) => (el.textContent = initials(user)));
    $$('[data-admin-only]').forEach((el) => (el.hidden = user.role !== 'admin' || document.body.dataset.area === 'admin'));
    $$('[data-sales-switch]').forEach((el) => (el.hidden = user.role !== 'admin' || document.body.dataset.area === 'sales'));
    $$('[data-staff-link]').forEach((el) => {
        el.hidden = user.role === 'customer';
        el.href = homeFor(user);
    });
}

// Pages that need a role check it before showing their content.
function guard(user) {
    const required = document.body.dataset.requireRole;
    if (!required) return true;
    if (!user) {
        const here = window.location.pathname + window.location.search;
        window.location.replace(data().routes.login + '?next=' + encodeURIComponent(here));
        return false;
    }
    if (!required.split(',').includes(user.role)) {
        store.set(
            'flash',
            required === 'admin'
                ? `The admin area is for admins. You are logged in as ${ROLE_LABELS[user.role].toLowerCase()}.`
                : 'That area is for the sales team. You are logged in as a customer.',
        );
        window.location.replace(homeFor(user));
        return false;
    }
    document.body.classList.add('auth-ok');
    return true;
}

export function initAuth() {
    const user = currentUser();
    if (!guard(user)) return;
    paintUser(user);
    wireLoginForms();
    wireRegister();
    wireTabs();
    $$('[data-logout]').forEach((button) => button.addEventListener('click', signOut));

    const flash = store.get('flash');
    if (flash) {
        store.remove('flash');
        toast(flash);
    }
}
