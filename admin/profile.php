<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$me = require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = db()->prepare('SELECT password_hash FROM users WHERE id=?'); $st->execute([$me['id']]);
    $hash = $st->fetchColumn();
    $new = $_POST['new'] ?? '';
    if (!password_verify($_POST['current'] ?? '', $hash)) flash('Mot de passe actuel incorrect.', 'err');
    elseif (strlen($new) < 10) flash('Nouveau mot de passe : 10 caractères minimum.', 'err');
    elseif ($new !== ($_POST['new2'] ?? '')) flash('La confirmation ne correspond pas.', 'err');
    else {
        db()->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($new, PASSWORD_DEFAULT), $me['id']]);
        log_action('Mot de passe modifié');
        flash('Mot de passe modifié.');
    }
    header('Location: profile'); exit;
}

$adminRole = null; $adminActive = 'profile'; $adminTitle = 'Mon compte';
require __DIR__ . '/partials/header.php';
?>
<div class="panel" style="max-width:520px">
  <p><strong><?= e($me['name']) ?></strong><br><span class="muted"><?= e($me['email']) ?></span></p>
  <h2 style="margin-top:20px">Changer mon mot de passe</h2>
  <form method="post" class="grid-form">
    <?= csrf_field() ?>
    <label class="full">Mot de passe actuel<input type="password" name="current" required autocomplete="current-password"></label>
    <label class="full">Nouveau mot de passe (10 car. min.)<input type="password" name="new" required minlength="10" autocomplete="new-password"></label>
    <label class="full">Confirmer<input type="password" name="new2" required minlength="10" autocomplete="new-password"></label>
    <div class="full"><button class="btn" type="submit">Mettre à jour</button></div>
  </form>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
