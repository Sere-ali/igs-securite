<?php
$adminRole = null; $adminActive = 'dashboard'; $adminTitle = 'Tableau de bord';
require __DIR__ . '/partials/header.php';
$pdo = db();
$count = fn($sql) => (int)$pdo->query($sql)->fetchColumn();
$recent = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC LIMIT 6')->fetchAll();
?>
<div class="stats">
  <div class="stat"><b><?= $count("SELECT COUNT(*) FROM messages WHERE status='nouveau'") ?></b><span>Nouvelles demandes</span></div>
  <div class="stat"><b><?= $count("SELECT COUNT(*) FROM messages WHERE created_at >= (NOW() - INTERVAL '7 days')") ?></b><span>Demandes (7 jours)</span></div>
  <div class="stat"><b><?= $count("SELECT COUNT(*) FROM messages") ?></b><span>Total des demandes</span></div>
  <div class="stat"><b><?= $count("SELECT COUNT(*) FROM services WHERE active=1") ?></b><span>Services en ligne</span></div>
</div>
<div class="panel">
  <h2>Dernières demandes</h2>
  <?php if (!$recent): ?><p class="muted">Aucune demande pour le moment.</p><?php else: ?>
  <div class="table-wrap"><table>
    <tr><th>Date</th><th>Nom</th><th>Téléphone</th><th>Service</th><th>Statut</th><th></th></tr>
    <?php foreach ($recent as $m): ?>
      <tr>
        <td><?= e(date('d/m/Y H:i', strtotime($m['created_at']))) ?></td>
        <td><?= e($m['name']) ?></td>
        <td><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $m['phone'])) ?>"><?= e($m['phone']) ?></a></td>
        <td><?= e($m['service'] ?: '—') ?></td>
        <td><span class="badge b-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
        <td><a class="btn sm ghost" href="messages?view=<?= (int)$m['id'] ?>">Ouvrir</a></td>
      </tr>
    <?php endforeach; ?>
  </table></div>
  <?php endif; ?>
</div>
<?php if ($isSuper): ?>
<div class="panel">
  <h2>Équipe</h2>
  <p class="muted"><?= $count("SELECT COUNT(*) FROM users WHERE role='admin' AND active=1") ?> administrateur(s) actif(s). <a href="users">Gérer les administrateurs →</a></p>
</div>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
