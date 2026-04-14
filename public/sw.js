const CACHE_NAME = "afsc-dashboard-v4";
const STATIC_ASSETS = ["/offline.html", "/logo2.png", "/manifest.json"];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        }),
    );
    self.skipWaiting();
});

self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                }),
            );
        }),
    );
    self.clients.claim();
});

// Runtime Caching for Student Groups and JS
self.addEventListener("fetch", (event) => {
    // 1. Only intercept GET requests
    if (event.request.method !== "GET") return;

    const url = new URL(event.request.url);
    const isLivewire = event.request.headers.has("X-Livewire");

    // 2. Filter out external tracking/analytics scripts to avoid noise and ERR_FAILED issues
    const ignoredDomains = [
        "static.cloudflareinsights.com",
        "google-analytics.com",
        "googletagmanager.com",
    ];

    if (ignoredDomains.some((domain) => url.hostname.includes(domain))) {
        return;
    }

    // Cache Strategy: Network First, falling back to cache
    // Applies to:
    // 1. Student Group pages (navigation)
    // 2. Offline Attendance JS (script)
    if (
        (url.pathname.includes("/student-group") &&
            event.request.mode === "navigate") ||
        url.pathname === "/js/offline-attendance.js"
    ) {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    if (
                        !response ||
                        response.status !== 200 ||
                        response.type !== "basic"
                    ) {
                        return response;
                    }

                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });

                    return response;
                })
                .catch(async () => {
                    const cacheResponse = await caches.match(event.request);
                    if (cacheResponse) return cacheResponse;

                    if (event.request.mode === "navigate") {
                        return (await caches.match("/offline.html")) || 
                               new Response("Offline page not available", { status: 503, headers: { "Content-Type": "text/plain" } });
                    }
                    
                    return new Response("Network error occurred", { status: 408, headers: { "Content-Type": "text/plain" } });
                }),
        );
        return;
    }

    // Generic Fetch handling
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Don't cache Livewire fragments as they can break full-page reloads
                if (isLivewire) return response;

                const responseClone = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    if (
                        event.request.url.startsWith("http") &&
                        response.status === 200
                    ) {
                        cache.put(event.request, responseClone);
                    }
                });

                return response;
            })
            .catch(async () => {
                const cacheResponse = await caches.match(event.request);
                if (cacheResponse) return cacheResponse;

                if (event.request.mode === "navigate") {
                    return (await caches.match("/offline.html")) || 
                           new Response("Offline page not available", { status: 503, headers: { "Content-Type": "text/plain" } });
                }

                return new Response("Network error occurred", { status: 408, headers: { "Content-Type": "text/plain" } });
            }),
    );
});
