<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$me = require_login('superadmin');
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($act === 'create') {
        $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $pass = $_POST['password'] ?? '';
        $role = ($_POST['role'] ?? 'admin') === 'superadmin' ? 'superadmin' : 'admin';
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) flash('Nom et email valides requis.', 'err');
        elseif (strlen($pass) < 10) flash('Mot de passe : 10 caractères minimum.', 'err');
        else {
            try {
                $pdo->prepare('INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,?)')
                    ->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT), $role]);
                log_action('Utilisateur créé', "$email ($role)"); flash('Compte créé.');
            } catch (PDOException $x) { flash('Cet email existe déjà.', 'err'); }
        }
    }
    elseif ($id === (int)$me['id'] && in_array($act, ['toggle','delete'], true)) {
        flash('Vous ne pouvez pas désactiver ou supprimer votre propre compte.', 'err');
    }
    elseif ($act === 'toggle') {
        $pdo->prepare('UPDATE users SET active = 1 - active WHERE id=?')->execute([$id]);
        log_action('Utilisateur activé/désactivé', '#' . $id); flash('Statut modifié.');
    }
    elseif ($act === 'delete') {
        $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$id]);
        log_action('Utilisateur supprimé', '#' . $id); flash('Compte supprimé.');
    }
    elseif ($act === 'reset') {
        $pass = $_POST['password'] ?? '';
        if (strlen($pass) < 10) flash('Mot de passe : 10 caractères minimum.', 'err');
        else {
            $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($pass, PASSWORD_DEFAULT), $id]);
            log_action('Mot de passe réinitialisé', '#' . $id); flash('Mot de passe réinitialisé.');
        }
    }
    header('Location: users'); exit;
}

$adminRole = 'superadmin'; $adminActive = 'users'; $adminTitle = 'Administrateurs';
require __DIR__ . '/partials/header.php';
$rows = $pdo->query('SELECT * FROM users ORDER BY role DESC, name')->fetchAll();
?>
<div class="panel">
  <h2>Créer un compte</h2>
  <form method="post" class="grid-form" autocomplete="off">
    <?= csrf_field() ?><input type="hidden" name="action" value="create">
    <label>Nom complet<input name="name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Mot de passe (10 car. min.)<input type="text" name="password" required minlength="10" autocomplete="new-password"></label>
    <label>Rôle<select name="role"><option value="admin">Administrateur</option><option value="superadmin">Super administrateur</option></select></label>
    <div class="full"><button class="btn" type="submit">Créer le compte</button></div>
  </form>
</div>
<div class="panel">
  <h2>Comptes (<?= count($rows) ?>)</h2>
  <div class="table-wrap"><table>
    <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Dernière connexion</th><th>Statut</th><th>Actions</th></tr>
    <?php foreach ($rows as $u): ?>
      <tr>
        <td><strong><?= e($u['name']) ?></strong></td><td><?= e($u['email']) ?></td>
        <td><span class="role <?= $u['role'] === 'superadmin' ? '' : 'admin' ?>"><?= $u['role'] === 'superadmin' ? 'SUPER ADMIN' : 'ADMIN' ?></span></td>
        <td class="muted"><?= $u['last_login'] ? e(date('d/m/Y H:i', strtotime($u['last_login']))) : '—' ?></td>
        <td><span class="badge <?= $u['active'] ? 'b-traite' : 'b-off' ?>"><?= $u['active'] ? 'Actif' : 'Désactivé' ?></span></td>
        <td><div class="actions">
          <form method="post" class="actions" onsubmit="var p=prompt('Nouveau mot de passe (10 caractères min.) :');if(!p)return false;this.password.value=p;return true;">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><input type="hidden" name="action" value="reset"><input type="hidden" name="password">
            <button class="btn sm ghost">Réinit. MDP</button></form>
          <?php if ((int)$u['id'] !== (int)$me['id']): ?>
          <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><input type="hidden" name="action" value="toggle"><button class="btn sm ghost"><?= $u['active'] ? 'Désactiver' : 'Activer' ?></button></form>
          <form method="post" onsubmit="return confirm('Supprimer ce compte ?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><input type="hidden" name="action" value="delete"><button class="btn sm danger">Supprimer</button></form>
          <?php endif; ?>
        </div></td>
      </tr>
    <?php endforeach; ?>
  </table></div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
