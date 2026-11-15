/**
 * Velosha — Event Cards Deck: Stacked on view -> Fan out smoothly on scroll
 * 1:1 Motion choreography: berkumpul di tengah lalu menyebar saat di-scroll
 */

export function initRaceDeck() {
    const deck = document.getElementById('race-cards-grid');
    if (!deck) return null;

    const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
    const easeIo = t => t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;

    const vis = () => [...deck.querySelectorAll('.race-card:not(.off)')];
    let lastE = 0;

    function scrub() {
        const v = vis();
        if (!v.length) return;

        // Mobile & Tablet (< 1024px): reset transforms so native horizontal track & < > arrows work cleanly
        if (window.innerWidth < 1024) {
            v.forEach(c => {
                c.style.translate = '';
                c.style.rotate = '';
                c.style.scale = '';
                c.style.zIndex = '';
            });
            return;
        }

        const r = deck.getBoundingClientRect();
        // Calculate progress: 0 when entering viewport (stacked), 1 when fully scrolled into center (spread)
        const e = easeIo(clamp((window.innerHeight * 0.88 - r.top) / (window.innerHeight * 0.50), 0, 1));
        lastE = e;

        const m = (v.length - 1) / 2;
        const W = deck.offsetWidth;
        const c0 = v[0];
        if (!c0) return;
        const y0 = c0.offsetTop + c0.offsetHeight / 2;

        v.forEach((c, k) => {
            const cx = c.offsetLeft + c.offsetWidth / 2;
            const cy = c.offsetTop + c.offsetHeight / 2;
            const s = 1 - e; // 1 = bertumpuk di tengah (berkumpul), 0 = menyebar ke posisi masing-masing

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

    window.removeEventListener('scroll', req);
    window.removeEventListener('resize', req);
    window.addEventListener('scroll', req, { passive: true });
    window.addEventListener('resize', req, { passive: true });

    // 3D Tilt on Hover when cards are spread out (e > 0.90) on desktop
    const handleMove = ev => {
        if (window.innerWidth < 1024) return;
        const c = ev.target.closest('.race-card');
        if (!c || lastE < 0.90 || ev.pointerType === 'touch') return;
        const inner = c.querySelector('.race-card-inner') || c;
        const r = c.getBoundingClientRect();
        const x = (ev.clientX - r.left) / r.width - 0.5;
        const y = (ev.clientY - r.top) / r.height - 0.5;
        inner.style.transform = `rotateY(${x * 12}deg) rotateX(${-y * 10}deg) translateY(-8px)`;
        inner.style.boxShadow = '10px 10px 0 #10282E';
    };

    const handleOut = ev => {
        const c = ev.target.closest('.race-card');
        if (c) {
            const inner = c.querySelector('.race-card-inner') || c;
            inner.style.transform = '';
            inner.style.boxShadow = '';
        }
    };

    deck.removeEventListener('pointermove', handleMove);
    deck.removeEventListener('pointerout', handleOut);
    deck.addEventListener('pointermove', handleMove);
    deck.addEventListener('pointerout', handleOut);

    // Initial scrub check
    scrub();

    return { scrub };
}

// Auto-init fallback
if (typeof window !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initRaceDeck());
    } else {
        initRaceDeck();
    }
    document.addEventListener('livewire:navigated', () => initRaceDeck());
}
