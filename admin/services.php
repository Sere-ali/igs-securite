<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$me = require_login();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($act === 'save') {
        $f = [trim($_POST['category'] ?? ''), trim($_POST['title'] ?? ''), trim($_POST['description'] ?? ''),
              trim($_POST['icon'] ?? '🛡️') ?: '🛡️', (int)($_POST['sort_order'] ?? 0), isset($_POST['active']) ? 1 : 0];
        if ($f[0] === '' || $f[1] === '' || $f[2] === '') { flash('Catégorie, titre et description sont obligatoires.', 'err'); }
        elseif ($id) {
            $pdo->prepare('UPDATE services SET category=?,title=?,description=?,icon=?,sort_order=?,active=? WHERE id=?')->execute([...$f, $id]);
            log_action('Service modifié', $f[1]); flash('Service mis à jour.');
        } else {
            $pdo->prepare('INSERT INTO services (category,title,description,icon,sort_order,active) VALUES (?,?,?,?,?,?)')->execute($f);
            log_action('Service créé', $f[1]); flash('Service ajouté.');
        }
    }
    if ($act === 'toggle') {
        $pdo->prepare('UPDATE services SET active = 1 - active WHERE id=?')->execute([$id]);
        log_action('Service activé/désactivé', '#' . $id);
    }
    if ($act === 'delete') {
        $pdo->prepare('DELETE FROM services WHERE id=?')->execute([$id]);
        log_action('Service supprimé', '#' . $id); flash('Service supprimé.');
    }
    header('Location: services.php'); exit;
}

$adminRole = null; $adminActive = 'services'; $adminTitle = 'Gestion des services';
require __DIR__ . '/partials/header.php';

$edit = ['id'=>0,'category'=>'','title'=>'','description'=>'','icon'=>'🛡️','sort_order'=>0,'active'=>1];
if (isset($_GET['edit'])) {
    $st = $pdo->prepare('SELECT * FROM services WHERE id=?'); $st->execute([(int)$_GET['edit']]);
    $edit = $st->fetch() ?: $edit;
}
$cats = $pdo->query('SELECT DISTINCT category FROM services ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
$rows = $pdo->query('SELECT * FROM services ORDER BY sort_order, id')->fetchAll();
?>
<div class="panel">
  <h2><?= $edit['id'] ? 'Modifier le service' : 'Ajouter un service' ?></h2>
  <form method="post" class="grid-form">
    <?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
    <label>Catégorie<input name="category" list="cats" required value="<?= e($edit['category']) ?>">
      <datalist id="cats"><?php foreach ($cats as $c): ?><option value="<?= e($c) ?>"><?php endforeach; ?></datalist></label>
    <label>Titre<input name="title" required value="<?= e($edit['title']) ?>"></label>
    <label class="full">Description<textarea name="description" rows="3" required><?= e($edit['description']) ?></textarea></label>
    <label>Icône (emoji)<input name="icon" maxlength="8" value="<?= e($edit['icon']) ?>"></label>
    <label>Position (ordre d'affichage)<input type="number" name="sort_order" value="<?= (int)$edit['sort_order'] ?>"></label>
    <label><span><input type="checkbox" name="active" <?= $edit['active'] ? 'checked' : '' ?>> Visible sur le site</span></label>
    <div class="full actions">
      <button class="btn" type="submit"><?= $edit['id'] ? 'Enregistrer' : 'Ajouter' ?></button>
      <?php if ($edit['id']): ?><a class="btn ghost" href="services.php">Annuler</a><?php endif; ?>
    </div>
  </form>
</div>
<div class="panel">
  <h2>Services (<?= count($rows) ?>)</h2>
  <div class="table-wrap"><table>
    <tr><th>#</th><th></th><th>Titre</th><th>Catégorie</th><th>Statut</th><th>Actions</th></tr>
    <?php foreach ($rows as $s): ?>
      <tr>
        <td><?= (int)$s['sort_order'] ?></td><td><?= e($s['icon']) ?></td>
        <td><strong><?= e($s['title']) ?></strong></td><td class="muted"><?= e($s['category']) ?></td>
        <td><span class="badge <?= $s['active'] ? 'b-traite' : 'b-off' ?>"><?= $s['active'] ? 'En ligne' : 'Masqué' ?></span></td>
        <td><div class="actions">
          <a class="btn sm ghost" href="?edit=<?= (int)$s['id'] ?>">Modifier</a>
          <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$s['id'] ?>"><input type="hidden" name="action" value="toggle"><button class="btn sm ghost"><?= $s['active'] ? 'Masquer' : 'Afficher' ?></button></form>
          <form method="post" onsubmit="return confirm('Supprimer ce service ?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$s['id'] ?>"><input type="hidden" name="action" value="delete"><button class="btn sm danger">Supprimer</button></form>
        </div></td>
      </tr>
    <?php endforeach; ?>
  </table></div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
