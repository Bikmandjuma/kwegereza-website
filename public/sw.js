const CACHE_NAME = 'kiu-static-v1';

const STATIC_ASSETS = [
    '/Guest/images/logo.png',
    '/Guest/assets/style.css',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/offline.html',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const req = event.request;

    // Only ever handle GET requests — never intercept POST/PUT/DELETE.
    // This app has CSRF-protected forms, live chat, and session-based
    // auth everywhere; caching or intercepting anything but a plain GET
    // risks silently breaking a form submission or serving a stale CSRF
    // token. Safety over offline-completeness.
    if (req.method !== 'GET') {
        return;
    }

    const url = new URL(req.url);

    // Never cache anything under the authenticated dashboards, or any
    // request carrying a query string (search results, filters) — those
    // are exactly the pages where stale content would be misleading.
    if (url.pathname.startsWith('/owner') || url.pathname.startsWith('/student')) {
        return;
    }

    // Static assets: cache-first, since these rarely change.
    if (/\.(png|jpg|jpeg|svg|webp|woff2?|css)$/.test(url.pathname)) {
        event.respondWith(
            caches.match(req).then((cached) => cached || fetch(req))
        );
        return;
    }

    // Page navigations: always go to the network first (this is a live,
    // dynamic Islamic learning + chat platform — a stale cached homepage
    // showing yesterday's announcements is worse than a loading spinner).
    // Only fall back to a friendly offline page if the network is
    // genuinely unreachable.
    if (req.mode === 'navigate') {
        event.respondWith(
            fetch(req).catch(() => caches.match('/offline.html'))
        );
    }
});

// Fires when a push message arrives from the server, even if no tab for
// this site is currently open. This is the part that makes it a real
// push notification rather than just an in-page popup.
self.addEventListener('push', (event) => {
    let data = { title: 'Kwegereza Islam Umuryango', body: 'Hari amakuru mashya.', url: '/' };

    if (event.data) {
        try {
            data = { ...data, ...event.data.json() };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: '/icons/icon-192x192.png',
            badge: '/icons/icon-192x192.png',
            data: { url: data.url },
        })
    );
});

// Clicking the notification focuses an already-open tab for this site if
// one exists, otherwise opens a new one at the relevant URL.
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            if (self.clients.openWindow) {
                return self.clients.openWindow(targetUrl);
            }
        })
    );
});
