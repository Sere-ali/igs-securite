<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$B = base_url();
if (current_user()) { header('Location: ' . $B . '/admin/'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $ip = client_ip();
    $pdo = db();
    $pdo->prepare("DELETE FROM login_attempts WHERE created_at < (NOW() - INTERVAL '1 day')")->execute();
    $st = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip=? AND created_at > (NOW() - INTERVAL '15 minutes')");
    $st->execute([$ip]);
    if ((int)$st->fetchColumn() >= 5) {
        $error = 'Trop de tentatives. Réessayez dans 15 minutes.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $st = $pdo->prepare('SELECT * FROM users WHERE email=? AND active=1');
        $st->execute([$email]);
        $u = $st->fetch();
        if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['uid'] = (int)$u['id'];
            $pdo->prepare('UPDATE users SET last_login=NOW() WHERE id=?')->execute([$u['id']]);
            $pdo->prepare('DELETE FROM login_attempts WHERE ip=?')->execute([$ip]);
            log_action('Connexion', $u['email']);
            header('Location: ' . $B . '/admin/'); exit;
        }
        $pdo->prepare('INSERT INTO login_attempts (ip) VALUES (?)')->execute([$ip]);
        log_action('Échec de connexion', $email);
        $error = 'Identifiants incorrects.';
    }
}
?><!doctype html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="robots" content="noindex,nofollow">
<title>Connexion — Administration IGS Sécurité</title>
<link rel="stylesheet" href="<?= $B ?>/assets/css/admin.css"></head>
<body class="auth">
<main class="auth-card">
  <img src="<?= $B ?>/assets/img/logo.jpeg" alt="IGS Sécurité" class="auth-logo">
  <h1>Espace administration</h1>
  <p class="muted">Accès réservé au personnel autorisé.</p>
  <?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <label>Email<input type="email" name="email" required autofocus autocomplete="username"></label>
    <label>Mot de passe<input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn" type="submit">Se connecter</button>
  </form>
  <p class="small muted" style="margin-top:16px"><a href="<?= $B ?>/">← Retour au site</a></p>
</main></body></html>
