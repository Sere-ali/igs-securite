<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$me = require_login('superadmin');
$pdo = db();
$keys = ['phone1','phone2','email','address','hours','whatsapp','hero_title','hero_text'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = $pdo->prepare('INSERT INTO settings (skey,svalue) VALUES (?,?) ON CONFLICT (skey) DO UPDATE SET svalue=EXCLUDED.svalue');
    foreach ($keys as $k) $st->execute([$k, trim($_POST[$k] ?? '')]);
    log_action('Paramètres modifiés');
    flash('Paramètres enregistrés.');
    header('Location: settings'); exit;
}

$adminRole = 'superadmin'; $adminActive = 'settings'; $adminTitle = 'Paramètres du site';
require __DIR__ . '/partials/header.php';
?>
<div class="panel">
  <form method="post" class="grid-form">
    <?= csrf_field() ?>
    <label>Téléphone principal<input name="phone1" value="<?= e(setting('phone1')) ?>"></label>
    <label>Téléphone secondaire<input name="phone2" value="<?= e(setting('phone2')) ?>"></label>
    <label>Numéro WhatsApp (sans +225)<input name="whatsapp" value="<?= e(setting('whatsapp')) ?>"></label>
    <label>Email de contact (reçoit les notifications)<input type="email" name="email" value="<?= e(setting('email')) ?>"></label>
    <label>Adresse<input name="address" value="<?= e(setting('address')) ?>"></label>
    <label>Disponibilité<input name="hours" value="<?= e(setting('hours')) ?>"></label>
    <label class="full">Titre d'accueil (sous « IGS SÉCURITÉ »)<input name="hero_title" value="<?= e(setting('hero_title')) ?>"></label>
    <label class="full">Texte d'accroche<textarea name="hero_text" rows="3"><?= e(setting('hero_text')) ?></textarea></label>
    <div class="full"><button class="btn" type="submit">Enregistrer</button></div>
  </form>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
