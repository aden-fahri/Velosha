import './bootstrap';
import { initIntroHero } from './intro-hero';
import { initRaceDeck } from './race-deck';
import { initInteractiveTicket } from './interactive-ticket';

function initVeloshaApp() {
    initIntroHero();
    initRaceDeck();
    initInteractiveTicket();
}

// Global Anti-Double-Submit Guard untuk Form Standar
if (typeof document !== 'undefined') {
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!form || !(form instanceof HTMLFormElement)) return;
        if (e.defaultPrevented) return;

        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (!submitBtn || submitBtn.disabled) return;

        setTimeout(() => {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
            const textSpan = submitBtn.querySelector('span');
            if (textSpan && !submitBtn.querySelector('.animate-spin')) {
                textSpan.textContent = 'Memproses...';
            }
        }, 10);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVeloshaApp);
} else {
    initVeloshaApp();
}

document.addEventListener('livewire:navigated', () => {
    initRaceDeck();
    initInteractiveTicket();
});

document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', () => {
        initRaceDeck();
        initInteractiveTicket();
    });
});

if (window.Livewire) {
    window.Livewire.hook('morph.updated', () => {
        initRaceDeck();
        initInteractiveTicket();
    });
}
