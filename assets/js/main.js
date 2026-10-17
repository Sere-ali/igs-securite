// Menu mobile
(function () {
  var b = document.querySelector('.burger'), m = document.getElementById('menu');
  if (b && m) {
    b.addEventListener('click', function () {
      var o = m.classList.toggle('open');
      b.setAttribute('aria-expanded', o ? 'true' : 'false');
    });
    m.addEventListener('click', function (e) { if (e.target.tagName === 'A') m.classList.remove('open'); });
  }

  // Animations d'apparition au scroll
  var els = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
      });
    }, { threshold: 0.12 });
    els.forEach(function (el, i) { el.style.transitionDelay = (i % 4) * 80 + 'ms'; io.observe(el); });
  } else {
    els.forEach(function (el) { el.classList.add('in'); });
  }
})();


// ===== PWA : service worker + bouton « Télécharger l'application » =====
(function () {
  var base = (window.IGS_BASE || '');
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register(base + '/sw.js').catch(function () {});
    });
  }

  var btns = document.querySelectorAll('[data-install]');
  var modal = document.getElementById('install-modal');
  var standalone = (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) || window.navigator.standalone === true;

  function hideAll() {
    btns.forEach(function (b) { b.hidden = true; });
    var sec = document.querySelector('[data-install-section]'); if (sec) sec.hidden = true;
  }
  if (standalone) { hideAll(); return; }   // déjà installée : on masque les boutons

  var deferred = null;
  window.addEventListener('beforeinstallprompt', function (e) { e.preventDefault(); deferred = e; });
  window.addEventListener('appinstalled', function () { deferred = null; hideAll(); });

  var ua = navigator.userAgent || '';
  var isIOS = /iphone|ipad|ipod/i.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

  function openModal() {
    if (!modal) return;
    document.getElementById('im-ios').hidden = !isIOS;
    document.getElementById('im-other').hidden = isIOS;
    modal.classList.add('open');
  }
  function closeModal() { if (modal) modal.classList.remove('open'); }

  btns.forEach(function (b) {
    b.addEventListener('click', function () {
      if (deferred) {
        deferred.prompt();
        deferred.userChoice.then(function () { deferred = null; });
      } else {
        openModal();
      }
    });
  });
  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal || e.target.hasAttribute('data-close')) closeModal();
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
  }
})();
