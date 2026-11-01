/**
 * Velosha — Intro to Hero Continuous Cinematic Choreography
 * 1:1 Motion sequence based on velosha-intro-hero.html & Gambar 2-5
 */

export function initIntroHero() {
    const hero = document.getElementById('hero-section') || document.getElementById('hero');
    if (!hero) return null;

    const RM = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
    const wait = ms => new Promise(r => setTimeout(r, ms));
    const A = (el, kf, o = {}) => {
        if (!el) return Promise.resolve();
        return el.animate(kf, { fill: 'both', ...o }).finished.catch(() => {});
    };

    const intro = document.getElementById('intro-stage') || document.getElementById('intro');
    const flood = document.getElementById('flood');
    const ib = document.getElementById('ibike');
    const road = document.getElementById('road');
    const rider = document.getElementById('hero-rider') || document.getElementById('rider');
    const skipBtn = document.getElementById('skip-intro') || document.getElementById('skip');
    const replayBtn = document.getElementById('replay-hero') || document.getElementById('replay-intro') || document.getElementById('replay');
    const badge = document.getElementById('intro-badge');
    const logo = document.getElementById('intro-logo');
    const fill = document.getElementById('fill');
    const pct = document.getElementById('pct');

    let run = 0;
    let idle = null;
    const ease = {
        io: t => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2,
        in: t => t * t * t
    };

    // Preload hero rider image so it never flashes or stutters
    let heroImageLoaded = false;
    const preloadImg = new Image();
    preloadImg.src = '/images/hero/rider_final.png';
    if (preloadImg.complete) {
        heroImageLoaded = true;
    } else {
        preloadImg.onload = () => { heroImageLoaded = true; };
    }

    function reset() {
        if (idle) {
            cancelAnimationFrame(idle);
            idle = null;
        }
        window.onpointermove = null;
        hero.querySelectorAll('*').forEach(e => {
            if (typeof e.getAnimations === 'function') {
                e.getAnimations().forEach(a => a.cancel());
            }
        });
        document.querySelectorAll('.cf').forEach(e => e.remove());

        if (skipBtn) skipBtn.style.display = '';
        const stage = hero.querySelector('.stage');
        if (stage) {
            stage.classList.add('off');
            stage.classList.remove('on');
        }
        if (flood) flood.style.display = 'none';
        if (intro) intro.style.display = 'grid';
        if (ib) ib.style.display = 'none';
        if (fill) fill.style.width = '0%';
        if (pct) pct.textContent = '0%';
        if (badge) badge.style.transform = '';
        if (logo) logo.style.transform = '';
        if (rider) rider.style.transform = '';
        if (road && road.firstElementChild) road.firstElementChild.style.backgroundPositionX = '0px';
        document.querySelectorAll('#ibike .sl').forEach(s => s.style.transform = 'scaleX(0)');
    }

    function finish() {
        run++;
        if (idle) {
            cancelAnimationFrame(idle);
            idle = null;
        }
        window.onpointermove = null;
        hero.querySelectorAll('*').forEach(e => {
            if (typeof e.getAnimations === 'function') {
                e.getAnimations().forEach(a => a.cancel());
            }
        });
        document.querySelectorAll('.cf').forEach(e => e.remove());

        if (intro) intro.style.display = 'none';
        if (flood) flood.style.display = 'none';
        if (ib) ib.style.display = 'none';
        if (skipBtn) skipBtn.style.display = 'none';
        
        const stage = hero.querySelector('.stage');
        if (stage) {
            stage.classList.remove('off');
            stage.classList.add('on');
        }

        document.body.style.overflow = '';
        try { sessionStorage.setItem('velosha_intro_seen', 'true'); } catch (e) {}
        startIdle();
    }

    function startIdle() {
        if (!rider) return;
        let tx = 0, ty = 0, mx = 0, my = 0;
        window.onpointermove = e => {
            const r = hero.getBoundingClientRect();
            tx = ((e.clientX - r.left) / r.width - 0.5) * 2;
            ty = ((e.clientY - r.top) / r.height - 0.5) * 2;
        };
        const loop = t => {
            mx += (tx - mx) * 0.07;
            my += (ty - my) * 0.07;
            rider.style.transform = `translate3d(${mx * -16}px, ${my * -8}px, 0) rotate(${mx * -1.5}deg)`;
            idle = requestAnimationFrame(loop);
        };
        idle = requestAnimationFrame(loop);
    }

    async function play() {
        if (RM) return finish();
        const id = ++run;
        const live = () => id === run;
        reset();
        document.body.style.overflow = 'hidden';
        window.scrollTo(0, 0);

        if (!road || !ib) {
            return finish();
        }

        const H = hero.getBoundingClientRect();
        const R = road.getBoundingClientRect();

        /* 1 · Logo dan badge stempel (0 – 0.6s) */
        A(badge, [{ opacity: 0, translate: '0 -24px', scale: 0.8 }, { opacity: 1, translate: '0 0', scale: 1 }], { duration: 480, easing: 'cubic-bezier(.2,.9,.2,1)' });
        await wait(140);
        if (!live()) return;

        A(logo, [{ opacity: 0, scale: 1.7, rotate: '-3deg' }, { opacity: 1, scale: 1, rotate: '0deg' }], { duration: 560, easing: 'cubic-bezier(.16,1.4,.4,1)' });
        await wait(420);
        if (!live()) return;

        /* 2 · Sepeda menyusuri jalan = progres muat (0.6 – 2.6s) */
        const bw = clamp(R.width * 0.28, 110, 160);
        const bh = bw * 698 / 780;
        hero.style.setProperty('--bw', bw + 'px');
        ib.style.width = bw + 'px';
        ib.style.display = 'block';

        // Posisi y agar ban sepeda tepat menyentuh permukaan aspal jalan
        const ty = R.top - bh + 6;
        const x0 = R.left - bw * 0.38;
        const x1 = R.right - bw * 0.88;

        let dist = 0, px = x0;
        const place = x => {
            px = x;
            ib.style.transform = `translate(${x}px, ${ty}px)`;
        };
        place(x0);

        await new Promise(done => {
            const T = 2000, s = performance.now();
            const f = t => {
                if (!live()) return;
                let simP = clamp((t - s) / T, 0, 1);
                // Tunggu decode gambar hero bila masih proses
                if (!heroImageLoaded && simP > 0.94) {
                    simP = 0.94;
                }
                const e = 0.15 * simP + 0.85 * ease.io(simP);
                const x = x0 + (x1 - x0) * e;
                dist += Math.abs(x - px);
                place(x);
                if (fill) fill.style.width = (e * 100) + '%';
                if (pct) pct.textContent = Math.round(e * 100) + '%';
                if (road.firstElementChild) road.firstElementChild.style.backgroundPositionX = -dist + 'px';

                if (simP < 1) {
                    requestAnimationFrame(f);
                } else {
                    done();
                }
            };
            requestAnimationFrame(f);
        });
        if (!live()) return;

        /* 3 · Garis finis: konfeti + banjir mentega dari titik finis */
        const fx = R.right - 12;
        const fy = R.top + 15;
        const colors = ['#DE7119', '#F4BE3F', '#1F6F4A', '#9B3D5A', '#FFFDF8'];
        colors.flatMap(c => [0, 1, 2, 3]).forEach((c, i) => {
            const d = document.createElement('i');
            d.className = 'cf';
            d.style.cssText = `position:fixed;left:${fx}px;top:${fy}px;background:${colors[i % 5]};border-radius:${i % 2 ? '50%' : '3px'};z-index:99999;width:12px;height:12px;border:2.5px solid #10282E;pointer-events:none;`;
            document.body.appendChild(d);
            const a = Math.random() * Math.PI - Math.PI;
            const r = 70 + Math.random() * 140;
            d.animate([
                { translate: '0 0', rotate: '0deg', scale: 1 },
                { translate: `${Math.cos(a) * r}px ${Math.sin(a) * r - 40}px`, rotate: `${Math.random() * 540}deg`, scale: 0.3 }
            ], { duration: 900, easing: 'cubic-bezier(.1,.8,.3,1)', fill: 'forwards' })
            .finished.then(() => d.remove()).catch(() => {});
        });

        const pc = (fx / window.innerWidth * 100).toFixed(1);
        if (flood) flood.style.display = 'block';
        const fl = flood ? flood.animate([
            { clipPath: `polygon(${pc}% 0, ${pc}% 0, ${pc}% 100%, ${pc}% 100%)` },
            { clipPath: 'polygon(-35% 0, 135% 0, 120% 100%, -50% 100%)' }
        ], { duration: 900, easing: 'cubic-bezier(.6,0,.25,1)', fill: 'forwards' }) : { finished: Promise.resolve() };

        // Sepeda terus meluncur keluar kanan dengan garis kecepatan
        const ex = new Promise(done => {
            const T = 900, s = performance.now(), a = px;
            const f = t => {
                if (!live()) return;
                const p = clamp((t - s) / T, 0, 1);
                const x = a + (window.innerWidth + bw * 1.4 - a) * ease.in(p);
                place(x);
                document.querySelectorAll('#ibike .sl').forEach(l => l.style.transform = `scaleX(${clamp(p * 3, 0, 1)})`);
                if (p < 1) requestAnimationFrame(f); else done();
            };
            requestAnimationFrame(f);
        });

        await Promise.all([fl.finished, ex]);
        if (!live()) return;
        ib.style.display = 'none';
        if (intro) intro.style.display = 'none';

        /* 4 · Tirai mentega flood terangkat; stage hero mendarat serentak dengan CSS 1:1 */
        const stage = hero.querySelector('.stage');
        if (stage) {
            stage.classList.remove('off');
            stage.classList.add('on');
        }

        if (flood) {
            await flood.animate([
                { transform: 'translateY(0%)', opacity: 1 },
                { transform: 'translateY(-100%)', opacity: 0.95 }
            ], { duration: 650, easing: 'cubic-bezier(.7, 0, .2, 1)', fill: 'forwards' }).finished.catch(() => {});
            flood.style.display = 'none';
        }

        if (skipBtn) skipBtn.style.display = 'none';
        try { sessionStorage.setItem('velosha_intro_seen', 'true'); } catch (e) {}

        await wait(900);
        if (!live()) return;
        document.body.style.overflow = '';
        startIdle();
    }

    // Bind interaction buttons
    if (skipBtn) skipBtn.onclick = finish;
    if (replayBtn) replayBtn.onclick = () => { window.scrollTo({ top: 0 }); play(); };
    const heroReplay = document.getElementById('replay-hero');
    if (heroReplay) heroReplay.onclick = () => { window.scrollTo({ top: 0 }); play(); };

    // Initial check: if already seen in session or reduced-motion is active
    if (sessionStorage.getItem('velosha_intro_seen') === 'true' || RM) {
        finish();
    } else {
        play();
    }

    return { play, finish, reset };
}

// Auto-init on DOM Ready
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initIntroHero);
    } else {
        initIntroHero();
    }
    document.addEventListener('livewire:navigated', () => {
        if (document.getElementById('hero-section')) {
            initIntroHero();
        }
    });
}
