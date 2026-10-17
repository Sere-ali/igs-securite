<?php
$active = 'home';
require __DIR__ . '/partials/header.php';
try {
    $cats = [];
    foreach (db()->query('SELECT category, COUNT(*) n FROM services WHERE active=1 GROUP BY category ORDER BY MIN(sort_order)') as $r) $cats[] = $r;
} catch (Throwable $t) { $cats = []; }
$catIcons = ['Sécurité Physique & Humaine'=>'👮','Sécurité Électronique & Technologique'=>'📡','Sécurité des Réseaux Informatiques'=>'💻','Conseil, Audit & Formation'=>'🎓'];
$catText  = [
 'Sécurité Physique & Humaine'=>'Agents, maîtres-chiens, protection rapprochée, événementiel et investigations.',
 'Sécurité Électronique & Technologique'=>'Alarmes, vidéosurveillance, GPS, clôtures électriques et énergie solaire.',
 'Sécurité des Réseaux Informatiques'=>'Protection des réseaux et des données, audits de vulnérabilités.',
 'Conseil, Audit & Formation'=>'Analyse des risques, audit complet et formations professionnelles.',
];
?>
<section class="hero">
  <div class="wrap hero-in">
    <div class="hero-copy reveal">
      <span class="eyebrow">Sécurité privée globale · Anyama</span>
      <h1>IGS SÉCURITÉ<span class="gold-line"><?= e(setting('hero_title', 'Ingénierie Globale de Sûreté')) ?></span></h1>
      <p class="lead"><?= e(setting('hero_text', 'Protection Physique, Sécurité Électronique & Cyberdéfense.')) ?></p>
      <div class="cta-row">
        <a class="btn" href="<?= $B ?>/contact.php#devis">📞 Demander un Devis Gratuit</a>
        <a class="btn btn-ghost" href="<?= $B ?>/services.php">🛡️ Découvrir nos Services</a>
      </div>
      <ul class="hero-badges">
        <li><strong>24/7</strong><span>Disponibilité</span></li>
        <li><strong>3</strong><span>Piliers de sûreté</span></li>
        <li><strong>1</strong><span>Interlocuteur unique</span></li>
      </ul>
    </div>
    <div class="hero-visual reveal">
      <div class="shield-glow"></div>
      <img src="<?= $B ?>/assets/img/logo.jpeg" alt="Blason IGS Sécurité — Chaque second compte" width="520" height="650">
    </div>
  </div>
</section>

<section class="strip">
  <div class="wrap strip-in">
    <span>🛡️ Protection physique</span><span>📡 Sécurité électronique</span><span>💻 Cyber-sûreté</span><span>🎓 Conseil & formation</span>
  </div>
</section>

<section class="sec">
  <div class="wrap two-col">
    <div class="reveal">
      <span class="eyebrow">À propos</span>
      <h2>Une sûreté pensée de bout en bout</h2>
      <p>IGS Sécurité (Ingénierie Globale de Sûreté) est une entreprise spécialisée dans la sécurité privée globale. Nous combinons la présence humaine qualifiée, l'innovation technologique électronique et la sécurité des réseaux informatiques pour offrir une couverture complète aux entreprises, institutions et particuliers.</p>
      <p>Implantés à Anyama (Boulevard du Stade Olympique), nos équipes interviennent avec rigueur, discrétion et réactivité sur l'ensemble du territoire.</p>
      <a class="btn btn-ghost" href="<?= $B ?>/a-propos.php">En savoir plus →</a>
    </div>
    <div class="photo-card reveal">
      <img src="<?= $B ?>/assets/img/agents-portail.jpeg" alt="Agents IGS Sécurité devant un portail de résidence" loading="lazy">
    </div>
  </div>
</section>

<section class="sec sec-dark">
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="eyebrow">Nos expertises</span>
      <h2>Quatre domaines, une seule exigence</h2>
    </div>
    <div class="grid-4">
      <?php foreach ($cats as $c): $n = $c['category']; ?>
        <a class="card cat reveal" href="<?= $B ?>/services.php#<?= e(md5($n)) ?>">
          <div class="ico"><?= $catIcons[$n] ?? '🛡️' ?></div>
          <h3><?= e($n) ?></h3>
          <p><?= e($catText[$n] ?? '') ?></p>
          <span class="more"><?= (int)$c['n'] ?> prestations →</span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="eyebrow">Pourquoi nous choisir</span>
      <h2>Pourquoi choisir IGS Sécurité ?</h2>
    </div>
    <div class="grid-4">
      <div class="why reveal"><b>01</b><h3>Approche globale</h3><p>Un interlocuteur unique pour la sécurité physique, électronique et informatique.</p></div>
      <div class="why reveal"><b>02</b><h3>Réactivité immédiate</h3><p>Notre devise « Chaque seconde compte » guide chacune de nos interventions sur le terrain.</p></div>
      <div class="why reveal"><b>03</b><h3>Discrétion & professionnalisme</h3><p>Des équipes formées et encadrées selon les normes de la sécurité privée.</p></div>
      <div class="why reveal"><b>04</b><h3>Technologies autonomes</h3><p>Des systèmes intégrant l'énergie solaire pour une protection 100 % ininterrompue.</p></div>
    </div>
  </div>
</section>

<section class="sec sec-dark">
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="eyebrow">Sur le terrain</span>
      <h2>Nos équipes en action</h2>
    </div>
    <div class="gallery">
      <figure class="reveal"><img src="<?= $B ?>/assets/img/agents-portail.jpeg" alt="Agents IGS Sécurité devant le portail d'une résidence" loading="lazy"><figcaption>Résidences & villas<small>Gardiennage jour et nuit</small></figcaption></figure>
      <figure class="reveal"><img src="<?= $B ?>/assets/img/chef-equipe.jpeg" alt="Chef d'équipe IGS Sécurité et moto d'intervention" loading="lazy"><figcaption>Chef d'équipe & intervention<small>Moto prête à partir</small></figcaption></figure>
      <figure class="reveal"><img src="<?= $B ?>/assets/img/patrouille-moto.jpeg" alt="Patrouille motorisée IGS Sécurité devant une gare" loading="lazy"><figcaption>Gares & lieux publics<small>Patrouilles motorisées</small></figcaption></figure>
      <figure class="reveal"><img src="<?= $B ?>/assets/img/ecole-lakota.jpg" alt="Agents IGS Sécurité devant une école primaire, entourés d'élèves" loading="lazy"><figcaption>Établissements scolaires<small>Sécurité des élèves à l'entrée et à la sortie</small></figcaption></figure>
    </div>
    <p class="muted center small" style="margin-top:14px">Visuels d’illustration.</p>
  </div>
</section>

<section class="cta-band">
  <div class="wrap cta-in reveal">
    <div>
      <h2>Besoin d'un audit, d'un devis ou d'une intervention rapide ?</h2>
      <p>Nos experts vous répondent 24h/24 – 7j/7.</p>
    </div>
    <div class="cta-row">
      <a class="btn btn-dark" href="<?= tel_link($p1) ?>">📞 +225 <?= e($p1) ?></a>
      <a class="btn btn-ghost-dark" href="<?= $B ?>/contact.php#devis">Écrire un message</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
