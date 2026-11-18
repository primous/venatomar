/**
 * Cookie consent. Choice stored in the "pf_consent" cookie (JSON, 365 days)
 * together with the cookie-policy version. No third-party script is loaded
 * before an explicit "yes" for its category.
 */
const COOKIE = 'pf_consent';
const MAX_AGE = 365 * 24 * 60 * 60;

function readConfig() {
    const el = document.getElementById('pf-config');
    try {
        return el ? JSON.parse(el.textContent) : {};
    } catch {
        return {};
    }
}

function readCookie() {
    const match = document.cookie.split('; ').find((c) => c.startsWith(COOKIE + '='));
    if (!match) return null;
    try {
        return JSON.parse(decodeURIComponent(match.slice(COOKIE.length + 1)));
    } catch {
        return null;
    }
}

export function consentStore() {
    const config = readConfig();
    const saved = readCookie();
    const valid = saved && saved.v === config.policyVersion;

    return {
        config,
        decided: Boolean(valid),
        panelOpen: false,
        statistics: valid ? saved.statistics === true : false,
        marketing: valid ? saved.marketing === true : false,

        open() {
            this.panelOpen = true;
        },
        save(statistics, marketing) {
            this.statistics = Boolean(statistics);
            this.marketing = Boolean(marketing);
            const value = encodeURIComponent(JSON.stringify({
                v: this.config.policyVersion,
                necessary: true,
                statistics: this.statistics,
                marketing: this.marketing,
                at: new Date().toISOString(),
            }));
            const secure = location.protocol === 'https:' ? '; Secure' : '';
            document.cookie = `${COOKIE}=${value}; Max-Age=${MAX_AGE}; Path=/; SameSite=Lax${secure}`;
            this.decided = true;
            this.panelOpen = false;
            document.dispatchEvent(new CustomEvent('pf:consent-changed'));
        },
        acceptAll() { this.save(true, true); },
        rejectAll() { this.save(false, false); },
        saveSelection() { this.save(this.statistics, this.marketing); },
    };
}

const loaded = new Set();

function injectScript(src) {
    const s = document.createElement('script');
    s.async = true;
    s.src = src;
    document.head.appendChild(s);
}

export function loadConsentedTags(store) {
    const ids = store.config.tracking || {};

    // Google tag (GA4 = statistics, Ads = marketing). IP anonymised, no ad
    // personalisation signals without marketing consent.
    const wantGa = store.statistics && ids.ga4;
    const wantAds = store.marketing && ids.ads;
    if ((wantGa || wantAds) && !loaded.has('gtag')) {
        loaded.add('gtag');
        window.dataLayer = window.dataLayer || [];
        window.gtag = function () { window.dataLayer.push(arguments); };
        window.gtag('consent', 'default', {
            analytics_storage: store.statistics ? 'granted' : 'denied',
            ad_storage: store.marketing ? 'granted' : 'denied',
            ad_user_data: store.marketing ? 'granted' : 'denied',
            ad_personalization: store.marketing ? 'granted' : 'denied',
        });
        window.gtag('js', new Date());
        injectScript('https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(wantGa ? ids.ga4 : ids.ads));
    }
    if (wantGa && !loaded.has('ga4')) {
        loaded.add('ga4');
        // page_location without query string: never send personal data.
        window.gtag('config', ids.ga4, { page_location: location.origin + location.pathname });
    }
    if (wantAds && !loaded.has('ads')) {
        loaded.add('ads');
        window.gtag('config', ids.ads);
    }

    if (store.marketing && ids.meta && !loaded.has('meta')) {
        loaded.add('meta');
        /* Meta Pixel base code (official snippet, deferred until consent). */
        (function (f, b, e, v, n, t, s) {
            if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); };
            if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = [];
            t = b.createElement(e); t.async = !0; t.src = v; s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s);
        })(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
        window.fbq('init', ids.meta);
        window.fbq('track', 'PageView');
    }
}
