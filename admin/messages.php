<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$me = require_login();
$pdo = db();

// Export CSV
if (($_GET['export'] ?? '') === 'csv') {
    log_action('Export CSV des messages');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="demandes-igs-' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Date','Nom','Téléphone','Email','Service','Message','Statut'], ';');
    foreach ($pdo->query('SELECT * FROM messages ORDER BY created_at DESC') as $m)
        fputcsv($out, [$m['created_at'],$m['name'],$m['phone'],$m['email'],$m['service'],$m['message'],$m['status']], ';');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($act === 'status' && in_array($_POST['status'] ?? '', ['nouveau','lu','traite'], true)) {
        $pdo->prepare('UPDATE messages SET status=?, note=? WHERE id=?')->execute([$_POST['status'], trim($_POST['note'] ?? '') ?: null, $id]);
        log_action('Message #' . $id, 'statut → ' . $_POST['status']);
        flash('Demande mise à jour.');
        header('Location: messages?view=' . $id); exit;
    }
    if ($act === 'delete') {
        $pdo->prepare('DELETE FROM messages WHERE id=?')->execute([$id]);
        log_action('Message supprimé', '#' . $id);
        flash('Demande supprimée.');
        header('Location: messages'); exit;
    }
}

$adminRole = null; $adminActive = 'messages'; $adminTitle = 'Demandes & messages';
require __DIR__ . '/partials/header.php';

// Détail
if (isset($_GET['view'])):
    $st = $pdo->prepare('SELECT * FROM messages WHERE id=?'); $st->execute([(int)$_GET['view']]);
    $m = $st->fetch();
    if (!$m) { echo '<p class="muted">Demande introuvable.</p>'; }
    else {
        if ($m['status'] === 'nouveau') {
            $pdo->prepare("UPDATE messages SET status='lu' WHERE id=?")->execute([$m['id']]);
            $m['status'] = 'lu';
        }
?>
<p><a href="messages">← Retour à la liste</a></p>
<div class="panel">
  <h2><?= e($m['name']) ?> <span class="badge b-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></h2>
  <p class="muted small">Reçue le <?= e(date('d/m/Y à H:i', strtotime($m['created_at']))) ?> — IP <?= e($m['ip']) ?></p>
  <p>📞 <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $m['phone'])) ?>"><?= e($m['phone']) ?></a>
     <?php if ($m['email']): ?> · ✉️ <a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a><?php endif; ?></p>
  <p><strong>Service :</strong> <?= e($m['service'] ?: '—') ?></p>
  <div class="msg-body"><?= e($m['message']) ?></div>
  <form method="post" class="grid-form">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><input type="hidden" name="action" value="status">
    <label>Statut
      <select name="status">
        <?php foreach (['nouveau'=>'Nouveau','lu'=>'Lu','traite'=>'Traité'] as $k => $v): ?>
          <option value="<?= $k ?>" <?= $m['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select></label>
    <label class="full">Note interne<textarea name="note" rows="3"><?= e($m['note']) ?></textarea></label>
    <div class="full actions"><button class="btn" type="submit">Enregistrer</button></div>
  </form>
  <form method="post" onsubmit="return confirm('Supprimer définitivement cette demande ?')" style="margin-top:14px">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><input type="hidden" name="action" value="delete">
    <button class="btn sm danger" type="submit">Supprimer</button>
  </form>
</div>
<?php }
else:
    $filter = $_GET['s'] ?? '';
    $where = in_array($filter, ['nouveau','lu','traite'], true) ? 'WHERE status=' . $pdo->quote($filter) : '';
    $page = max(1, (int)($_GET['p'] ?? 1)); $per = 20;
    $total = (int)$pdo->query("SELECT COUNT(*) FROM messages $where")->fetchColumn();
    $rows = $pdo->query("SELECT * FROM messages $where ORDER BY created_at DESC LIMIT $per OFFSET " . (($page-1)*$per))->fetchAll();
    $pages = max(1, (int)ceil($total / $per));
?>
<div class="filters">
  <a href="messages" class="<?= $filter === '' ? 'on' : '' ?>">Toutes</a>
  <a href="?s=nouveau" class="<?= $filter === 'nouveau' ? 'on' : '' ?>">Nouvelles</a>
  <a href="?s=lu" class="<?= $filter === 'lu' ? 'on' : '' ?>">Lues</a>
  <a href="?s=traite" class="<?= $filter === 'traite' ? 'on' : '' ?>">Traitées</a>
  <a href="?export=csv" style="margin-left:auto">⬇️ Export CSV</a>
</div>
<div class="panel">
  <?php if (!$rows): ?><p class="muted">Aucune demande.</p><?php else: ?>
  <div class="table-wrap"><table>
    <tr><th>Date</th><th>Nom</th><th>Téléphone</th><th>Service</th><th>Statut</th><th></th></tr>
    <?php foreach ($rows as $m): ?>
      <tr>
        <td><?= e(date('d/m/Y H:i', strtotime($m['created_at']))) ?></td>
        <td><strong><?= e($m['name']) ?></strong></td>
        <td><?= e($m['phone']) ?></td>
        <td><?= e($m['service'] ?: '—') ?></td>
        <td><span class="badge b-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
        <td><a class="btn sm ghost" href="?view=<?= (int)$m['id'] ?>">Ouvrir</a></td>
      </tr>
    <?php endforeach; ?>
  </table></div>
  <?php if ($pages > 1): ?><div class="pager">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <?= $i === $page ? "<span>$i</span>" : '<a href="?p=' . $i . ($filter ? '&s=' . e($filter) : '') . '">' . $i . '</a>' ?>
    <?php endfor; ?></div><?php endif; ?>
  <?php endif; ?>
</div>
<?php endif; require __DIR__ . '/partials/footer.php'; ?>
