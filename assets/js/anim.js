/* IGS — Animations dynamiques (sans bibliothèque) */
(function () {
  'use strict';
  var root = document.documentElement;
  if (!root.classList.contains('anim')) return;
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var fine = window.matchMedia('(hover:hover) and (pointer:fine)').matches;
  var B = window.IGS_BASE || '';

  /* Intro (une fois par session, page d'accueil uniquement) */
  try {
    var home = !!$('.hero');
    if (home && !sessionStorage.getItem('igs_intro')) {
      sessionStorage.setItem('igs_intro', '1');
      var intro = document.createElement('div');
      intro.className = 'intro';
      intro.innerHTML = '<div class="intro-in"><img src="' + B + '/assets/icons/icon-192.png" alt="" width="92" height="92"><span class="intro-bar"></span></div>';
      document.body.appendChild(intro);
      var done = function () { intro.classList.add('out'); setTimeout(function () { intro.remove(); }, 400); };
      setTimeout(done, 500);
    }
  } catch (e) {}

  /* Barre de progression + en-tête + retour en haut */
  var bar = document.createElement('div'); bar.className = 'scroll-bar'; document.body.appendChild(bar);
  var top = document.createElement('button'); top.className = 'to-top'; top.setAttribute('aria-label', 'Retour en haut'); top.textContent = '↑';
  top.onclick = function () { window.scrollTo({ top: 0, behavior: 'smooth' }); };
  document.body.appendChild(top);
  var header = $('.site-header'), ticking = false;
  function onScroll() {
    var h = document.documentElement, max = h.scrollHeight - h.clientHeight, y = window.pageYOffset;
    bar.style.transform = 'scaleX(' + (max > 0 ? y / max : 0) + ')';
    if (header) header.classList.toggle('scrolled', y > 30);
    top.classList.toggle('show', y > 600);
    ticking = false;
  }
  window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(onScroll); } }, { passive: true });
  onScroll();

  /* Variantes de révélation (avant l'observateur de main.js) */
  $$('.two-col').forEach(function (c) {
    var k = c.children;
    if (k[0]) k[0].classList.add('r-left');
    if (k[1]) k[1].classList.add('r-right');
  });
  $$('.gallery figure, .hero-visual').forEach(function (e) { e.classList.add('r-zoom'); });
  $$('.grid-4 > .reveal, .cta-in').forEach(function (e) { e.classList.add('r-up'); });

  /* HERO : titre mot par mot */
  var h1 = $('.hero h1');
  if (h1) {
    var node = h1.firstChild;
    if (node && node.nodeType === 3) {
      var frag = document.createDocumentFragment();
      node.textContent.trim().split(/\s+/).forEach(function (w, i) {
        var s = document.createElement('span'); s.className = 'w'; s.style.setProperty('--i', i); s.textContent = w;
        frag.appendChild(s); frag.appendChild(document.createTextNode(' '));
      });
      h1.replaceChild(frag, node);
    }
    var hc = $('.hero-copy');
    if (hc) { hc.classList.remove('reveal'); hc.style.opacity = 1; hc.style.transform = 'none'; }
  }

  /* Phrase rotative */
  var lead = $('.hero .lead');
  if (lead) {
    var phrases = ['Chaque seconde compte.', 'Protection physique & humaine.', 'Vidéosurveillance, alarmes & GPS.', 'Cyberdéfense & audit de réseaux.', 'Disponibles 24h/24 – 7j/7.'];
    var rot = document.createElement('p'); rot.className = 'rotator'; rot.setAttribute('aria-hidden', 'true');
    var sp = document.createElement('span'); sp.textContent = phrases[0]; rot.appendChild(sp);
    lead.parentNode.insertBefore(rot, lead.nextSibling);
    var n = 0;
    setInterval(function () {
      sp.classList.add('out');
      setTimeout(function () { n = (n + 1) % phrases.length; sp.textContent = phrases[n]; sp.classList.remove('out'); }, 450);
    }, 3200);
  }

  /* Anneaux autour du logo + inclinaison */
  var hv = $('.hero-visual');
  if (hv) {
    var glow = $('.shield-glow', hv);
    ['r2', 'r1'].forEach(function (c) { var r = document.createElement('i'); r.className = 'ring ' + c; hv.insertBefore(r, glow ? glow.nextSibling : hv.firstChild); });
    var img = $('img', hv);
    if (fine && img) {
      var hero = $('.hero');
      hero.addEventListener('mousemove', function (e) {
        var r = hv.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
        img.style.animation = 'none';
        img.style.transform = 'rotateY(' + (x * 14).toFixed(2) + 'deg) rotateX(' + (-y * 14).toFixed(2) + 'deg) translateZ(10px)';
      });
      hero.addEventListener('mouseleave', function () { img.style.transform = ''; img.style.animation = ''; });
    }
  }

  /* Particules dorées */
  var hero = $('.hero');
  if (hero) {
    var cv = document.createElement('canvas'); cv.className = 'hero-canvas'; hero.insertBefore(cv, hero.firstChild);
    var ctx = cv.getContext('2d'), P = [], W, H, run = true, dpr = Math.min(window.devicePixelRatio || 1, 2);
    var count = window.innerWidth < 700 ? 28 : 55;
    function size() { W = cv.clientWidth; H = cv.clientHeight; cv.width = W * dpr; cv.height = H * dpr; ctx.setTransform(dpr, 0, 0, dpr, 0, 0); }
    size(); window.addEventListener('resize', size);
    for (var i = 0; i < count; i++) P.push({ x: Math.random(), y: Math.random(), r: Math.random() * 1.8 + .6, vx: (Math.random() - .5) * .00018, vy: -Math.random() * .00022 - .00005, a: Math.random() * .6 + .2 });
    var mouse = null;
    hero.addEventListener('mousemove', function (e) { var r = cv.getBoundingClientRect(); mouse = { x: e.clientX - r.left, y: e.clientY - r.top }; });
    hero.addEventListener('mouseleave', function () { mouse = null; });
    (function frame() {
      if (run) {
        ctx.clearRect(0, 0, W, H);
        for (var i = 0; i < P.length; i++) {
          var p = P[i]; p.x += p.vx * 16; p.y += p.vy * 16;
          if (p.y < -.02) { p.y = 1.02; p.x = Math.random(); } if (p.x < -.02) p.x = 1.02; if (p.x > 1.02) p.x = -.02;
          var px = p.x * W, py = p.y * H;
          ctx.beginPath(); ctx.arc(px, py, p.r, 0, 6.283); ctx.fillStyle = 'rgba(246,213,92,' + p.a + ')'; ctx.shadowColor = '#e8b923'; ctx.shadowBlur = 8; ctx.fill();
          ctx.shadowBlur = 0;
          for (var j = i + 1; j < P.length; j++) {
            var q = P[j], dx = px - q.x * W, dy = py - q.y * H, d = dx * dx + dy * dy;
            if (d < 9000) { ctx.strokeStyle = 'rgba(232,185,35,' + (0.16 * (1 - d / 9000)) + ')'; ctx.lineWidth = 1; ctx.beginPath(); ctx.moveTo(px, py); ctx.lineTo(q.x * W, q.y * H); ctx.stroke(); }
          }
          if (mouse) { var mx = px - mouse.x, my = py - mouse.y, md = mx * mx + my * my; if (md < 16000) { ctx.strokeStyle = 'rgba(246,213,92,' + (0.35 * (1 - md / 16000)) + ')'; ctx.beginPath(); ctx.moveTo(px, py); ctx.lineTo(mouse.x, mouse.y); ctx.stroke(); } }
        }
      }
      requestAnimationFrame(frame);
    })();
    if ('IntersectionObserver' in window) new IntersectionObserver(function (en) { run = en[0].isIntersecting; }).observe(hero);
    document.addEventListener('visibilitychange', function () { run = !document.hidden; });
  }

  /* Compteurs animés */
  function countUp(el) {
    var m = el.textContent.match(/^(\d+)(.*)$/); if (!m) return;
    var to = +m[1], suf = m[2], t0 = null, dur = 1600;
    function step(t) {
      if (!t0) t0 = t; var p = Math.min((t - t0) / dur, 1), v = Math.round(to * (1 - Math.pow(1 - p, 3)));
      el.textContent = v + suf; if (p < 1) requestAnimationFrame(step);
    }
    el.textContent = '0' + suf; requestAnimationFrame(step);
  }
  var counters = $$('.hero-badges strong');
  if (counters.length) setTimeout(function () { counters.forEach(countUp); }, 1100);

  /* Bande défilante */
  var strip = $('.strip'), sIn = strip && $('.strip-in', strip);
  if (sIn) {
    var all = sIn.innerHTML; sIn.innerHTML = all + all + all + all;
    strip.classList.add('marquee');
  }

  /* Cartes : halo + inclinaison 3D (souris) */
  if (fine) {
    $$('.card, .why').forEach(function (c) {
      c.addEventListener('mousemove', function (e) {
        var r = c.getBoundingClientRect(), x = e.clientX - r.left, y = e.clientY - r.top;
        c.style.setProperty('--mx', x + 'px'); c.style.setProperty('--my', y + 'px');
        if (c.classList.contains('card')) {
          c.style.transition = 'border-color .3s, box-shadow .3s';
          c.style.transform = 'translateY(-6px) perspective(800px) rotateX(' + (((y / r.height) - .5) * -7).toFixed(2) + 'deg) rotateY(' + (((x / r.width) - .5) * 7).toFixed(2) + 'deg)';
        }
      });
      c.addEventListener('mouseleave', function () { c.style.transform = ''; c.style.transition = ''; });
    });
  }

  /* Ondulation sur les boutons */
  document.addEventListener('click', function (e) {
    var b = e.target.closest && e.target.closest('.btn'); if (!b) return;
    var r = b.getBoundingClientRect(), s = Math.max(r.width, r.height), d = document.createElement('span');
    d.className = 'ripple'; d.style.width = d.style.height = s + 'px';
    d.style.left = (e.clientX - r.left - s / 2) + 'px'; d.style.top = (e.clientY - r.top - s / 2) + 'px';
    b.appendChild(d); setTimeout(function () { d.remove(); }, 750);
  });

  /* Lightbox */
  var lb = document.createElement('div'); lb.className = 'lightbox'; lb.setAttribute('role', 'dialog'); lb.setAttribute('aria-modal', 'true');
  lb.innerHTML = '<button type="button" aria-label="Fermer">✕</button><img alt=""><div class="lb-cap"></div>';
  document.body.appendChild(lb);
  var lbImg = $('img', lb), lbCap = $('.lb-cap', lb);
  function openLb(src, alt, cap) { lbImg.src = src; lbImg.alt = alt || ''; lbCap.textContent = cap || ''; lb.classList.add('open'); document.body.style.overflow = 'hidden'; }
  function closeLb() { lb.classList.remove('open'); document.body.style.overflow = ''; }
  lb.addEventListener('click', closeLb);
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeLb(); });
  $$('.gallery figure, .photo-card').forEach(function (f) {
    f.addEventListener('click', function () {
      var i = $('img', f), c = $('figcaption', f);
      openLb(i.currentSrc || i.src, i.alt, c ? c.textContent : i.alt);
    });
  });

  /* Transition douce entre pages */
  document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('a[href]');
    if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button) return;
    if (a.target && a.target !== '_self') return;
    if (a.hasAttribute('download') || a.origin !== location.origin) return;
    if (a.pathname === location.pathname && a.hash) return;
    if (/^\/admin\//.test(a.pathname)) return;
    e.preventDefault();
    document.body.classList.add('leaving');
    setTimeout(function () { location.href = a.href; }, 220);
  });
  window.addEventListener('pageshow', function (e) { if (e.persisted) document.body.classList.remove('leaving'); });
})();
