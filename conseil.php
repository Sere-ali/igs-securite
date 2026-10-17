<?php
$active = 'conseil';
$pageTitle = 'Conseil, Audit & Formation — IGS Sécurité';
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero"><div class="wrap">
  <span class="eyebrow">Conseil & Audit</span>
  <h1>Anticipez les risques avant qu'ils n'arrivent</h1>
  <p class="lead">Analyse complète de vos installations physiques, électroniques et informatiques.</p>
</div></section>

<section class="sec"><div class="wrap">
  <div class="grid-2">
    <article class="card big reveal">
      <div class="ico">📋</div>
      <h3>Conseil & Audit de Sécurité</h3>
      <p>Analyse complète des risques et vulnérabilités de vos installations physiques, électroniques et informatiques, suivie de recommandations priorisées et chiffrées.</p>
      <ul class="ticks">
        <li>Visite du site et cartographie des risques</li>
        <li>Audit des dispositifs existants (accès, alarmes, vidéo)</li>
        <li>Audit réseau : pare-feu, protocoles, vulnérabilités</li>
        <li>Plan d'action et accompagnement à la mise en œuvre</li>
      </ul>
    </article>
    <article class="card big reveal">
      <div class="ico">🎓</div>
      <h3>Formation en Sécurité</h3>
      <p>Modules de formation professionnelle pour vos équipes : gestion des risques, sécurité incendie, prévention et gestion des crises.</p>
      <ul class="ticks">
        <li>Gestion des risques et des situations de crise</li>
        <li>Sécurité incendie et évacuation</li>
        <li>Prévention et bonnes pratiques</li>
        <li>Sessions sur site ou dans vos locaux</li>
      </ul>
    </article>
  </div>
</div></section>

<section class="sec sec-dark"><div class="wrap">
  <div class="sec-head reveal"><span class="eyebrow">Notre méthode</span><h2>Un audit en quatre étapes</h2></div>
  <div class="grid-4">
    <div class="why reveal"><b>01</b><h3>Écoute</h3><p>Compréhension de vos enjeux, de vos sites et de vos contraintes.</p></div>
    <div class="why reveal"><b>02</b><h3>Analyse</h3><p>Inspection terrain, tests et identification des vulnérabilités.</p></div>
    <div class="why reveal"><b>03</b><h3>Recommandations</h3><p>Rapport clair, solutions hiérarchisées par urgence et par budget.</p></div>
    <div class="why reveal"><b>04</b><h3>Mise en œuvre</h3><p>Déploiement des solutions et suivi dans le temps.</p></div>
  </div>
</div></section>
<section class="cta-band"><div class="wrap cta-in reveal">
  <div><h2>Planifiez votre audit</h2><p>Première prise de contact gratuite.</p></div>
  <div class="cta-row"><a class="btn btn-dark" href="<?= $B ?>/contact.php?service=Conseil+%26+Audit+de+S%C3%A9curit%C3%A9#devis">Demander un audit</a></div>
</div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
