<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'counts') {
    $pdo = db_or_null();
    $pending = 0;
    $latest = null;
    if ($pdo) {
        $pending = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
        $latest = $pdo->query('SELECT order_number, created_at FROM orders ORDER BY id DESC LIMIT 1')->fetch() ?: null;
    }
    json_response(['pending' => $pending, 'latest' => $latest]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'status') {
    require_csrf();
    $status = (string) ($_POST['status'] ?? '');
    $orderId = filter_var($_POST['order_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$orderId || !in_array($status, ['pending', 'delivered', 'cancelled'], true)) {
        json_response(['ok' => false, 'message' => 'Données invalides.'], 422);
    }
    try {
        $statement = db()->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $statement->execute([$status, $orderId]);
        json_response(['ok' => true]);
    } catch (Throwable) {
        json_response(['ok' => false, 'message' => 'Mise à jour impossible.'], 503);
    }
}

json_response(['ok' => false, 'message' => 'Action inconnue.'], 404);