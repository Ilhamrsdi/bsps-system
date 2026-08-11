/**
 * Service Worker - BSPS Verval (Offline Blankspot & PWA Engine)
 * Dinas PUPR / BSPS Verval System
 */

const CACHE_NAME = 'bsps-verval-v3';

// Aset Statis Inti yang Wajib Di-cache
const STATIC_ASSETS = [
    '/assets/css/app.css',
    '/assets/css/component.css',
    '/assets/css/modal.css',
    '/assets/css/table.css',
    '/assets/css/dropdown.css',
    '/assets/js/app.js',
    '/assets/js/modal.js',
    '/assets/js/dropdown.js',
    '/assets/js/chart.js',
    '/assets/js/offline-survey.js',
    '/logo.jpg',
    '/manifest.json',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap'
];

// 1. Install Event: Pre-cache Static Assets secara Aman (Toleran Error)
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(async (cache) => {
            console.log('[ServiceWorker v3] Pre-caching static assets...');
            for (const asset of STATIC_ASSETS) {
                try {
                    await cache.add(asset);
                } catch (err) {
                    console.warn('[ServiceWorker v3] Gagal pre-cache asset:', asset, err);
                }
            }
        })
    );
});

// 2. Activate Event: Bersihkan Versi Cache Lama & Langsung Kontrol Halaman
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(
                keyList.map((key) => {
                    if (key !== CACHE_NAME) {
                        console.log('[ServiceWorker v3] Menghapus cache lama:', key);
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Event: Intercept Request untuk Akses Offline Tanpa Dinosaurus
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Strategi 1: Aset Statis (CSS, JS, Gambar, Font, CDN) -> Cache First
    const isStaticAsset = url.pathname.includes('/assets/') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.jpeg') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.json') ||
        url.pathname.endsWith('.woff2') ||
        url.pathname.endsWith('.woff') ||
        url.pathname.endsWith('.ttf') ||
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
                    // Abaikan fetch error saat offline
                });
            })
        );
        return;
    }

    // Strategi 2: Halaman Web HTML (Navigasi Antar Halaman)
    // Mode: Network First dengan Auto-Cache & Offline Fallback (Anti Dinosaurus)
    if (event.request.mode === 'navigate' || event.request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(event.request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseToCache);
                            // Simpan juga salinan sebagai fallback umum
                            cache.put('/offline-shell', responseToCache.clone());
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    console.log('[ServiceWorker v3] Offline terdeteksi, memuat dari cache:', event.request.url);
                    
                    // 1. Coba cari halaman persis yang diminta di cache
                    const matchedDirect = await caches.match(event.request, { ignoreSearch: true });
                    if (matchedDirect) {
                        return matchedDirect;
                    }

                    // 2. Coba cari fallback halaman yang tersimpan
                    const cache = await caches.open(CACHE_NAME);
                    const matchedShell = await cache.match('/offline-shell') || 
                                         await cache.match('/petugas/dashboard', { ignoreSearch: true }) ||
                                         await cache.match('/petugas/belum-survei', { ignoreSearch: true }) ||
                                         await cache.match('/survey', { ignoreSearch: true }) ||
                                         await cache.match('/', { ignoreSearch: true });
                    
                    if (matchedShell) {
                        return matchedShell;
                    }

                    // 3. Fallback Darurat HTML (Anti Layar Dinosaurus)
                    return new Response(
                        `<!DOCTYPE html>
                        <html lang="id">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>BSPS Verval - Mode Offline</title>
                            <link rel="stylesheet" href="/assets/css/app.css">
                            <style>
                                body { background: #002855; color: #ffffff; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: sans-serif; padding: 20px; box-sizing: border-box; }
                                .offline-card { background: #ffffff; color: #002855; max-width: 440px; width: 100%; border-radius: 16px; padding: 32px 24px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
                                .offline-icon { width: 70px; height: 70px; background: rgba(255, 184, 0, 0.15); color: #d69e00; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 30px; margin-bottom: 18px; }
                                .offline-btn { background: #002855; color: #ffffff; padding: 12px 24px; border-radius: 8px; font-weight: bold; text-decoration: none; display: inline-block; margin-top: 18px; }
                            </style>
                        </head>
                        <body>
                            <div class="offline-card">
                                <div class="offline-icon">📡</div>
                                <h3 style="margin: 0 0 10px 0; font-size: 18px;">Mode Offline Aktif</h3>
                                <p style="color: #64748b; font-size: 13.5px; line-height: 1.5; margin: 0;">
                                    Perangkat Anda sedang berada di area tanpa sinyal. Halaman yang pernah Anda buka sebelumnya tetap dapat diakses.
                                </p>
                                <a href="javascript:window.history.back()" class="offline-btn">Kembali ke Halaman Sebelumnya</a>
                            </div>
                        </body>
                        </html>`,
                        { headers: { 'Content-Type': 'text/html' } }
                    );
                })
        );
    }
});
