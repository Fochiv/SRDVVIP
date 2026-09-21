<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();

$pdo = db_or_null();
$order = null;
$items = [];
if ($pdo) {
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if ($id) {
        $statement = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
        $statement->execute([$id]);
        $order = $statement->fetch() ?: null;
        if ($order) {
            $statement = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ? ORDER BY id');
            $statement->execute([$id]);
            $items = $statement->fetchAll();
        }
    }
}
if (!$order) {
    http_response_code(404);
    admin_header('Commande introuvable');
    echo '<div class="alert alert-warning">Cette commande n’existe pas.</div><a class="btn btn-outline-gold" href="orders.php">Retour aux commandes</a>';
    admin_footer();
    exit;
}

$labels = ['pending' => 'En attente', 'delivered' => 'Livrée', 'cancelled' => 'Annulée'];
$modeLabels = ['delivery' => 'Livraison', 'takeaway' => 'À emporter', 'onsite' => 'Sur place'];
$message = "Bonjour SRDVVIP,\n\nConcernant la commande {$order['order_number']} de {$order['customer_name']}.";
$whatsappUrl = 'https://wa.me/' . preg_replace('/\D+/', '', $order['whatsapp']) . '?text=' . rawurlencode($message);
admin_header('Détail de la commande');
?>
<div class="d-flex justify-content-between align-items-center gap-2 mb-4 flex-wrap">
  <div><p class="eyebrow mb-1">Commande enregistrée</p><h2 class="m-0"><?= e($order['order_number']) ?></h2></div>
  <div class="d-flex gap-2"><a class="btn btn-outline-gold" href="orders.php"><i class="fas fa-arrow-left me-1"></i>Retour</a><button class="btn btn-outline-gold" onclick="window.print()"><i class="fas fa-print me-1"></i>Imprimer</button></div>
</div>
<div class="row g-4">
  <div class="col-lg-7">
    <section class="admin-panel mb-4">
      <div class="admin-panel-heading"><h2>Plats commandés</h2><span class="status-pill status-<?= e($order['status']) ?>"><?= e($labels[$order['status']] ?? $order['status']) ?></span></div>
      <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Plat</th><th>Prix unitaire</th><th>Qté</th><th>Sous-total</th></tr></thead><tbody>
      <?php foreach ($items as $item): ?><tr><td><div class="d-flex align-items-center gap-2"><img class="menu-thumb" src="../<?= e($item['image_path']) ?>" alt=""><span><?= e($item['menu_name']) ?></span></div></td><td><?= e(money($item['unit_price'])) ?></td><td><?= (int) $item['quantity'] ?></td><td><?= e(money($item['subtotal'])) ?></td></tr><?php endforeach; ?>
      </tbody><tfoot><tr><th colspan="3" class="text-end">Total final</th><th class="text-warning"><?= e(money($order['total_amount'])) ?></th></tr></tfoot></table></div>
    </section>
  </div>
  <div class="col-lg-5">
    <section class="admin-panel mb-4">
      <h2 class="mb-3">Informations client</h2>
      <dl class="row mb-0 small"><dt class="col-5 text-secondary">Nom</dt><dd class="col-7"><?= e($order['customer_name']) ?></dd><dt class="col-5 text-secondary">Téléphone</dt><dd class="col-7"><?= e($order['phone']) ?></dd><dt class="col-5 text-secondary">WhatsApp</dt><dd class="col-7"><?= e($order['whatsapp']) ?></dd><dt class="col-5 text-secondary">Mode</dt><dd class="col-7"><?= e($modeLabels[$order['recovery_mode']] ?? $order['recovery_mode']) ?></dd><dt class="col-5 text-secondary">Adresse</dt><dd class="col-7"><?= e($order['address'] ?: '—') ?></dd><dt class="col-5 text-secondary">Quartier</dt><dd class="col-7"><?= e($order['district'] ?: '—') ?></dd><dt class="col-5 text-secondary">Date</dt><dd class="col-7"><?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></dd></dl>
    </section>
    <section class="admin-panel">
      <h2 class="mb-3">Actions</h2>
      <a class="btn btn-success w-100 mb-2" href="<?= e($whatsappUrl) ?>" target="_blank"><i class="fab fa-whatsapp me-1"></i>Contacter via WhatsApp</a>
      <label class="form-label mt-2">Modifier le statut</label>
      <select id="detailStatus" class="form-select" data-order-id="<?= (int) $order['id'] ?>"><option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>En attente</option><option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Livrée</option><option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Annulée</option></select>
      <?php if ($order['instructions']): ?><div class="mt-3 p-3 rounded bg-dark-subtle"><strong>Instructions :</strong><br><?= nl2br(e($order['instructions'])) ?></div><?php endif; ?>
    </section>
  </div>
</div>
<script>
  document.getElementById('detailStatus').addEventListener('change', async (event) => {
    const body = new URLSearchParams({ action: 'status', order_id: event.target.dataset.orderId, status: event.target.value, csrf_token: <?= json_encode(csrf_token()) ?> });
    const response = await fetch('api.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' }, body });
    const result = await response.json();
    if (!response.ok || !result.ok) alert(result.message || 'Mise à jour impossible.');
  });
</script>
<?php admin_footer(); ?>