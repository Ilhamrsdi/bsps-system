const CACHE_NAME = 'bsps-verval-v2';
const ASSETS_TO_CACHE = [
  '/',
  '/survey',
  '/verval-data',
  '/dashboard-kecamatan',
  '/assets/css/app.css',
  '/assets/css/component.css',
  '/assets/css/modal.css',
  '/assets/css/table.css',
  '/assets/css/dropdown.css',
  '/assets/js/app.js',
  '/assets/js/modal.js',
  '/assets/js/dropdown.js',
  '/logo.jpg',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
  'https://cdn.jsdelivr.net/npm/chart.js'
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('[ServiceWorker] Caching app shell & assets for offline mode');
      return cache.addAll(ASSETS_TO_CACHE).catch((err) => {
        console.warn('[ServiceWorker] Caching warning:', err);
      });
    })
  );
  self.skipWaiting();
});

// Activate Event
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
    })
  );
  self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // Cache First for static assets (CSS, JS, Images, Fonts) with ignoreSearch: true
  const isStaticAsset = url.pathname.includes('/assets/') || 
                        url.pathname.endsWith('.css') || 
                        url.pathname.endsWith('.js') || 
                        url.pathname.endsWith('.jpg') || 
                        url.pathname.endsWith('.png') ||
                        url.hostname.includes('cdnjs.cloudflare.com') ||
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
        });
      })
    );
    return;
  }

  // Network First for HTML pages, fallback to cached HTML page when offline
  event.respondWith(
    fetch(event.request)
      .then((response) => {
        if (response && response.status === 200 && response.type === 'basic') {
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return response;
      })
      .catch(() => {
        console.log('[ServiceWorker] Network failed, serving from cache for URL:', event.request.url);
        return caches.match(event.request, { ignoreSearch: true }).then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }
          if (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html')) {
            return caches.match('/survey', { ignoreSearch: true });
          }
        });
      })
  );
});
