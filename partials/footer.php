</main>
<?php $wa = preg_replace('/\D+/', '', setting('whatsapp', '0596966667')); ?>
<footer class="site-footer">
  <div class="wrap foot-grid">
    <div>
      <img src="<?= $B ?>/assets/img/logo.jpeg" alt="IGS Sécurité" class="foot-logo" width="110" height="138">
      <p class="muted">Ingénierie Globale de Sûreté — protection physique, sécurité électronique et cyberdéfense.</p>
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
    <a href="<?= $B ?>/admin/login.php" class="admin-link">Espace administration</a>
  </div>
</footer>

<div class="float-actions">
  <a class="fab fab-wa" href="https://wa.me/225<?= e($wa) ?>" target="_blank" rel="noopener" aria-label="WhatsApp">💬</a>
  <a class="fab fab-call" href="<?= tel_link($p1) ?>" aria-label="Appeler IGS Sécurité">📞</a>
</div>
<script src="<?= $B ?>/assets/js/main.js" defer></script>
</body>
</html>
