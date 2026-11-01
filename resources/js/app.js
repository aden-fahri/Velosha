import './bootstrap';
import { initIntroHero } from './intro-hero';
import { initRaceDeck } from './race-deck';
import { initInteractiveTicket } from './interactive-ticket';

function initVeloshaApp() {
    initIntroHero();
    initRaceDeck();
    initInteractiveTicket();
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

if (window.Livewire) {
    window.Livewire.hook('morph.updated', () => {
        initRaceDeck();
        initInteractiveTicket();
    });
}
