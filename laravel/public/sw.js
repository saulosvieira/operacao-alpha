// Service Worker for Alfa Quest PWA
// Handles push notifications and basic caching

const CACHE_NAME = 'alfa-quest-v4';
const urlsToCache = [
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png'
];
// Note: We don't cache '/' anymore to avoid stale HTML issues

// Install event - cache essential resources
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
      .catch((error) => {
        console.error('Cache installation failed:', error);
      })
  );
  // Force the waiting service worker to become the active service worker
  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  // Claim all clients immediately
  return self.clients.claim();
});

// Fetch event - network-first for navigation, cache-first for assets
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);
  
  // Skip non-GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  // Skip admin routes - let them load normally without SW interference
  if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/vendor/')) {
    return;
  }

  // Skip API requests - always fetch from network
  if (event.request.url.includes('/api/')) {
    return;
  }

  // Navigation requests (HTML pages) - always network-first
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          // Cache the latest HTML
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
          return response;
        })
        .catch(() => {
          // Offline fallback
          return caches.match('/');
        })
    );
    return;
  }

  // Static assets (JS, CSS, images) - cache-first with network fallback
  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        // Cache hit - return response
        if (response) {
          return response;
        }

        // Clone the request
        const fetchRequest = event.request.clone();

        return fetch(fetchRequest).then((response) => {
          // Check if valid response
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }

          // Clone the response
          const responseToCache = response.clone();

          caches.open(CACHE_NAME)
            .then((cache) => {
              cache.put(event.request, responseToCache);
            });

          return response;
        });
      })
      .catch(() => {
        // Return a custom offline page if available
        return caches.match('/');
      })
  );
});

// Push event - handle push notifications
self.addEventListener('push', (event) => {
  console.log('Push notification received:', event);

  let notificationData = {
    title: 'Alfa Quest',
    body: 'Você tem uma nova notificação',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-96x96.png',
    tag: 'default',
    requireInteraction: false,
    data: {
      url: '/'
    }
  };

  // Parse notification data if available
  if (event.data) {
    try {
      const data = event.data.json();
      notificationData = {
        title: data.title || notificationData.title,
        body: data.body || notificationData.body,
        icon: data.icon || notificationData.icon,
        badge: data.badge || notificationData.badge,
        tag: data.tag || notificationData.tag,
        requireInteraction: data.requireInteraction || false,
        data: {
          url: data.url || '/',
          ...data.data
        }
      };
    } catch (error) {
      console.error('Error parsing notification data:', error);
    }
  }

  event.waitUntil(
    self.registration.showNotification(notificationData.title, {
      body: notificationData.body,
      icon: notificationData.icon,
      badge: notificationData.badge,
      tag: notificationData.tag,
      requireInteraction: notificationData.requireInteraction,
      data: notificationData.data,
      vibrate: [200, 100, 200],
      actions: [
        {
          action: 'open',
          title: 'Abrir'
        },
        {
          action: 'close',
          title: 'Fechar'
        }
      ]
    })
  );
});

// Notification click event - handle notification interactions
self.addEventListener('notificationclick', (event) => {
  console.log('Notification clicked:', event);

  event.notification.close();

  // Handle action buttons
  if (event.action === 'close') {
    return;
  }

  // Get the URL to open from notification data
  const urlToOpen = event.notification.data?.url || '/';

  event.waitUntil(
    clients.matchAll({
      type: 'window',
      includeUncontrolled: true
    }).then((clientList) => {
      // Check if there's already a window open
      for (let i = 0; i < clientList.length; i++) {
        const client = clientList[i];
        if (client.url === urlToOpen && 'focus' in client) {
          return client.focus();
        }
      }
      
      // If no window is open, open a new one
      if (clients.openWindow) {
        return clients.openWindow(urlToOpen);
      }
    })
  );
});

// Background sync event (for future use)
self.addEventListener('sync', (event) => {
  console.log('Background sync:', event);
  
  if (event.tag === 'sync-answers') {
    event.waitUntil(
      // Sync pending answers when connection is restored
      syncPendingAnswers()
    );
  }
});

// Helper function for syncing (placeholder)
async function syncPendingAnswers() {
  // This would sync any pending exam answers stored in IndexedDB
  // when the connection is restored
  console.log('Syncing pending answers...');
  return Promise.resolve();
}

// Message event - handle messages from the client
self.addEventListener('message', (event) => {
  console.log('Service Worker received message:', event.data);

  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }

  if (event.data && event.data.type === 'CACHE_URLS') {
    event.waitUntil(
      caches.open(CACHE_NAME).then((cache) => {
        return cache.addAll(event.data.urls);
      })
    );
  }
});
