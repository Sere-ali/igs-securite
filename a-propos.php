<?php
$active = 'about';
$pageTitle = 'À propos — IGS Sécurité';
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero"><div class="wrap">
  <span class="eyebrow">À propos</span>
  <h1>IGS Sécurité, Ingénierie Globale de Sûreté</h1>
  <p class="lead">Élégance, puissance et réactivité au service de votre tranquillité.</p>
</div></section>

<section class="sec"><div class="wrap two-col">
  <div class="reveal">
    <h2>Qui sommes-nous ?</h2>
    <p>IGS Sécurité est une entreprise spécialisée dans la <strong>sécurité privée globale</strong>. Nous combinons la présence humaine qualifiée, l'innovation technologique électronique et la sécurité des réseaux informatiques pour offrir une couverture complète aux entreprises, institutions et particuliers.</p>
    <p>Implantés à <strong>Anyama (Boulevard du Stade Olympique)</strong>, nos équipes interviennent avec rigueur, discrétion et réactivité sur l'ensemble du territoire.</p>
    <p>Notre emblème, l'éléphant, symbolise la force, la mémoire et la protection du troupeau : trois valeurs qui guident chacune de nos missions.</p>
    <ul class="ticks">
      <li>Un interlocuteur unique pour toutes vos problématiques de sûreté</li>
      <li>Des agents formés, équipés et encadrés</li>
      <li>Intervention rapide sur le terrain, patrouilles motorisées</li>
      <li>Disponibilité 24h/24 – 7j/7</li>
    </ul>
  </div>
  <div class="photo-card reveal"><img src="<?= $B ?>/assets/img/chef-equipe.jpeg" alt="Chef d'équipe IGS Sécurité" loading="lazy"></div>
</div></section>

<section class="sec"><div class="wrap two-col">
  <div class="photo-card reveal"><img src="<?= $B ?>/assets/img/ecole-lakota.jpg" alt="Agents IGS Sécurité devant une école primaire" loading="lazy"></div>
  <div class="reveal">
    <span class="eyebrow">Sur tous les terrains</span>
    <h2>Écoles, résidences, gares : une présence adaptée à chaque lieu</h2>
    <p>Aux abords des <strong>établissements scolaires</strong>, nos agents sécurisent l'entrée et la sortie des élèves, orientent les parents et veillent sur les enfants avec calme et bienveillance.</p>
    <p>Dans les <strong>résidences et quartiers</strong>, gardiennage, rondes et contrôle d'accès assurent la tranquillité des familles. Dans les <strong>gares et lieux très fréquentés</strong>, nos patrouilles motorisées interviennent en quelques instants.</p>
    <a class="btn btn-ghost" href="<?= $B ?>/services.php">Voir nos services →</a>
  </div>
</div></section>

<section class="sec sec-dark"><div class="wrap">
  <div class="sec-head reveal"><span class="eyebrow">Nos valeurs</span><h2>Ce qui nous guide</h2></div>
  <div class="grid-4">
    <div class="why reveal"><b>⚡</b><h3>Réactivité</h3><p>« Chaque seconde compte » : une devise, un engagement.</p></div>
    <div class="why reveal"><b>🤝</b><h3>Discrétion</h3><p>Confidentialité totale sur vos sites, vos données et vos personnes.</p></div>
    <div class="why reveal"><b>🎖️</b><h3>Professionnalisme</h3><p>Équipes formées selon les normes de la sécurité privée.</p></div>
    <div class="why reveal"><b>💡</b><h3>Innovation</h3><p>Technologies électroniques, GPS, télé-surveillance et énergie solaire.</p></div>
  </div>
</div></section>
<section class="cta-band"><div class="wrap cta-in reveal">
  <div><h2>Parlons de votre sécurité</h2><p>Devis gratuit et sans engagement.</p></div>
  <div class="cta-row"><a class="btn btn-dark" href="<?= $B ?>/contact.php#devis">Demander un devis</a></div>
</div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
