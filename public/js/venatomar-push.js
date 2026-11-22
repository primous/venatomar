/**
 * Venatomar Web Push Client Helper
 */
(function () {
    'use strict';

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    const VenatomarPush = {
        isSupported: function () {
            return ('serviceWorker' in navigator) && ('PushManager' in window) && ('Notification' in window);
        },

        getRegistration: async function () {
            if (!this.isSupported()) return null;
            return await navigator.serviceWorker.register('/sw.js', { scope: '/' });
        },

        getPublicKey: async function () {
            try {
                const response = await fetch('/push/vapid-public-key');
                const data = await response.json();
                return data.publicKey;
            } catch (e) {
                console.error('[VenatomarPush] Failed to fetch VAPID public key', e);
                return null;
            }
        },

        isSubscribed: async function () {
            if (!this.isSupported()) return false;
            const reg = await this.getRegistration();
            if (!reg) return false;
            const sub = await reg.pushManager.getSubscription();
            return !!sub;
        },

        subscribe: async function (email) {
            if (!this.isSupported()) {
                console.warn('[VenatomarPush] Push not supported in this browser.');
                return false;
            }

            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                console.warn('[VenatomarPush] Notification permission denied or dismissed.');
                return false;
            }

            const reg = await this.getRegistration();
            if (!reg) return false;

            const publicKey = await this.getPublicKey();
            if (!publicKey) return false;

            try {
                let sub = await reg.pushManager.getSubscription();
                if (!sub) {
                    const appServerKey = urlBase64ToUint8Array(publicKey);
                    sub = await reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: appServerKey
                    });
                }

                // Send subscription to server
                const rawKey = sub.getKey ? sub.getKey('p256dh') : null;
                const rawAuth = sub.getKey ? sub.getKey('auth') : null;

                const p256dh = rawKey ? btoa(String.fromCharCode.apply(null, new Uint8Array(rawKey))) : '';
                const auth = rawAuth ? btoa(String.fromCharCode.apply(null, new Uint8Array(rawAuth))) : '';

                const payload = {
                    endpoint: sub.endpoint,
                    keys: {
                        p256dh: p256dh,
                        auth: auth
                    },
                    email: email || null
                };

                this.lastSubscription = sub;
                this.lastEndpoint = sub.endpoint;

                await fetch('/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                console.log('[VenatomarPush] Subscription active.');
                return true;
            } catch (err) {
                console.error('[VenatomarPush] Subscription error:', err);
                return false;
            }
        },

        sendTest: async function (email) {
            try {
                let payload = {};
                if (email) {
                    payload.email = email;
                } else if (this.lastEndpoint) {
                    payload.endpoint = this.lastEndpoint;
                } else {
                    const reg = await this.getRegistration();
                    const sub = reg ? await reg.pushManager.getSubscription() : null;
                    if (sub) payload.endpoint = sub.endpoint;
                }

                const res = await fetch('/push/test', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                return await res.json();
            } catch (e) {
                console.error('[VenatomarPush] Test push error:', e);
                return null;
            }
        },

        unsubscribe: async function () {
            if (!this.isSupported()) return false;
            const reg = await this.getRegistration();
            if (!reg) return false;
            const sub = await reg.pushManager.getSubscription();
            if (sub) {
                const endpoint = sub.endpoint;
                await sub.unsubscribe();
                await fetch('/push/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ endpoint: endpoint })
                });
                return true;
            }
            return false;
        }
    };

    window.VenatomarPush = VenatomarPush;

    // Auto-register service worker on load
    if (VenatomarPush.isSupported()) {
        window.addEventListener('load', function () {
            VenatomarPush.getRegistration().catch(function (e) {
                console.warn('[VenatomarPush] SW register error:', e);
            });
        });
    }
})();
