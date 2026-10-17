<?php
/**
 * Installation : crée les tables, les services par défaut et le compte SUPER ADMINISTRATEUR.
 * À lancer UNE fois : https://votre-site/install?key=VOTRE_INSTALL_KEY
 * Se verrouille automatiquement dès qu'un compte existe en base.
 */
require_once __DIR__ . '/includes/bootstrap.php';

function already_installed(): bool {
    try { return (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0; }
    catch (Throwable $t) { return false; }
}
if (already_installed()) {
    http_response_code(403);
    exit('Installation déjà effectuée.');
}
if (INSTALL_KEY === '') {
    http_response_code(403);
    exit("Définissez la variable d'environnement INSTALL_KEY (Render > Environment) puis rechargez.");
}
if (!hash_equals(INSTALL_KEY, (string)($_GET['key'] ?? $_POST['key'] ?? ''))) {
    http_response_code(403);
    exit("Clé d'installation invalide. Utilisez /install?key=VOTRE_INSTALL_KEY");
}

$error = '';
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Nom et email valides requis.';
    elseif (strlen($pass) < 10) $error = 'Le mot de passe doit contenir au moins 10 caractères.';
    elseif ($pass !== ($_POST['password2'] ?? '')) $error = 'Les mots de passe ne correspondent pas.';
    else {
        try {
            $pdo = db();
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(160) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                role VARCHAR(12) NOT NULL DEFAULT 'admin',
                active SMALLINT NOT NULL DEFAULT 1,
                last_login TIMESTAMP NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )");
            $pdo->exec("CREATE TABLE IF NOT EXISTS services (
                id SERIAL PRIMARY KEY,
                category VARCHAR(60) NOT NULL,
                title VARCHAR(160) NOT NULL,
                description TEXT NOT NULL,
                icon VARCHAR(16) NOT NULL DEFAULT '🛡️',
                sort_order INT NOT NULL DEFAULT 0,
                active SMALLINT NOT NULL DEFAULT 1
            )");
            $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
                id SERIAL PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                phone VARCHAR(40) NOT NULL,
                email VARCHAR(160) NULL,
                service VARCHAR(160) NULL,
                message TEXT NOT NULL,
                status VARCHAR(10) NOT NULL DEFAULT 'nouveau',
                note TEXT NULL,
                ip VARCHAR(45) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )");
            $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
                skey VARCHAR(60) PRIMARY KEY,
                svalue TEXT NOT NULL
            )");
            $pdo->exec("CREATE TABLE IF NOT EXISTS logs (
                id SERIAL PRIMARY KEY,
                user_id INT NULL,
                action VARCHAR(80) NOT NULL,
                detail VARCHAR(500) NULL,
                ip VARCHAR(45) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )");
            $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
                id SERIAL PRIMARY KEY,
                ip VARCHAR(45) NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )");
            $pdo->exec("CREATE INDEX IF NOT EXISTS idx_attempts ON login_attempts (ip, created_at)");

            $st = $pdo->prepare("INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,'superadmin')");
            $st->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);

            $defaults = [
                'phone1' => '05 96 96 66 67',
                'phone2' => '01 73 05 13 86',
                'email'  => 'igssecurite10@gmail.com',
                'address'=> 'Anyama, Boulevard du Stade Olympique',
                'hours'  => '24h/24 – 7j/7',
                'whatsapp' => '0596966667',
                'hero_title' => 'Ingénierie Globale de Sûreté',
                'hero_text'  => "Protection Physique, Sécurité Électronique & Cyberdéfense. Parce que chaque seconde compte, nous déployons une expertise globale pour garantir votre tranquillité 24h/24 et 7j/7.",
            ];
            $ins = $pdo->prepare('INSERT INTO settings (skey,svalue) VALUES (?,?)');
            foreach ($defaults as $k => $v) $ins->execute([$k, $v]);

            $svc = [
              ['Sécurité Physique & Humaine','Agents de Sécurité & Surveillance','Agents qualifiés pour le gardiennage de vos sites industriels, commerciaux et résidentiels.','👮'],
              ['Sécurité Physique & Humaine','Maîtres-Chiens (Unité Cynophile)','Équipes cynophiles dissuasives pour la surveillance renforcée des grandes superficies et zones à risque.','🐕'],
              ['Sécurité Physique & Humaine','Garde du Corps (Protection Rapprochée)','Agents spécialisés pour la protection des personnalités (VIP) et accompagnements sécurisés.','🕴️'],
              ['Sécurité Physique & Humaine','Sécurité Événementielle','Gestion des flux, filtrage, contrôle d\'accès et sécurisation globale de vos manifestations publiques ou privées.','🎟️'],
              ['Sécurité Physique & Humaine','Détectives Privés & Investigations','Enquêtes confidentielles, recherches, filatures et prévention des fraudes pour particuliers et entreprises.','🔍'],
              ['Sécurité Électronique & Technologique','Alarmes sur Intervention','Systèmes d\'alarme anti-intrusion connectés avec déclenchement d\'interventions rapides sur le terrain.','🚨'],
              ['Sécurité Électronique & Technologique','Vidéosurveillance & Télé-surveillance','Caméras HD intelligentes accessibles à distance en temps réel.','📹'],
              ['Sécurité Électronique & Technologique','Puces GPS & Géolocalisation','Traçabilité et suivi en temps réel de vos véhicules, engins et flotte de transport.','📍'],
              ['Sécurité Électronique & Technologique','Protection Périmétrique','Clôtures électriques et barbelés : dispositifs de sécurité physique et dissuasive haute résistance.','⚡'],
              ['Sécurité Électronique & Technologique','Systèmes Solaires Autonomes','Solutions énergétiques garantissant le fonctionnement continu de vos équipements de sécurité, même en cas de coupure de courant.','☀️'],
              ['Sécurité des Réseaux Informatiques','Protection des Réseaux & Données','Sécurisation de votre infrastructure informatique contre les cyberattaques, intrusions et fuites de données.','🔐'],
              ['Sécurité des Réseaux Informatiques','Audits de Réseau','Analyse des vulnérabilités de vos systèmes informatiques et mise en place de pare-feu et protocoles sécurisés.','🖥️'],
              ['Conseil, Audit & Formation','Conseil & Audit de Sécurité','Analyse complète des risques et vulnérabilités de vos installations physiques, électroniques et informatiques.','📋'],
              ['Conseil, Audit & Formation','Formation en Sécurité','Modules de formation professionnelle : gestion des risques, sécurité incendie, prévention et gestion des crises.','🎓'],
            ];
            $is = $pdo->prepare('INSERT INTO services (category,title,description,icon,sort_order) VALUES (?,?,?,?,?)');
            foreach ($svc as $i => $s) $is->execute([$s[0],$s[1],$s[2],$s[3],$i+1]);

            $done = true;
        } catch (Throwable $t) {
            $error = 'Erreur base de données : ' . $t->getMessage() . ' — vérifiez DATABASE_URL.';
        }
    }
}
?><!doctype html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Installation — IGS Sécurité</title>
<link rel="stylesheet" href="assets/css/admin.css"></head>
<body class="auth">
<main class="auth-card">
  <img src="assets/img/logo.jpeg" alt="IGS Sécurité" class="auth-logo">
  <?php if ($done): ?>
    <h1>Installation terminée ✅</h1>
    <p>Le compte super administrateur est créé. La page d'installation est maintenant verrouillée.</p>
    <a class="btn" href="admin/login">Aller à la connexion</a>
  <?php else: ?>
    <h1>Installation</h1>
    <p class="muted">Création de la base et du compte super administrateur.</p>
    <?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <?= csrf_field() ?><input type="hidden" name="key" value="<?= e($_GET['key'] ?? $_POST['key'] ?? '') ?>">
      <label>Nom complet<input name="name" required value="<?= e($_POST['name'] ?? '') ?>"></label>
      <label>Email<input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
      <label>Mot de passe (10 caractères min.)<input type="password" name="password" required minlength="10"></label>
      <label>Confirmer le mot de passe<input type="password" name="password2" required minlength="10"></label>
      <button class="btn" type="submit">Installer</button>
    </form>
  <?php endif; ?>
<script src="assets/js/pwd-eye.js"></script>
</main></body></html>
