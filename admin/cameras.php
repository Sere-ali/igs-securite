<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/cameras.php';
$me = require_login();
ensure_cameras_table();
$adminRole = null; $adminActive = 'cameras'; $adminTitle = 'Vidéosurveillance — Caméras';
require __DIR__ . '/partials/header.php';
$cams = db()->query('SELECT * FROM cameras WHERE active=1 ORDER BY client, name')->fetchAll();
$clients = array_values(array_unique(array_map(fn($c) => $c['client'], $cams)));
log_action('Consultation des caméras', count($cams) . ' caméra(s)');
?>
<div class="panel cam-bar">
  <div class="cam-filters">
    <label>Client / site
      <select id="cam-filter"><option value="">Tous (<?= count($cams) ?>)</option>
        <?php foreach ($clients as $c): ?><option><?= e($c ?: 'Sans client') ?></option><?php endforeach; ?>
      </select></label>
    <label>Affichage
      <select id="cam-cols"><option value="auto">Automatique</option><option value="1">1 par ligne</option><option value="2">2 par ligne</option><option value="3">3 par ligne</option><option value="4">4 par ligne</option></select></label>
  </div>
  <?php if ($me['role'] === 'superadmin'): ?><a class="btn sm" href="cameras-gestion">⚙️ Gérer les caméras</a><?php endif; ?>
</div>

<?php if (!$cams): ?>
  <div class="panel"><p class="muted">Aucune caméra enregistrée.
    <?= $me['role'] === 'superadmin' ? '<a href="cameras-gestion">Ajouter une première caméra →</a>' : 'Le super administrateur peut en ajouter.' ?></p></div>
<?php endif; ?>

<div class="cam-grid" id="cam-grid">
<?php foreach ($cams as $c):
    $url = $c['stream_url'];
    $isHttp = stripos($url, 'http://') === 0;
?>
  <article class="cam" data-client="<?= e($c['client'] ?: 'Sans client') ?>" data-type="<?= e($c['stream_type']) ?>" data-url="<?= e($url) ?>" data-refresh="<?= max(1, (int)$c['refresh_sec']) ?>">
    <div class="cam-view">
      <span class="cam-state" data-s="load">● Connexion…</span>
      <div class="cam-msg" hidden></div>
      <?php if ($isHttp): ?><div class="cam-msg warn">⚠️ Adresse en http:// — le navigateur la bloque sur un site https. Utilisez une adresse https:// (voir l'aide).</div><?php endif; ?>
      <button type="button" class="cam-full" aria-label="Plein écran">⛶</button>
    </div>
    <div class="cam-info">
      <strong><?= e($c['name']) ?></strong>
      <span class="muted"><?= e($c['client']) ?><?= $c['location'] ? ' · ' . e($c['location']) : '' ?></span>
      <?php if ($c['ip']): ?><span class="cam-ip">IP : <?= e($c['ip']) ?></span><?php endif; ?>
    </div>
  </article>
<?php endforeach; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.13/dist/hls.min.js"></script>
<script>
(function () {
  var grid = document.getElementById('cam-grid');
  var cams = Array.prototype.slice.call(grid.querySelectorAll('.cam'));
  function state(cam, s, txt) { var el = cam.querySelector('.cam-state'); el.dataset.s = s; el.textContent = txt; if (s === 'on') { var w = cam.querySelector('.cam-msg.warn'); if (w) w.hidden = true; } }
  function msg(cam, t) { var m = cam.querySelector('.cam-msg:not(.warn)'); m.hidden = !t; m.textContent = t || ''; }
  function start(cam) {
    var view = cam.querySelector('.cam-view'), type = cam.dataset.type, url = cam.dataset.url;
    if (!/^https?:\/\//i.test(url)) { state(cam, 'off', '● Hors ligne'); msg(cam, 'Adresse du flux invalide.'); return; }
    if (type === 'hls') {
      var v = document.createElement('video'); v.muted = true; v.autoplay = true; v.playsInline = true; v.controls = true; view.prepend(v);
      if (window.Hls && Hls.isSupported()) {
        var h = new Hls({ lowLatencyMode: true, maxBufferLength: 6 });
        h.loadSource(url); h.attachMedia(v);
        h.on(Hls.Events.MANIFEST_PARSED, function () { v.play().catch(function () {}); });
        h.on(Hls.Events.ERROR, function (e, d) { if (d.fatal) { state(cam, 'off', '● Hors ligne'); msg(cam, 'Flux inaccessible. Nouvelle tentative…'); setTimeout(function () { h.destroy(); v.remove(); start(cam); }, 8000); } });
      } else if (v.canPlayType('application/vnd.apple.mpegurl')) { v.src = url; }
      else { msg(cam, 'Navigateur non compatible.'); }
      v.addEventListener('playing', function () { state(cam, 'on', '● En direct'); msg(cam, ''); });
    } else if (type === 'mjpeg' || type === 'snapshot') {
      var img = document.createElement('img'); img.alt = ''; view.prepend(img);
      img.onload = function () { state(cam, 'on', type === 'mjpeg' ? '● En direct' : '● Image'); msg(cam, ''); };
      img.onerror = function () { state(cam, 'off', '● Hors ligne'); msg(cam, 'Flux inaccessible.'); };
      if (type === 'mjpeg') img.src = url;
      else { var load = function () { img.src = url + (url.indexOf('?') < 0 ? '?' : '&') + '_=' + Date.now(); }; load(); setInterval(function () { if (!document.hidden) load(); }, (+cam.dataset.refresh || 3) * 1000); }
    } else {
      var f = document.createElement('iframe'); f.src = url; f.setAttribute('sandbox', 'allow-scripts allow-same-origin allow-forms'); f.setAttribute('referrerpolicy', 'no-referrer'); f.allow = 'fullscreen'; view.prepend(f);
      f.onload = function () { state(cam, 'on', '● Connecté'); };
    }
  }
  // Démarrage progressif : seulement quand la caméra est visible (économise la bande passante)
  var io = 'IntersectionObserver' in window ? new IntersectionObserver(function (en) {
    en.forEach(function (x) { if (x.isIntersecting && !x.target.dataset.started) { x.target.dataset.started = 1; start(x.target); } });
  }, { rootMargin: '200px' }) : null;
  cams.forEach(function (c) { io ? io.observe(c) : start(c); });
  grid.addEventListener('click', function (e) {
    var b = e.target.closest('.cam-full'); if (!b) return;
    var v = b.closest('.cam-view'); (v.requestFullscreen || v.webkitRequestFullscreen || function () {}).call(v);
  });
  document.getElementById('cam-filter').addEventListener('change', function () {
    var f = this.value; cams.forEach(function (c) { c.hidden = f && c.dataset.client !== f; });
  });
  document.getElementById('cam-cols').addEventListener('change', function () {
    grid.style.gridTemplateColumns = this.value === 'auto' ? '' : 'repeat(' + this.value + ',minmax(0,1fr))';
  });
})();
</script>
<?php require __DIR__ . '/partials/footer.php'; ?>
