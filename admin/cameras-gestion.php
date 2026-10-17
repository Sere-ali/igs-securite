<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/cameras.php';
$me = require_login('superadmin');
ensure_cameras_table();
$pdo = db(); $types = camera_types();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($act === 'save') {
        $url = trim($_POST['stream_url'] ?? '');
        $type = array_key_exists($_POST['stream_type'] ?? '', $types) ? $_POST['stream_type'] : 'hls';
        $f = [trim($_POST['name'] ?? ''), trim($_POST['client'] ?? ''), trim($_POST['location'] ?? ''), trim($_POST['ip'] ?? ''),
              $type, $url, max(1, min(60, (int)($_POST['refresh_sec'] ?? 3))), trim($_POST['notes'] ?? ''), isset($_POST['active']) ? 1 : 0];
        if ($f[0] === '') flash('Le nom de la caméra est obligatoire.', 'err');
        elseif ($url !== '' && !preg_match('#^https?://#i', $url)) flash('L\'adresse du flux doit commencer par https:// (ou http://).', 'err');
        elseif ($id) {
            $pdo->prepare('UPDATE cameras SET name=?,client=?,location=?,ip=?,stream_type=?,stream_url=?,refresh_sec=?,notes=?,active=? WHERE id=?')->execute([...$f, $id]);
            log_action('Caméra modifiée', $f[0]); flash('Caméra mise à jour.');
        } else {
            $pdo->prepare('INSERT INTO cameras (name,client,location,ip,stream_type,stream_url,refresh_sec,notes,active) VALUES (?,?,?,?,?,?,?,?,?)')->execute($f);
            log_action('Caméra ajoutée', $f[0]); flash('Caméra ajoutée.');
        }
    }
    if ($act === 'toggle') { $pdo->prepare('UPDATE cameras SET active = 1 - active WHERE id=?')->execute([$id]); log_action('Caméra activée/désactivée', '#' . $id); }
    if ($act === 'delete') { $pdo->prepare('DELETE FROM cameras WHERE id=?')->execute([$id]); log_action('Caméra supprimée', '#' . $id); flash('Caméra supprimée.'); }
    header('Location: cameras-gestion'); exit;
}

$adminRole = 'superadmin'; $adminActive = 'camgest'; $adminTitle = 'Gestion des caméras';
require __DIR__ . '/partials/header.php';
$edit = ['id'=>0,'name'=>'','client'=>'','location'=>'','ip'=>'','stream_type'=>'hls','stream_url'=>'','refresh_sec'=>3,'notes'=>'','active'=>1];
if (isset($_GET['edit'])) { $st = $pdo->prepare('SELECT * FROM cameras WHERE id=?'); $st->execute([(int)$_GET['edit']]); $edit = $st->fetch() ?: $edit; }
$rows = $pdo->query('SELECT * FROM cameras ORDER BY client, name')->fetchAll();
?>
<div class="panel">
  <h2><?= $edit['id'] ? 'Modifier la caméra' : 'Ajouter une caméra' ?></h2>
  <form method="post" class="grid-form" autocomplete="off">
    <?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
    <label>Nom de la caméra<input name="name" required value="<?= e($edit['name']) ?>" placeholder="Ex. Portail principal"></label>
    <label>Client<input name="client" value="<?= e($edit['client']) ?>" placeholder="Ex. École Lakota"></label>
    <label>Emplacement<input name="location" value="<?= e($edit['location']) ?>" placeholder="Ex. Entrée Nord"></label>
    <label>Adresse IP (pour information)<input name="ip" value="<?= e($edit['ip']) ?>" placeholder="Ex. 197.xxx.xxx.xxx"></label>
    <label class="full">Type de flux<select name="stream_type"><?php foreach ($types as $k => $l): ?><option value="<?= e($k) ?>" <?= $edit['stream_type'] === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></label>
    <label class="full">Adresse du flux vidéo (https://…)<input name="stream_url" value="<?= e($edit['stream_url']) ?>" placeholder="https://cam1.exemple.com/stream.m3u8"></label>
    <label>Rafraîchissement (secondes, pour « images »)<input type="number" name="refresh_sec" min="1" max="60" value="<?= (int)$edit['refresh_sec'] ?>"></label>
    <label><span><input type="checkbox" name="active" <?= $edit['active'] ? 'checked' : '' ?>> Caméra active</span></label>
    <label class="full">Notes internes<textarea name="notes" rows="2"><?= e($edit['notes']) ?></textarea></label>
    <div class="full actions">
      <button class="btn" type="submit"><?= $edit['id'] ? 'Enregistrer' : 'Ajouter' ?></button>
      <?php if ($edit['id']): ?><a class="btn ghost" href="cameras-gestion">Annuler</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="panel">
  <h2>Caméras (<?= count($rows) ?>)</h2>
  <div class="table-wrap"><table>
    <tr><th>Nom</th><th>Client</th><th>IP</th><th>Type</th><th>Statut</th><th>Actions</th></tr>
    <?php foreach ($rows as $c): ?>
      <tr>
        <td><strong><?= e($c['name']) ?></strong><br><span class="muted small"><?= e($c['location']) ?></span></td>
        <td><?= e($c['client']) ?></td><td class="muted"><?= e($c['ip']) ?></td><td class="muted"><?= e(strtoupper($c['stream_type'])) ?></td>
        <td><span class="badge <?= $c['active'] ? 'b-traite' : 'b-off' ?>"><?= $c['active'] ? 'Active' : 'Désactivée' ?></span></td>
        <td><div class="actions">
          <a class="btn sm ghost" href="?edit=<?= (int)$c['id'] ?>">Modifier</a>
          <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><input type="hidden" name="action" value="toggle"><button class="btn sm ghost"><?= $c['active'] ? 'Désactiver' : 'Activer' ?></button></form>
          <form method="post" onsubmit="return confirm('Supprimer cette caméra ?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><input type="hidden" name="action" value="delete"><button class="btn sm danger">Supprimer</button></form>
        </div></td>
      </tr>
    <?php endforeach; ?>
  </table></div>
</div>

<div class="panel help">
  <h2>💡 Comment relier une caméra IGS au site ?</h2>
  <p>Le site est sur Internet : il ne peut pas voir une adresse locale (192.168.x.x), et un navigateur ne lit pas le flux RTSP des caméras. Il faut un petit relais sur le site du client :</p>
  <ol>
    <li><strong>Relais vidéo</strong> : installez <em>go2rtc</em> ou <em>MediaMTX</em> (gratuits) sur le NVR, un mini-PC ou un Raspberry Pi du site. Il convertit le flux RTSP de la caméra en <strong>HLS</strong> (adresse se terminant par <code>.m3u8</code>).</li>
    <li><strong>Accès sécurisé</strong> : publiez ce relais en <strong>https</strong> avec un tunnel (Cloudflare Tunnel ou Tailscale Funnel) — sans ouvrir de port sur la box, sans exposer la caméra directement.</li>
    <li><strong>Ici</strong> : collez l'adresse https du flux ci-dessus. La caméra apparaît dans « Caméras ».</li>
  </ol>
  <p class="muted small">Sécurité : ne mettez jamais l'adresse IP brute d'une caméra avec son mot de passe d'usine sur Internet. Changez les mots de passe des caméras, et préférez un flux protégé (jeton ou mot de passe) côté relais. Chaque consultation est enregistrée dans le journal d'activité.</p>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
