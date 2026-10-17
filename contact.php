<?php
require_once __DIR__ . '/includes/bootstrap.php';

$errors = [];
$sent = false;
$old = ['name'=>'','phone'=>'','email'=>'','service'=>trim($_GET['service'] ?? ''),'message'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($old as $k => $_) $old[$k] = trim((string)($_POST[$k] ?? ''));

    if (!empty($_POST['website'])) { exit; }                       // honeypot
    if (time() - ($_SESSION['last_msg'] ?? 0) < 30) $errors[] = 'Merci de patienter quelques secondes avant un nouvel envoi.';
    if (mb_strlen($old['name']) < 2) $errors[] = 'Veuillez indiquer votre nom.';
    if (!preg_match('/^[0-9 +().-]{8,20}$/', $old['phone'])) $errors[] = 'Numéro de téléphone invalide.';
    if ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Adresse email invalide.';
    if (mb_strlen($old['message']) < 10) $errors[] = 'Votre message est trop court.';
    if (mb_strlen($old['message']) > 3000) $errors[] = 'Votre message est trop long.';

    if (!$errors) {
        try {
            $st = db()->prepare('INSERT INTO messages (name,phone,email,service,message,ip) VALUES (?,?,?,?,?,?)');
            $st->execute([
                mb_substr($old['name'],0,120), $old['phone'], $old['email'] ?: null,
                mb_substr($old['service'],0,160) ?: null, $old['message'], client_ip(),
            ]);
            $_SESSION['last_msg'] = time();
            // Notification email (si le serveur autorise mail())
            $to = setting('email', 'igssecurite10@gmail.com');
            $body = "Nouvelle demande via le site IGS Sécurité\n\nNom : {$old['name']}\nTél : {$old['phone']}\nEmail : {$old['email']}\nService : {$old['service']}\n\n{$old['message']}";
            @mail($to, '=?UTF-8?B?' . base64_encode('Nouvelle demande de devis — IGS Sécurité') . '?=', $body,
                  "Content-Type: text/plain; charset=UTF-8\r\nFrom: no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
            $sent = true;
        } catch (Throwable $t) {
            $errors[] = "Une erreur technique est survenue. Appelez-nous directement au +225 " . setting('phone1', '05 96 96 66 67') . ".";
        }
    }
}

$services = [];
try { foreach (db()->query('SELECT title FROM services WHERE active=1 ORDER BY sort_order') as $r) $services[] = $r['title']; } catch (Throwable $t) {}

$active = 'contact';
$pageTitle = 'Contact & Devis gratuit — IGS Sécurité';
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero"><div class="wrap">
  <span class="eyebrow">Contact</span>
  <h1>Prenez contact avec nos experts</h1>
  <p class="lead">Besoin d'un audit, d'un devis ou d'une intervention rapide ?</p>
</div></section>

<section class="sec"><div class="wrap contact-grid">
  <aside class="contact-card reveal">
    <h3>Nos coordonnées</h3>
    <ul class="contact-list big">
      <li><span>📍</span><div><b>Adresse</b><?= e(setting('address')) ?></div></li>
      <li><span>📞</span><div><b>Téléphone / WhatsApp</b><a href="<?= tel_link($p1) ?>">+225 <?= e($p1) ?></a><br><a href="<?= tel_link($p2) ?>">+225 <?= e($p2) ?></a></div></li>
      <li><span>✉️</span><div><b>Email</b><a href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a></div></li>
      <li><span>🕒</span><div><b>Disponibilité</b><?= e(setting('hours')) ?></div></li>
    </ul>
    <a class="btn btn-block" href="<?= tel_link($p1) ?>">📞 Appeler maintenant</a>
    <a class="btn btn-ghost btn-block" target="_blank" rel="noopener" href="https://wa.me/225<?= e(preg_replace('/\D+/', '', setting('whatsapp', '0596966667'))) ?>">💬 Écrire sur WhatsApp</a>
  </aside>

  <div class="form-card reveal" id="devis">
    <h3>Demander un devis gratuit</h3>
    <?php if ($sent): ?>
      <div class="alert ok">✅ Merci ! Votre demande a bien été envoyée. Notre équipe vous recontacte très rapidement.</div>
    <?php else: ?>
      <?php foreach ($errors as $er): ?><div class="alert err"><?= e($er) ?></div><?php endforeach; ?>
      <form method="post" action="#devis" novalidate>
        <?= csrf_field() ?>
        <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div class="row2">
          <label>Nom complet *<input name="name" required value="<?= e($old['name']) ?>" autocomplete="name"></label>
          <label>Téléphone *<input name="phone" type="tel" required value="<?= e($old['phone']) ?>" autocomplete="tel" placeholder="+225 ..."></label>
        </div>
        <div class="row2">
          <label>Email<input name="email" type="email" value="<?= e($old['email']) ?>" autocomplete="email"></label>
          <label>Service souhaité
            <select name="service">
              <option value="">— Choisir —</option>
              <?php foreach ($services as $s): ?>
                <option <?= $old['service'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
              <?php endforeach; ?>
              <option <?= $old['service'] === 'Autre' ? 'selected' : '' ?>>Autre</option>
            </select>
          </label>
        </div>
        <label>Votre message *<textarea name="message" rows="5" required placeholder="Décrivez votre besoin (site à protéger, durée, urgence…)"><?= e($old['message']) ?></textarea></label>
        <button class="btn btn-block" type="submit">Envoyer ma demande</button>
        <p class="muted small">Vos informations sont utilisées uniquement pour répondre à votre demande.</p>
      </form>
    <?php endif; ?>
  </div>
</div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
