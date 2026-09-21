<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();

$pdo = db_or_null();
$orders = [];
$error = null;
$search = trim((string) ($_GET['q'] ?? ''));
$statusFilter = (string) ($_GET['status'] ?? '');
if ($pdo) {
    $where = [];
    $params = [];
    if ($search !== '') {
        $where[] = '(o.order_number LIKE ? OR o.customer_name LIKE ? OR o.phone LIKE ? OR o.whatsapp LIKE ?)';
        $term = '%' . $search . '%';
        array_push($params, $term, $term, $term, $term);
    }
    if (in_array($statusFilter, ['pending', 'delivered', 'cancelled'], true)) {
        $where[] = 'o.status = ?';
        $params[] = $statusFilter;
    }
    $sql = 'SELECT o.*, COUNT(oi.id) item_count FROM orders o LEFT JOIN order_items oi ON oi.order_id = o.id';
    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
    $sql .= ' GROUP BY o.id ORDER BY o.id DESC';
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    $orders = $statement->fetchAll();
} else {
    $error = 'Connexion MySQL indisponible.';
}

$labels = ['pending' => 'En attente', 'delivered' => 'Livrée', 'cancelled' => 'Annulée'];
admin_header('Commandes');
?>
<?php if ($error): ?><div class="alert alert-warning"><?= e($error) ?></div><?php endif; ?>
<section class="admin-panel">
  <div class="admin-panel-heading">
    <h2>Suivi des commandes <span class="text-secondary fs-6">(<?= count($orders) ?>)</span></h2>
    <button class="btn btn-outline-gold btn-sm" type="button" onclick="window.location.reload()"><i class="fas fa-rotate me-1"></i>Actualiser</button>
  </div>
  <form class="row g-2 mb-4" method="get">
    <div class="col-md-6"><input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Rechercher par numéro, nom ou téléphone"></div>
    <div class="col-md-3"><select class="form-select" name="status"><option value="">Tous les statuts</option><?php foreach ($labels as $value => $label): ?><option value="<?= $value ?>" <?= $statusFilter === $value ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3 d-flex gap-2"><button class="btn btn-gold flex-grow-1">Filtrer</button><a class="btn btn-outline-secondary" href="orders.php">Réinitialiser</a></div>
  </form>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>Commande</th><th>Date</th><th>Client</th><th>Mode</th><th>Articles</th><th>Total</th><th>Statut</th><th>Action</th></tr></thead>
      <tbody id="ordersTable">
      <?php if (!$orders): ?><tr><td colspan="8" class="text-secondary">Aucune commande trouvée.</td></tr><?php endif; ?>
      <?php foreach ($orders as $order): ?>
        <tr id="order-row-<?= (int) $order['id'] ?>">
          <td><strong><?= e($order['order_number']) ?></strong></td>
          <td><?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></td>
          <td><?= e($order['customer_name']) ?><small class="d-block text-secondary"><?= e($order['phone']) ?></small></td>
          <td><?= e(['delivery' => 'Livraison', 'takeaway' => 'À emporter', 'onsite' => 'Sur place'][$order['recovery_mode']] ?? $order['recovery_mode']) ?></td>
          <td><?= (int) $order['item_count'] ?></td>
          <td><?= e(money($order['total_amount'])) ?></td>
          <td><select class="form-select form-select-sm status-select" data-order-id="<?= (int) $order['id'] ?>" data-current="<?= e($order['status']) ?>"><option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>En attente</option><option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Livrée</option><option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Annulée</option></select></td>
          <td><a class="btn btn-sm btn-outline-gold" href="order.php?id=<?= (int) $order['id'] ?>">Détails</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<script>
  const csrfToken = <?= json_encode(csrf_token()) ?>;
  document.querySelectorAll('.status-select').forEach((select) => {
    select.addEventListener('change', async () => {
      const previous = select.dataset.current;
      const body = new URLSearchParams({ action: 'status', order_id: select.dataset.orderId, status: select.value, csrf_token: csrfToken });
      try {
        const response = await fetch('api.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' }, body });
        const result = await response.json();
        if (!response.ok || !result.ok) throw new Error(result.message || 'Mise à jour impossible');
        select.dataset.current = select.value;
      } catch (error) {
        select.value = previous;
        alert(error.message);
      }
    });
  });
</script>
<?php admin_footer(); ?>