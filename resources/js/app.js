import './bootstrap';
import { initIntroHero } from './intro-hero';
import { initRaceDeck } from './race-deck';
import { initInteractiveTicket } from './interactive-ticket';

function initVeloshaApp() {
    initIntroHero();
    initRaceDeck();
    initInteractiveTicket();
}

// Helper to reset any disabled form buttons
export function resetDisabledFormButtons() {
    document.querySelectorAll('button[data-velo-submitting]').forEach((btn) => {
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-not-allowed');
        delete btn.dataset.veloSubmitting;
        if (btn.dataset.veloOriginalText) {
            const span = btn.querySelector('span');
            if (span) span.textContent = btn.dataset.veloOriginalText;
            delete btn.dataset.veloOriginalText;
        }
    });
}

// Global Anti-Double-Submit Guard - ONLY for standard non-Livewire forms
if (typeof document !== 'undefined') {
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!form || !(form instanceof HTMLFormElement)) return;
        if (e.defaultPrevented) return;

        // Skip Livewire forms completely - Livewire handles its own wire:loading
        const isLivewireForm = form.hasAttribute('wire:submit') ||
            Array.from(form.attributes).some(attr => attr.name.startsWith('wire:submit'));
        if (isLivewireForm) return;

        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (!submitBtn || submitBtn.disabled) return;

        setTimeout(() => {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
            submitBtn.dataset.veloSubmitting = 'true';
            const textSpan = submitBtn.querySelector('span');
            if (textSpan && !submitBtn.querySelector('.animate-spin')) {
                submitBtn.dataset.veloOriginalText = textSpan.textContent;
                textSpan.textContent = 'Memproses...';
            }
        }, 10);
    });

    document.addEventListener('livewire:navigated', resetDisabledFormButtons);
    document.addEventListener('livewire:init', () => {
        if (window.Livewire) {
            Livewire.hook('morph.updated', resetDisabledFormButtons);
            Livewire.hook('commit', ({ succeed, fail }) => {
                succeed(() => resetDisabledFormButtons());
                fail(() => resetDisabledFormButtons());
            });
        }
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
