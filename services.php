<?php
$active = 'services';
$pageTitle = 'Nos Services — IGS Sécurité';
require __DIR__ . '/partials/header.php';
$groups = [];
try {
    foreach (db()->query('SELECT * FROM services WHERE active=1 ORDER BY sort_order, id') as $s) $groups[$s['category']][] = $s;
} catch (Throwable $t) {}
?>
<section class="page-hero"><div class="wrap">
  <span class="eyebrow">Nos services</span>
  <h1>Une couverture complète de votre sûreté</h1>
  <p class="lead">Protection humaine, technologies électroniques, cyber-sûreté, conseil et formation.</p>
</div></section>

<?php $i = 0; foreach ($groups as $cat => $items): $i++; ?>
<section class="sec <?= $i % 2 === 0 ? 'sec-dark' : '' ?>" id="<?= e(md5($cat)) ?>"><div class="wrap">
  <div class="sec-head reveal"><span class="eyebrow">0<?= $i ?></span><h2><?= e($cat) ?></h2></div>
  <div class="grid-3">
    <?php foreach ($items as $s): ?>
      <article class="card svc reveal">
        <div class="ico"><?= e($s['icon']) ?></div>
        <h3><?= e($s['title']) ?></h3>
        <p><?= e($s['description']) ?></p>
        <a class="more" href="<?= $B ?>/contact?service=<?= urlencode($s['title']) ?>#devis">Demander un devis →</a>
      </article>
    <?php endforeach; ?>
  </div>
</div></section>
<?php endforeach; ?>

<section class="cta-band"><div class="wrap cta-in reveal">
  <div><h2>Un besoin spécifique ?</h2><p>Nous construisons une solution sur mesure.</p></div>
  <div class="cta-row"><a class="btn btn-dark" href="<?= $B ?>/contact#devis">Demander un devis gratuit</a></div>
</div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
