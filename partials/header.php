<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$B = base_url();
$pageTitle = $pageTitle ?? 'IGS Sécurité — Ingénierie Globale de Sûreté';
$pageDesc  = $pageDesc  ?? "IGS Sécurité : sécurité privée globale à Anyama — agents, maîtres-chiens, garde du corps, vidéosurveillance, alarmes, GPS, cybersécurité, audit et formation. Chaque seconde compte.";
$active    = $active ?? 'home';
$p1 = setting('phone1', '05 96 96 66 67');
$p2 = setting('phone2', '01 73 05 13 86');
$mail = setting('email', 'igssecurite10@gmail.com');
$nav = [
  'home'     => ['Accueil', 'index.php'],
  'about'    => ['À Propos', 'a-propos.php'],
  'services' => ['Nos Services', 'services.php'],
  'conseil'  => ['Conseil & Audit', 'conseil.php'],
  'contact'  => ['Contact', 'contact.php'],
];
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<meta name="theme-color" content="#0a0a0a">
<link rel="icon" type="image/png" sizes="48x48" href="<?= $B ?>/assets/icons/favicon-48.png">
<link rel="manifest" href="<?= $B ?>/manifest.webmanifest">
<link rel="apple-touch-icon" href="<?= $B ?>/assets/icons/apple-touch-icon.png">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="IGS Sécurité">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>try{if(!matchMedia("(prefers-reduced-motion:reduce)").matches)document.documentElement.classList.add("anim")}catch(e){}</script>
<link rel="stylesheet" href="<?= $B ?>/assets/css/style.css?v=2">
<link rel="stylesheet" href="<?= $B ?>/assets/css/anim.css?v=2">
</head>
<body>
<header class="site-header" id="top">
  <div class="wrap head-in">
    <a class="brand" href="<?= $B ?>/index.php" aria-label="IGS Sécurité — accueil">
      <img src="<?= $B ?>/assets/img/logo.jpeg" alt="Blason IGS Sécurité" width="56" height="70">
      <span class="brand-txt"><strong>I.G.S SÉCURITÉ</strong><small>Chaque seconde compte</small></span>
    </a>
    <button class="burger" aria-label="Menu" aria-expanded="false" aria-controls="menu"><span></span><span></span><span></span></button>
    <nav id="menu" class="menu">
      <?php foreach ($nav as $k => [$label, $href]): ?>
        <a href="<?= $B . '/' . $href ?>" class="<?= $active === $k ? 'on' : '' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
      <a class="btn btn-sm menu-cta" href="<?= $B ?>/contact.php#devis">Devis gratuit</a>
      <button type="button" class="btn btn-sm btn-install" data-install>📲 Télécharger l'app</button>
    </nav>
  </div>
</header>
<main>
