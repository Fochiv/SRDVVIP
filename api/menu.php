<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['ok' => false, 'message' => 'Méthode non autorisée.'], 405);
}

try {
    $menus = db()->query(
        'SELECT m.id, m.name, m.description, m.price, m.image_path, m.is_active, c.slug category
         FROM menus m JOIN categories c ON c.id = m.category_id
         ORDER BY m.display_order, m.id'
    )->fetchAll();
    json_response(['ok' => true, 'menus' => $menus]);
} catch (Throwable) {
    // The imported static menu remains usable while MySQL is being configured.
    json_response(['ok' => false, 'menus' => [], 'message' => 'Menu dynamique indisponible.'], 503);
}