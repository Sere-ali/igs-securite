<?php
$adminRole = 'superadmin'; $adminActive = 'logs'; $adminTitle = 'Journal d\'activité';
require __DIR__ . '/partials/header.php';
$rows = db()->query('SELECT l.*, u.name FROM logs l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.id DESC LIMIT 200')->fetchAll();
?>
<div class="panel">
  <p class="muted small">200 dernières actions (connexions, modifications, suppressions).</p>
  <div class="table-wrap"><table>
    <tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Détail</th><th>IP</th></tr>
    <?php foreach ($rows as $l): ?>
      <tr>
        <td class="muted"><?= e(date('d/m/Y H:i:s', strtotime($l['created_at']))) ?></td>
        <td><?= e($l['name'] ?: '—') ?></td>
        <td><strong><?= e($l['action']) ?></strong></td>
        <td class="muted"><?= e($l['detail']) ?></td>
        <td class="muted"><?= e($l['ip']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table></div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
