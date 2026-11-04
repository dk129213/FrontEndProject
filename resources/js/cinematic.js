// Cinematic homepage: the car film stops at a few chapters. One scroll,
// swipe or arrow key plays the real video on to the next chapter, then its
// caption fades in. Going back plays a reversed copy of the film, since
// browsers cannot play video backwards smoothly. After the last chapter the
// page scrolls normally. Playback only happens when the visitor asks for it,
// like pressing play, so it runs with reduced motion too.
// Without JavaScript the hero is a still with the headline and search.

import { $, $$ } from './lib';

const QUIET_MS = 220; // wheel silence needed before the next step (trackpad inertia)
const END_MARGIN = 0.06; // stop just before the very end so the video never "ends"

const once = (el, event) => new Promise((resolve) => el.addEventListener(event, resolve, { once: true }));

// Load the whole file into memory first: seeking then works on any server
// (even ones without range requests) and playback never stalls mid-step.
async function loadInto(video, url) {
    try {
        const blob = await (await fetch(url)).blob();
        video.src = URL.createObjectURL(blob);
    } catch {
        video.src = url;
    }
}

async function seek(video, time) {
    if (Math.abs(video.currentTime - time) < 0.02) return;
    video.currentTime = time;
    await once(video, 'seeked');
}

export function initCinematic() {
    const root = $('[data-cine]');
    if (!root) return;

    const small = window.innerWidth < 720;
    const forward = $('[data-cine-forward]', root);
    const reverse = $('[data-cine-reverse]', root);
    const bar = $('[data-cine-bar]', root);
    const dots = $$('[data-cine-dot]', root);
    const captions = $$('.cine__caption', root);

    loadInto(forward, small ? root.dataset.mobile : root.dataset.desktop);
    const reverseSrc = small ? root.dataset.mobileReverse : root.dataset.desktopReverse;

    let duration = 0;
    let times = [];
    let chapter = 0;
    let playing = false;
    let lastWheel = 0;
    let run = 0; // bumped by Skip, so a step that was interrupted does not finish
    let cancelPlay = null;

    root.classList.add('cine--live');

    const show = (video) => {
        forward.classList.toggle('is-active', video === forward);
        reverse.classList.toggle('is-active', video === reverse);
    };

    const showCaption = (index) => {
        captions.forEach((el, i) => el.classList.toggle('is-on', i === index));
        dots.forEach((d, i) => d.setAttribute('aria-current', i === index ? 'step' : 'false'));
        root.classList.toggle('is-last', index === captions.length - 1);
    };

    // Progress line follows whichever video is playing
    const paintBar = () => {
        if (!duration) return;
        const t = reverse.classList.contains('is-active') ? duration - reverse.currentTime : forward.currentTime;
        bar.style.setProperty('--p', Math.min(1, Math.max(0, t / duration)).toFixed(4));
    };

    forward.addEventListener('loadedmetadata', () => {
        duration = forward.duration;
        times = captions.map((el) => Math.min(duration - END_MARGIN, Number(el.dataset.at) * duration));
    });
    forward.addEventListener(
        'loadeddata',
        () => {
            show(forward);
            root.classList.add('is-ready');
        },
        { once: true },
    );

    // Play a video until it reaches the stop time, then pause. Checked on every decoded
    // frame where the browser supports it, with a short timer as a backup.
    // Resolves false if playback is refused, null if Skip cancelled it.
    const playUntil = (video, stop) =>
        new Promise((resolve) => {
            let done = false;
            let timer;
            const finish = (ok) => {
                if (done) return;
                done = true;
                cancelPlay = null;
                clearInterval(timer);
                video.pause();
                resolve(ok);
            };
            const check = () => {
                if (done) return;
                paintBar();
                if (video.currentTime >= stop - 0.03 || video.ended) return finish(true);
                if (video.requestVideoFrameCallback) video.requestVideoFrameCallback(check);
            };
            cancelPlay = () => finish(null);
            video
                .play()
                .then(() => {
                    check();
                    timer = setInterval(check, 40);
                })
                .catch(() => finish(false));
        });

    const jump = async (target) => {
        await seek(forward, times[target]);
        show(forward);
        paintBar();
    };

    const goTo = async (target) => {
        if (playing || target < 0 || target >= captions.length || target === chapter || !duration) return;
        const from = times[chapter];
        const to = times[target];
        chapter = target;
        captions.forEach((el) => el.classList.remove('is-on'));
        playing = true;
        const ticket = ++run;

        try {
            if (to > from) {
                // Forward: the real film, from here to the next chapter
                await seek(forward, from);
                show(forward);
                const played = await playUntil(forward, to);
                if (played === null) return;
                if (!played) await jump(target);
                // Start fetching the reversed copy once someone has stepped forward
                if (!reverse.dataset.loading) {
                    reverse.dataset.loading = '1';
                    reverse.preload = 'auto';
                    loadInto(reverse, reverseSrc);
                }
            } else if (reverse.readyState >= 3) {
                // Backward: the reversed film, from the matching moment
                await seek(reverse, duration - from);
                show(reverse);
                const played = await playUntil(reverse, duration - to);
                if (played === null) return;
                await seek(forward, played ? Math.max(0, duration - reverse.currentTime) : to);
                show(forward);
            } else {
                await jump(target);
            }
        } finally {
            if (ticket === run) {
                playing = false;
                paintBar();
                showCaption(target);
            }
        }
    };

    // Skip intro: stop the film on its last chapter (the search) and scroll
    // past the hero to the rest of the page.
    const skip = () => {
        run++;
        cancelPlay?.();
        forward.pause();
        reverse.pause();
        playing = false;
        const last = captions.length - 1;
        chapter = last;
        showCaption(last);
        if (duration) seek(forward, times[last]).then(() => {
            show(forward);
            paintBar();
        });
        const header = document.querySelector('.site-header')?.offsetHeight ?? 0;
        window.scrollTo({ top: root.offsetTop + root.offsetHeight - header, behavior: 'smooth' });
        document.querySelector('#makes-title')?.focus({ preventScroll: true });
    };
    $('[data-cine-skip]', root)?.addEventListener('click', skip);

    // The film only takes over the scroll while the hero sits at the top of the page
    const atHero = () => window.scrollY <= root.offsetTop + 2 && root.getBoundingClientRect().bottom > window.innerHeight * 0.5;
    const wantsStep = (down) => atHero() && (down ? chapter < captions.length - 1 : chapter > 0);

    window.addEventListener(
        'wheel',
        (event) => {
            if (Math.abs(event.deltaY) < 4) return;
            const down = event.deltaY > 0;
            if (!wantsStep(down)) return;
            event.preventDefault();
            const now = performance.now();
            const quiet = now - lastWheel > QUIET_MS;
            lastWheel = now;
            if (!playing && quiet) goTo(chapter + (down ? 1 : -1));
        },
        { passive: false },
    );

    let touchY = null;
    root.addEventListener('touchstart', (e) => (touchY = e.touches[0].clientY), { passive: true });
    root.addEventListener(
        'touchmove',
        (event) => {
            if (touchY === null) return;
            const dy = touchY - event.touches[0].clientY;
            const down = dy > 0;
            if (!wantsStep(down)) return;
            event.preventDefault();
            if (Math.abs(dy) > 40) {
                touchY = null;
                goTo(chapter + (down ? 1 : -1));
            }
        },
        { passive: false },
    );
    root.addEventListener('touchend', () => (touchY = null));

    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && atHero() && chapter < captions.length - 1) {
            skip();
            return;
        }
        if (event.target.closest('input, select, textarea, button, a')) return;
        const down = ['ArrowDown', 'PageDown', ' '].includes(event.key);
        const up = ['ArrowUp', 'PageUp'].includes(event.key);
        if ((!down && !up) || !wantsStep(down)) return;
        event.preventDefault();
        goTo(chapter + (down ? 1 : -1));
    });

    dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));

    showCaption(0);
}
