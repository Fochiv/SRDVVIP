<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();

$stats = ['menus' => 0, 'pending' => 0, 'delivered' => 0, 'today' => 0];
$recent = [];
$dbError = null;
try {
    $pdo = db();
    $stats['menus'] = (int) $pdo->query('SELECT COUNT(*) FROM menus WHERE is_active = 1')->fetchColumn();
    $stats['pending'] = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    $stats['delivered'] = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered'")->fetchColumn();
    $stats['today'] = (int) $pdo->query('SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURRENT_DATE')->fetchColumn();
    $recent = $pdo->query('SELECT id, order_number, customer_name, total_amount, status, created_at FROM orders ORDER BY id DESC LIMIT 8')->fetchAll();
} catch (Throwable $error) {
    $dbError = 'Connexion à la base indisponible. Vérifiez la configuration de DATABASE_URL ou des variables DB_*.';
}

admin_header('Tableau de bord');
?>
<?php if ($dbError): ?><div class="alert alert-warning"><?= e($dbError) ?></div><?php endif; ?>
<section class="stats-grid">
  <article class="stat-card"><div><span>Menus disponibles</span><strong><?= $stats['menus'] ?></strong></div><i class="fas fa-utensils"></i></article>
  <article class="stat-card"><div><span>Commandes en attente</span><strong><?= $stats['pending'] ?></strong></div><i class="fas fa-hourglass-half"></i></article>
  <article class="stat-card"><div><span>Commandes livrées</span><strong><?= $stats['delivered'] ?></strong></div><i class="fas fa-circle-check"></i></article>
  <article class="stat-card"><div><span>Commandes du jour</span><strong><?= $stats['today'] ?></strong></div><i class="fas fa-calendar-day"></i></article>
</section>

<section class="admin-panel">
  <div class="admin-panel-heading">
    <h2>Dernières commandes</h2>
    <a class="btn btn-outline-gold btn-sm" href="orders.php">Voir toutes les commandes</a>
  </div>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>Commande</th><th>Client</th><th>Total</th><th>Statut</th><th>Date</th><th></th></tr></thead>
      <tbody>
      <?php if (!$recent): ?><tr><td colspan="6" class="text-secondary">Aucune commande enregistrée.</td></tr><?php endif; ?>
      <?php foreach ($recent as $order): ?>
        <tr>
          <td><strong><?= e($order['order_number']) ?></strong></td>
          <td><?= e($order['customer_name']) ?></td>
          <td><?= e(money($order['total_amount'])) ?></td>
          <td><span class="status-pill status-<?= e($order['status']) ?>"><?= e(['pending' => 'En attente', 'delivered' => 'Livrée', 'cancelled' => 'Annulée'][$order['status']] ?? $order['status']) ?></span></td>
          <td><?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></td>
          <td><a class="btn btn-sm btn-outline-gold" href="order.php?id=<?= (int) $order['id'] ?>">Détails</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php admin_footer(); ?>