/**
 * Velosha — Event Cards Deck: Stacked on view -> Fan out smoothly on scroll
 * 1:1 Motion choreography based on velosha-intro-hero.html
 */

export function initRaceDeck() {
    const deck = document.getElementById('race-cards-grid');
    if (!deck) return null;

    const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
    const easeIo = t => t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;

    const vis = () => [...deck.querySelectorAll('.race-card:not(.off)')];
    let lastE = 0;

    function scrub() {
        const r = deck.getBoundingClientRect();
        const e = easeIo(clamp((window.innerHeight * 0.84 - r.top) / (window.innerHeight * 0.48), 0, 1));
        lastE = e;
        const v = vis();
        if (!v.length) return;

        const m = (v.length - 1) / 2;
        const W = deck.offsetWidth;
        const c0 = v[0];
        if (!c0) return;
        const y0 = c0.offsetTop + c0.offsetHeight / 2;

        v.forEach((c, k) => {
            const cx = c.offsetLeft + c.offsetWidth / 2;
            const cy = c.offsetTop + c.offsetHeight / 2;
            const s = 1 - e; // 1 = bertumpuk, 0 = menyebar ke kolom grid

            c.style.translate = `${(W / 2 - cx) * s + (k - m) * 16 * s}px ${(y0 - cy) * s + (k - m) * -8 * s}px`;
            c.style.rotate = `${(k - m) * 7.5 * s}deg`;
            c.style.scale = 1 - 0.07 * s;
            c.style.zIndex = k + 1;
        });
    }

    let tick = 0;
    const req = () => {
        if (!tick) {
            tick = requestAnimationFrame(() => {
                tick = 0;
                scrub();
            });
        }
    };

    window.addEventListener('scroll', req, { passive: true });
    window.addEventListener('resize', req, { passive: true });

    // 3D Tilt on Hover when cards are spread out (e > 0.92)
    deck.addEventListener('pointermove', ev => {
        const c = ev.target.closest('.race-card');
        if (!c || lastE < 0.92 || ev.pointerType === 'touch') return;
        const inner = c.querySelector('.race-card-inner') || c;
        const r = c.getBoundingClientRect();
        const x = (ev.clientX - r.left) / r.width - 0.5;
        const y = (ev.clientY - r.top) / r.height - 0.5;
        inner.style.transform = `rotateY(${x * 14}deg) rotateX(${-y * 12}deg) translateY(-8px)`;
        inner.style.boxShadow = '12px 12px 0 #10282E';
    });

    deck.addEventListener('pointerout', ev => {
        const c = ev.target.closest('.race-card');
        if (c) {
            const inner = c.querySelector('.race-card-inner') || c;
            inner.style.transform = '';
            inner.style.boxShadow = '';
        }
    });

    // Run immediately
    scrub();

    return { scrub };
}
