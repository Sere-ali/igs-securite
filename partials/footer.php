</main>
<?php $wa = preg_replace('/\D+/', '', setting('whatsapp', '0596966667')); ?>
<footer class="site-footer">
  <div class="wrap foot-grid">
    <div>
      <img src="<?= $B ?>/assets/img/logo.jpeg" alt="IGS Sécurité" class="foot-logo" width="110" height="138">
      <p class="muted">Ingénierie Globale de Sûreté — protection physique, sécurité électronique et cyberdéfense.</p>
      <button type="button" class="btn btn-sm btn-install" data-install>📲 Télécharger l'application</button>
    </div>
    <div>
      <h4>Navigation</h4>
      <ul>
        <?php foreach ($nav as $k => [$label, $href]): ?>
          <li><a href="<?= $B . '/' . $href ?>"><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h4>Nos expertises</h4>
      <ul>
        <li>Sécurité physique & humaine</li>
        <li>Sécurité électronique</li>
        <li>Cyber-sûreté</li>
        <li>Conseil, audit & formation</li>
      </ul>
    </div>
    <div>
      <h4>Contact</h4>
      <ul class="contact-list">
        <li>📍 <?= e(setting('address', 'Anyama, Boulevard du Stade Olympique')) ?></li>
        <li>📞 <a href="<?= tel_link($p1) ?>">+225 <?= e($p1) ?></a></li>
        <li>📞 <a href="<?= tel_link($p2) ?>">+225 <?= e($p2) ?></a></li>
        <li>✉️ <a href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a></li>
        <li>🕒 <?= e(setting('hours', '24h/24 – 7j/7')) ?></li>
      </ul>
    </div>
  </div>
  <div class="wrap foot-bottom">
    <span>© <?= date('Y') ?> I.G.S Sécurité — Tous droits réservés.</span>
    <a href="<?= $B ?>/admin/login" class="admin-link">Espace administration</a>
  </div>
</footer>

<div class="float-actions">
  <a class="fab fab-wa" href="https://wa.me/225<?= e($wa) ?>" target="_blank" rel="noopener" aria-label="WhatsApp">💬</a>
  <a class="fab fab-call" href="<?= tel_link($p1) ?>" aria-label="Appeler IGS Sécurité">📞</a>
</div>
<div class="modal" id="install-modal" role="dialog" aria-modal="true" aria-labelledby="im-title">
  <div class="modal-box">
    <button class="modal-close" type="button" aria-label="Fermer" data-close>&times;</button>
    <h3 id="im-title">Installer l'application</h3>
    <div id="im-ios" hidden>
      <p>Sur iPhone / iPad (Safari) :</p>
      <ol><li>Touchez le bouton <strong>Partager</strong> (carré avec une flèche) en bas de l'écran.</li><li>Choisissez <strong>« Sur l'écran d'accueil »</strong>.</li><li>Touchez <strong>Ajouter</strong>.</li></ol>
    </div>
    <div id="im-other" hidden>
      <p>Depuis votre navigateur :</p>
      <ol><li>Ouvrez le menu du navigateur (⋮ ou ⋯).</li><li>Choisissez <strong>« Installer l'application »</strong> ou <strong>« Ajouter à l'écran d'accueil »</strong>.</li><li>Confirmez.</li></ol>
      <p class="muted small">Sur Android, utilisez Chrome. Sur iPhone, utilisez Safari.</p>
    </div>
    <button type="button" class="btn btn-block" data-close>Compris</button>
  </div>
</div>
<script>window.IGS_BASE=<?= json_encode($B) ?>;</script>
<script src="<?= $B ?>/assets/js/anim.js?v=2" defer></script>
<script src="<?= $B ?>/assets/js/main.js?v=2" defer></script>
</body>
</html>
