/**
 * Service Worker - BSPS Verval (Offline Mode PWA)
 * Dinas PUPR / BSPS Verval System
 */

const CACHE_NAME = 'bsps-verval-cache-v1';

// Daftar Aset Statis yang Langsung Disimpan ke Memori HP
const PRECACHE_ASSETS = [
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

// 1. Install Event: Cache Seluruh Aset Statis Inti
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[PWA SW] Pre-caching static assets...');
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('[PWA SW] Pre-cache partial warning:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event: Bersihkan Cache Lama Jika Ada Versi Baru
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        console.log('[PWA SW] Clearing old cache:', cache);
                        return caches.delete(cache);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Event: Intercept Request untuk Akses Offline
self.addEventListener('fetch', (event) => {
    const req = event.request;

    // Hanya tangani request GET (POST/PUT form submit tidak di-cache)
    if (req.method !== 'GET') {
        return;
    }

    const url = new URL(req.url);

    // Strategi 1: Untuk Halaman HTML (Navigasi Antar Halaman)
    // Mode: Network-First dengan Cache Fallback
    if (req.mode === 'navigate' || req.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(req)
                .then((networkResponse) => {
                    // Jika sukses online, simpan salinan halaman terbaru ke cache
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(req, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    // Jika offline / sinyal putus, ambil langsung dari cache HP
                    console.log('[PWA SW] Offline mode: loading page from cache:', req.url);
                    const cachedResponse = await caches.match(req);
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // Fallback jika halaman spesifik belum pernah dibuka, coba cari halaman terdekat
                    const allCaches = await caches.open(CACHE_NAME);
                    const matchedFallback = await allCaches.match('/petugas/dashboard') || await allCaches.match('/survey') || await allCaches.match('/');
                    return matchedFallback || new Response(
                        `<!DOCTYPE html>
                        <html lang="id">
                        <head>
                            <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
                            <title>BSPS Verval - Mode Offline</title>
                            <style>
                                body { font-family: sans-serif; background: #002855; color: #fff; text-align: center; padding: 40px 20px; }
                                .box { background: #fff; color: #333; max-width: 420px; margin: 40px auto; padding: 24px; border-radius: 12px; }
                                .btn { background: #ffb800; color: #002855; padding: 10px 18px; border-radius: 6px; font-weight: bold; text-decoration: none; display: inline-block; margin-top: 14px; }
                            </style>
                        </head>
                        <body>
                            <div class="box">
                                <h2>📡 Mode Offline Aktif</h2>
                                <p>Perangkat Anda sedang tidak terhubung ke internet. Halaman yang pernah Anda buka sebelumnya tetap dapat diakses.</p>
                                <a href="javascript:window.history.back()" class="btn">Kembali ke Halaman Sebelumnya</a>
                            </div>
                        </body></html>`,
                        { headers: { 'Content-Type': 'text/html' } }
                    );
                })
        );
        return;
    }

    // Strategi 2: Untuk Aset Statis (CSS, JS, Gambar, Font, Icon)
    // Mode: Stale-While-Revalidate (Ambil cepat dari Cache, perbarui di background)
    event.respondWith(
        caches.match(req).then((cachedResponse) => {
            const fetchPromise = fetch(req)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(req, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => {
                    // Abaikan error fetch background jika offline
                });

            // Kembalikan versi cache jika ada, jika belum ada tunggu network
            return cachedResponse || fetchPromise;
        })
    );
});
