/* IGS Sécurité — Service worker (mise en cache + mode hors ligne) */
const VERSION = 'igs-v2';
const SCOPE = self.registration.scope;
const OFFLINE = new URL('offline.html', SCOPE).href;
const CORE = ['offline.html', 'assets/css/style.css', 'assets/css/anim.css', 'assets/js/anim.js', 'assets/js/main.js', 'assets/img/logo.jpeg', 'assets/icons/icon-192.png']
  .map(p => new URL(p, SCOPE).href);

self.addEventListener('install', e => {
  e.waitUntil(caches.open(VERSION).then(c => c.addAll(CORE)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys().then(keys => Promise.all(keys.filter(k => k !== VERSION).map(k => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', e => {
  const req = e.request;
  if (req.method !== 'GET') return;
  const url = new URL(req.url);
  if (url.origin !== location.origin) return;                       // polices Google, etc. : navigateur
  if (url.pathname.includes('/admin/') || url.pathname.endsWith('install.php')) return; // jamais en cache

  // Pages : réseau d'abord, repli sur le cache puis page hors ligne
  if (req.mode === 'navigate') {
    e.respondWith(
      fetch(req).then(res => {
        if (res.ok) { const copy = res.clone(); caches.open(VERSION).then(c => c.put(req, copy)); }
        return res;
      }).catch(() => caches.match(req).then(r => r || caches.match(OFFLINE)))
    );
    return;
  }

  // Fichiers statiques : cache d'abord, mise à jour en arrière-plan
  if (/\.(css|js|png|jpe?g|webp|svg|ico|woff2?|webmanifest)$/i.test(url.pathname)) {
    e.respondWith(
      caches.match(req).then(cached => {
        const net = fetch(req).then(res => {
          if (res.ok) { const copy = res.clone(); caches.open(VERSION).then(c => c.put(req, copy)); }
          return res;
        }).catch(() => cached);
        return cached || net;
      })
    );
  }
});
