// ONE Alpine instance: the one bundled with Livewire. Never import "alpinejs".
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import intersect from '@alpinejs/intersect';
import { consentStore, loadConsentedTags } from './consent';
import { ibanMask, digitsMask } from './masks';

Alpine.plugin(intersect);

Alpine.store('consent', consentStore());
Alpine.data('ibanMask', ibanMask);
Alpine.data('digitsMask', digitsMask);

// Sticky header: border after 8px of scroll.
Alpine.data('stickyHeader', () => ({
    scrolled: false,
    init() {
        const onScroll = () => { this.scrolled = window.scrollY > 8; };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    },
}));

// Home calculator: amount + term sliders, prefilled link to the form (no personal data).
Alpine.data('loanCalc', (c) => ({
    ...c,
    fmt: new Intl.NumberFormat(c.locale, { style: 'currency', currency: c.currency, maximumFractionDigits: 0, useGrouping: 'always' }),
    money(v) { return this.fmt.format(v); },
    fill(v, min, max) { return `--fill:${((v - min) / (max - min)) * 100}%`; },
    cta() { return this.cta_.replace('__AMOUNT__', this.money(this.amount)); },
    get cta_() { return c.cta; },
    href() { return `${c.url}?amount=${this.amount}&term=${this.term}`; },
}));

// Move focus to the step heading after a Livewire step change (a11y).
Livewire.on('step-changed', () => {
    requestAnimationFrame(() => {
        const heading = document.getElementById('step-heading');
        if (heading) {
            heading.focus({ preventScroll: true });
            heading.scrollIntoView({ block: 'start', behavior: 'smooth' });
        }
    });
});

Livewire.start();

// Third-party tags, only with consent (and only when IDs are configured).
loadConsentedTags(Alpine.store('consent'));
document.addEventListener('pf:consent-changed', () => loadConsentedTags(Alpine.store('consent')));
