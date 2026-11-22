// Venatomar Web Push Service Worker
self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function (event) {
    let payload = {
        title: 'Venatomar',
        body: 'Nueva notificación de su cuenta.',
        icon: '/images/logo.png',
        badge: '/images/logo.png',
        url: '/es/portal',
        tag: 'venatomar-' + Date.now(),
        data: {}
    };

    if (event.data) {
        try {
            const parsed = event.data.json();
            payload = Object.assign(payload, parsed);
        } catch (e) {
            payload.body = event.data.text();
        }
    }

    const notificationOptions = {
        body: payload.body,
        icon: payload.icon || '/images/logo.png',
        badge: payload.badge || '/images/logo.png',
        tag: payload.tag || 'venatomar-notification',
        renotify: true,
        vibrate: [150, 80, 150],
        data: {
            url: payload.url || (payload.data && payload.data.url) || '/es/portal'
        }
    };

    event.waitUntil(
        self.registration.showNotification(payload.title, notificationOptions)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const targetUrl = (event.notification.data && event.notification.data.url)
        ? event.notification.data.url
        : '/es/portal';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
