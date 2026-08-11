/**
 * Service Worker - BSPS Verval (Offline Blankspot & PWA Engine)
 * Dinas PUPR / BSPS Verval System
 */

const CACHE_NAME = 'bsps-verval-v2';

const ASSETS_TO_CACHE = [
    '/',
    '/survey',
    '/verval-data',
    '/dashboard-kecamatan',
    '/petugas/dashboard',
    '/petugas/belum-survei',
    '/petugas/sudah-survei',
    '/assets/css/app.css',
    '/assets/css/component.css',
    '/assets/css/modal.css',
    '/assets/css/table.css',
    '/assets/css/dropdown.css',
    '/assets/js/app.js',
    '/assets/js/modal.js',
    '/assets/js/dropdown.js',
    '/assets/js/chart.js',
    '/logo.jpg',
    '/manifest.json',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap'
];

// 1. Install Event: Pre-cache App Shell & Assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[ServiceWorker] Caching app shell & assets for offline mode');
            return cache.addAll(ASSETS_TO_CACHE).catch((err) => {
                console.warn('[ServiceWorker] Caching partial warning:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event: Clean up old cache versions
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(
                keyList.map((key) => {
                    if (key !== CACHE_NAME) {
                        console.log('[ServiceWorker] Removing old cache:', key);
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Event: Intercept Requests for Offline Capability
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Strategy 1: Cache First for static assets (CSS, JS, Images, Fonts, CDNs) with ignoreSearch: true
    const isStaticAsset = url.pathname.includes('/assets/') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.json') ||
        url.hostname.includes('cdnjs.cloudflare.com') ||
        url.hostname.includes('fonts.googleapis.com') ||
        url.hostname.includes('fonts.gstatic.com') ||
        url.hostname.includes('jsdelivr.net');

    if (isStaticAsset) {
        event.respondWith(
            caches.match(event.request, { ignoreSearch: true }).then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(event.request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return networkResponse;
                }).catch(() => {
                    // Ignore offline network error for static background fetches
                });
            })
        );
        return;
    }

    // Strategy 2: Network First for HTML pages, fallback to cached HTML page when offline
    if (event.request.mode === 'navigate' || event.request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    if (response && response.status === 200) {
                        const responseToCache = response.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return response;
                })
                .catch(async () => {
                    console.log('[ServiceWorker] Network failed, serving from cache for URL:', event.request.url);
                    const cachedResponse = await caches.match(event.request, { ignoreSearch: true });
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // Fallback to matched known shell pages
                    const cache = await caches.open(CACHE_NAME);
                    return await cache.match('/petugas/dashboard', { ignoreSearch: true }) ||
                        await cache.match('/survey', { ignoreSearch: true }) ||
                        await cache.match('/', { ignoreSearch: true });
                })
        );
    }
});
