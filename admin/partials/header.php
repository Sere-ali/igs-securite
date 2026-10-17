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
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= e($adminTitle ?? 'Administration') ?> — IGS Sécurité</title>
<link rel="stylesheet" href="<?= $B ?>/assets/css/admin.css"></head>
<body>
<div class="layout">
  <aside class="side" id="side">
    <div class="brand"><img src="<?= $B ?>/assets/img/logo.jpeg" alt=""><span>IGS ADMIN</span></div>
    <nav>
      <?php foreach ($items as $k => [$ic, $lb, $href]): ?>
        <a href="<?= $B ?>/admin/<?= $href ?>" class="<?= ($adminActive ?? '') === $k ? 'on' : '' ?>"><?= $ic ?> <?= e($lb) ?>
          <?php if ($k === 'messages' && $newCount): ?><span class="badge b-nouveau" style="margin-left:auto"><?= $newCount ?></span><?php endif; ?></a>
      <?php endforeach; ?>
      <?php if ($isSuper): ?>
        <div class="sep">Super administrateur</div>
        <?php foreach ($superItems as $k => [$ic, $lb, $href]): ?>
          <a href="<?= $B ?>/admin/<?= $href ?>" class="<?= ($adminActive ?? '') === $k ? 'on' : '' ?>"><?= $ic ?> <?= e($lb) ?></a>
        <?php endforeach; ?>
      <?php endif; ?>
      <div class="sep">Liens</div>
      <a href="<?= $B ?>/index.php" target="_blank">🌐 Voir le site</a>
      <a href="<?= $B ?>/admin/logout.php">🚪 Déconnexion</a>
    </nav>
    <div class="me">
      <span class="role <?= $isSuper ? '' : 'admin' ?>"><?= $isSuper ? 'SUPER ADMIN' : 'ADMIN' ?></span><br>
      <strong><?= e($me['name']) ?></strong><br><span class="muted"><?= e($me['email']) ?></span>
    </div>
  </aside>
  <div class="main">
    <div class="top">
      <div>
        <button class="btn sm ghost menu-toggle" onclick="document.getElementById('side').classList.toggle('open')">☰ Menu</button>
        <h1><?= e($adminTitle ?? '') ?></h1>
      </div>
    </div>
    <?php if ($f = flash()): ?><div class="alert <?= $f[1] === 'err' ? 'err' : 'ok' ?>"><?= e($f[0]) ?></div><?php endif; ?>
