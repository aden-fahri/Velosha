export function initInteractiveDeck() {
    const deck = document.getElementById('deck');
    if (!deck) return;

    if (deck.dataset.initialized === 'true') {
        reindexCards(deck);
        return;
    }
    deck.dataset.initialized = 'true';

    const RM = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let mode = 'stacked';
    let autoSpread = false;
    let drag = null;

    const cards = () => [...deck.querySelectorAll('.card')];
    const vis = () => cards().filter(c => !c.classList.contains('off'));

    function reindexCards(deckEl) {
        const v = vis();
        v.forEach((c, k) => {
            c.style.setProperty('--k', k);
            c.style.zIndex = k + 1;
        });
        deckEl.style.setProperty('--m', (v.length - 1) / 2);
        deckEl.style.setProperty('--n', v.length);
    }

    function flip(fn) {
        const before = new Map(vis().map(c => [c, {
            x: c.offsetLeft,
            y: c.offsetTop,
            r: getComputedStyle(c).rotate
        }]));
        fn();
        reindexCards(deck);
        vis().forEach((c, i) => {
            const b = before.get(c);
            const r1 = getComputedStyle(c).rotate;
            if (RM) return;
            if (b) {
                c.animate([
                    { translate: `${b.x - c.offsetLeft}px ${b.y - c.offsetTop}px`, rotate: b.r },
                    { translate: '0px 0px', rotate: r1 }
                ], {
                    duration: 650,
                    delay: i * 45,
                    easing: 'cubic-bezier(.2, .9, .2, 1)',
                    fill: 'backwards'
                });
            } else {
                c.animate([
                    { opacity: 0, transform: 'scale(0.6)' },
                    { opacity: 1, transform: 'scale(1)' }
                ], {
                    duration: 450,
                    delay: i * 45,
                    easing: 'ease-out',
                    fill: 'backwards'
                });
            }
        });
    }

    const modeBtn = document.getElementById('deck-mode-btn');

    function setMode(m) {
        flip(() => {
            mode = m;
            deck.className = 'deck ' + m;
            if (modeBtn) {
                modeBtn.textContent = m === 'spread' ? 'Tumpuk kartu' : 'Sebar kartu';
            }
        });
    }

    if (modeBtn) {
        modeBtn.addEventListener('click', () => {
            autoSpread = true;
            setMode(mode === 'spread' ? 'stacked' : 'spread');
        });
    }

    reindexCards(deck);

    // Auto spread on viewport intersection
    const observer = new IntersectionObserver(([entry], obs) => {
        if (entry.isIntersecting && !autoSpread) {
            autoSpread = true;
            setTimeout(() => setMode('spread'), 350);
            obs.disconnect();
        }
    }, { threshold: 0.25 });
    observer.observe(deck);

    // 3D Tilt in spread mode
    deck.addEventListener('pointermove', e => {
        const c = e.target.closest('.card');
        if (!c || mode !== 'spread' || e.pointerType === 'touch') return;
        const r = c.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - 0.5;
        const y = (e.clientY - r.top) / r.height - 0.5;
        const inner = c.querySelector('.in');
        if (inner) {
            inner.style.transform = `perspective(800px) rotateY(${x * 14}deg) rotateX(${-y * 12}deg) translateY(-8px)`;
        }
    });

    deck.addEventListener('pointerout', e => {
        const c = e.target.closest('.card');
        if (c) {
            const inner = c.querySelector('.in');
            if (inner) inner.style.transform = '';
        }
    });

    // Drag top card when stacked
    deck.addEventListener('pointerdown', e => {
        const c = e.target.closest('.card');
        const v = vis();
        if (mode !== 'stacked' || !c || c !== v[v.length - 1] || e.target.closest('a, button')) return;
        drag = { c, x: e.clientX, y: e.clientY };
        c.setPointerCapture(e.pointerId);
        c.style.transition = 'none';
    });

    deck.addEventListener('pointermove', e => {
        if (!drag) return;
        const dx = e.clientX - drag.x;
        const dy = (e.clientY - drag.y) * 0.4;
        drag.c.style.translate = `${dx}px ${dy}px`;
        drag.c.style.rotate = `${dx / 18}deg`;
    });

    const endDrag = e => {
        if (!drag) return;
        const { c } = drag;
        const dx = e.clientX - drag.x;
        drag = null;
        c.style.translate = '';
        c.style.rotate = '';
        if (Math.abs(dx) > 110) {
            const s = Math.sign(dx);
            const a = c.animate([
                { translate: `${dx}px 0px`, opacity: 1 },
                { translate: `${s * 700}px 40px`, opacity: 0 }
            ], { duration: 260, easing: 'ease-in' });
            a.onfinish = () => flip(() => deck.prepend(c));
        } else {
            c.animate([
                { translate: `${dx}px 0px` },
                { translate: '0px 0px' }
            ], { duration: 420, easing: 'cubic-bezier(.3, 1.6, .5, 1)' });
        }
    };

    deck.addEventListener('pointerup', endDrag);
    deck.addEventListener('pointercancel', endDrag);
}
