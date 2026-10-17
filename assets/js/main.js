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
