export function initInteractiveTicket() {
    const area = document.getElementById('tkarea');
    const tk = document.getElementById('ticket');
    const stub = document.getElementById('stub');
    const qr = document.getElementById('qr');
    const msg = document.getElementById('tkmsg');
    const badge = document.getElementById('tk-badge') || document.getElementById('badge');
    const scanBtn = document.getElementById('scan');
    const resetBtn = document.getElementById('reset');

    if (!area || !tk || !stub) return;

    if (area.dataset.initialized === 'true') return;
    area.dataset.initialized = 'true';

    const RM = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let scanned = false;
    let torn = false;

    // Generate crisp QR matrix path if #qrp exists
    const qrp = document.getElementById('qrp');
    if (qrp && !qrp.getAttribute('d')) {
        let seed = 147;
        const rnd = () => (seed = (seed * 1103515245 + 12345) & 0x7fffffff) / 0x7fffffff;
        const N = 25;
        const fin = (x, y) => (x < 8 && y < 8) || (x > 16 && y < 8) || (x < 8 && y > 16);
        let d = '';
        for (let y = 0; y < N; y++) {
            for (let x = 0; x < N; x++) {
                let on;
                if (fin(x, y)) {
                    const lx = x > 16 ? x - 17 : x;
                    const ly = y > 16 ? y - 17 : y;
                    on = lx === 0 || lx === 6 || ly === 0 || ly === 6 || (lx > 1 && lx < 5 && ly > 1 && ly < 5);
                    if (lx === 7 || ly === 7) on = false;
                } else {
                    on = rnd() > 0.52;
                }
                if (on) d += `M${x} ${y}h1v1h-1z`;
            }
        }
        qrp.setAttribute('d', d);
    }

    // 3D tilt & glare on ticket container
    area.addEventListener('pointermove', e => {
        const r = tk.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width;
        const y = (e.clientY - r.top) / r.height;
        tk.style.setProperty('--ry', `${(x - 0.5) * 16}deg`);
        tk.style.setProperty('--rx', `${-(y - 0.5) * 12}deg`);
        tk.style.setProperty('--gx', `${x * 100}%`);
        tk.style.setProperty('--gy', `${y * 100}%`);
        tk.style.setProperty('--go', 0.9);
    });

    area.addEventListener('pointerleave', () => {
        tk.style.setProperty('--rx', '0deg');
        tk.style.setProperty('--ry', '0deg');
        tk.style.setProperty('--go', 0);
    });

    // Pindai QR
    if (scanBtn) {
        scanBtn.addEventListener('click', () => {
            if (scanned || torn) return;
            if (qr) qr.classList.add('scanning');
            if (msg) msg.textContent = 'Memindai…';
            setTimeout(() => {
                if (qr) {
                    qr.classList.remove('scanning');
                    qr.classList.add('ok');
                }
                if (badge) badge.style.display = 'inline-flex';
                scanned = true;
                if (msg) msg.textContent = 'Valid. Sekarang tarik potongan kanan ke bawah untuk menyobek.';
            }, RM ? 0 : 1150);
        });
    }

    // Tear logic
    function tear() {
        torn = true;
        tk.classList.add('torn');
        if (msg) msg.textContent = 'Tiket tervalidasi. Sampai jumpa di garis start!';
        stub.style.transition = 'transform .7s cubic-bezier(.5, 0, 1, .6), opacity .7s';
        stub.style.transform = 'translate(30px, 420px) rotate(55deg)';
        stub.style.opacity = '0';
    }

    let sy = null;
    stub.addEventListener('pointerdown', e => {
        if (torn) return;
        if (!scanned) {
            if (msg) msg.textContent = 'Pindai QR dulu sebelum menyobek.';
            return;
        }
        sy = e.clientY;
        stub.setPointerCapture(e.pointerId);
        stub.style.transition = 'none';
    });

    stub.addEventListener('pointermove', e => {
        if (sy === null) return;
        const a = Math.max(0, Math.min(50, (e.clientY - sy) / 3.2));
        stub.style.transform = `rotate(${a}deg)`;
        stub.dataset.a = a;
    });

    const up = () => {
        if (sy === null) return;
        sy = null;
        if (+stub.dataset.a > 22) {
            tear();
        } else {
            stub.style.transition = 'transform .45s cubic-bezier(.3, 1.7, .5, 1)';
            stub.style.transform = '';
        }
        stub.dataset.a = 0;
    };

    stub.addEventListener('pointerup', up);
    stub.addEventListener('pointercancel', up);

    // Keyboard support: Enter key tears ticket if scanned
    stub.addEventListener('keydown', e => {
        if (e.key === 'Enter' && scanned && !torn) {
            e.preventDefault();
            tear();
        }
    });

    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            torn = false;
            scanned = false;
            tk.classList.remove('torn');
            if (qr) qr.classList.remove('ok', 'scanning');
            if (badge) badge.style.display = 'none';
            stub.style.transition = 'none';
            stub.style.transform = '';
            stub.style.opacity = '';
            if (msg) msg.textContent = 'Pindai dulu, lalu tarik potongan kanan ke bawah.';
        });
    }
}

if (typeof window !== 'undefined') {
    window.initInteractiveTicket = initInteractiveTicket;
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initInteractiveTicket);
    } else {
        initInteractiveTicket();
    }
    document.addEventListener('livewire:navigated', initInteractiveTicket);
    if (window.Livewire) {
        window.Livewire.hook('morph.updated', initInteractiveTicket);
    }
}
