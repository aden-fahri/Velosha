import './bootstrap';
import { initInteractiveDeck } from './interactive-cards';
import { initInteractiveTicket } from './interactive-ticket';

function initVeloshaInteractive() {
    initInteractiveDeck();
    initInteractiveTicket();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVeloshaInteractive);
} else {
    initVeloshaInteractive();
}

document.addEventListener('livewire:navigated', initVeloshaInteractive);

if (window.Livewire) {
    window.Livewire.hook('morph.updated', () => {
        initInteractiveDeck();
        initInteractiveTicket();
    });
}
