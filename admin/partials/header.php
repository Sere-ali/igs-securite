<?php
// Utilisation : $adminRole = null|'superadmin'; $adminActive = 'dashboard'; $adminTitle = '...';
require_once __DIR__ . '/../../includes/bootstrap.php';
$me = require_login($adminRole ?? null);
$B = base_url();
$isSuper = $me['role'] === 'superadmin';
$newCount = (int)db()->query("SELECT COUNT(*) FROM messages WHERE status='nouveau'")->fetchColumn();
$items = [
  'dashboard' => ['📊', 'Tableau de bord', 'index.php'],
  'messages'  => ['✉️', 'Demandes & messages', 'messages.php'],
  'services'  => ['🛡️', 'Services', 'services.php'],
  'profile'   => ['👤', 'Mon compte', 'profile.php'],
];
$superItems = [
  'users'    => ['👥', 'Administrateurs', 'users.php'],
  'settings' => ['⚙️', 'Paramètres du site', 'settings.php'],
  'logs'     => ['📜', 'Journal d\'activité', 'logs.php'],
];
?><!doctype html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="robots" content="noindex,nofollow">
<title><?= e($adminTitle ?? 'Administration') ?> — IGS Sécurité</title>
<link rel="stylesheet" href="<?= $B ?>/assets/css/admin.css"></head>
<body>
<header class="adminbar">
  <div class="ab-row">
    <a class="ab-brand" href="<?= $B ?>/admin/index.php"><img src="<?= $B ?>/assets/img/logo.jpeg" alt=""><span>IGS ADMIN</span></a>
    <div class="ab-me">
      <span class="role <?= $isSuper ? '' : 'admin' ?>"><?= $isSuper ? 'SUPER ADMIN' : 'ADMIN' ?></span>
      <span class="ab-name"><?= e($me['name']) ?></span>
      <a class="btn sm ghost" href="<?= $B ?>/index.php" target="_blank">🌐 Voir le site</a>
      <a class="btn sm danger" href="<?= $B ?>/admin/logout.php">🚪 Déconnexion</a>
    </div>
  </div>
  <nav class="ab-nav" aria-label="Navigation administration">
    <?php foreach ($items as $k => [$ic, $lb, $href]): ?>
      <a href="<?= $B ?>/admin/<?= $href ?>" class="nav-btn <?= ($adminActive ?? '') === $k ? 'on' : '' ?>"><?= $ic ?> <?= e($lb) ?>
        <?php if ($k === 'messages' && $newCount): ?><span class="badge b-nouveau"><?= $newCount ?></span><?php endif; ?></a>
    <?php endforeach; ?>
    <?php if ($isSuper): ?>
      <span class="nav-sep" aria-hidden="true"></span>
      <?php foreach ($superItems as $k => [$ic, $lb, $href]): ?>
        <a href="<?= $B ?>/admin/<?= $href ?>" class="nav-btn super <?= ($adminActive ?? '') === $k ? 'on' : '' ?>"><?= $ic ?> <?= e($lb) ?></a>
      <?php endforeach; ?>
    <?php endif; ?>
  </nav>
</header>
<div class="main">
  <div class="top"><h1><?= e($adminTitle ?? '') ?></h1></div>
  <?php if ($f = flash()): ?><div class="alert <?= $f[1] === 'err' ? 'err' : 'ok' ?>"><?= e($f[0]) ?></div><?php endif; ?>
