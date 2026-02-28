const CACHE_NAME = 'eventpoint-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
    // Pass all requests straight through to the network.
    // This keeps the service worker minimal while satisfying
    // the PWA installability requirement.
    event.respondWith(fetch(event.request));
});
