<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verify_csrf($csrf)) {
    json_response(['ok' => false, 'message' => 'Session de sécurité expirée. Rechargez la page.'], 419);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'message' => 'Méthode non autorisée.'], 405);
}

$input = request_json();
$items = is_array($input['items'] ?? null) ? $input['items'] : [];
$name = trim((string) ($input['customer_name'] ?? ''));
$phone = trim((string) ($input['phone'] ?? ''));
$whatsapp = trim((string) ($input['whatsapp'] ?? ''));
$mode = (string) ($input['recovery_mode'] ?? '');
$address = trim((string) ($input['address'] ?? ''));
$district = trim((string) ($input['district'] ?? ''));
$people = trim((string) ($input['people_count'] ?? ''));
$requestedTime = trim((string) ($input['requested_time'] ?? ''));
$instructions = trim((string) ($input['instructions'] ?? ''));

$errors = [];
if (mb_strlen($name) < 2 || mb_strlen($name) > 160) $errors['customer_name'] = 'Entrez votre nom complet.';
if (!preg_match('/^[0-9+\s().-]{8,40}$/', $phone)) $errors['phone'] = 'Entrez un numéro de téléphone valide.';
if (!preg_match('/^[0-9+\s().-]{8,40}$/', $whatsapp)) $errors['whatsapp'] = 'Entrez un numéro WhatsApp valide.';
if (!in_array($mode, ['delivery', 'takeaway', 'onsite'], true)) $errors['recovery_mode'] = 'Choisissez un mode de récupération.';
if ($mode === 'delivery' && mb_strlen($address) < 4) $errors['address'] = 'L’adresse est obligatoire pour une livraison.';
if (mb_strlen($address) > 255 || mb_strlen($district) > 120 || mb_strlen($instructions) > 1000) $errors['details'] = 'Les informations complémentaires sont trop longues.';
if ($requestedTime !== '' && !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $requestedTime)) $errors['requested_time'] = 'L’heure souhaitée est invalide.';
if (mb_strlen($people) > 20) $errors['people_count'] = 'Le nombre de personnes est invalide.';
if (count($items) < 1 || count($items) > 30) $errors['items'] = 'Votre panier est vide ou invalide.';

if ($errors) json_response(['ok' => false, 'message' => 'Vérifiez les informations saisies.', 'errors' => $errors], 422);

try {
    $pdo = db();
    $ids = [];
    $quantities = [];
    foreach ($items as $item) {
        $id = filter_var($item['id'] ?? null, FILTER_VALIDATE_INT);
        $quantity = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || !$quantity || $quantity < 1 || $quantity > 99) {
            json_response(['ok' => false, 'message' => 'Un article du panier est invalide.'], 422);
        }
        $ids[] = $id;
        $quantities[$id] = ($quantities[$id] ?? 0) + $quantity;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $statement = $pdo->prepare("SELECT id, name, price, image_path FROM menus WHERE is_active = 1 AND id IN ($placeholders)");
    $statement->execute($ids);
    $menus = [];
    foreach ($statement->fetchAll() as $menu) $menus[(int) $menu['id']] = $menu;
    if (count($menus) !== count(array_unique($ids))) {
        json_response(['ok' => false, 'message' => 'Un article n’est plus disponible. Actualisez votre panier.'], 409);
    }

    $lineItems = [];
    $total = 0;
    foreach ($quantities as $id => $quantity) {
        $menu = $menus[$id];
        $subtotal = (int) $menu['price'] * $quantity;
        $total += $subtotal;
        $lineItems[] = [
            'id' => $id,
            'name' => $menu['name'],
            'price' => (int) $menu['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'image' => $menu['image_path'],
        ];
    }

    $pdo->beginTransaction();
    $insert = $pdo->prepare(
        'INSERT INTO orders (order_number, customer_name, phone, whatsapp, recovery_mode, address, district, people_count, requested_time, instructions, total_amount)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $insert->execute(['PENDING-' . bin2hex(random_bytes(8)), $name, $phone, $whatsapp, $mode, $address ?: null, $district ?: null, $people ?: null, $requestedTime ?: null, $instructions ?: null, $total]);
    $orderId = (int) $pdo->lastInsertId();
    $orderNumber = sprintf('CMD-%s-%06d', date('Y'), $orderId);
    $pdo->prepare('UPDATE orders SET order_number = ? WHERE id = ?')->execute([$orderNumber, $orderId]);

    $lineInsert = $pdo->prepare(
        'INSERT INTO order_items (order_id, menu_id, menu_name, unit_price, quantity, subtotal, image_path)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    foreach ($lineItems as $line) {
        $lineInsert->execute([$orderId, $line['id'], $line['name'], $line['price'], $line['quantity'], $line['subtotal'], $line['image']]);
    }
    $pdo->commit();

    $modeLabels = ['delivery' => 'Livraison', 'takeaway' => 'À emporter', 'onsite' => 'Sur place'];
    $message = "Bonjour SRDVVIP,\n\nJe souhaite passer la commande suivante :\n\n";
    $message .= "Commande : {$orderNumber}\n";
    foreach ($lineItems as $line) {
        $message .= "• {$line['name']} × {$line['quantity']} : " . money($line['subtotal']) . "\n";
    }
    $message .= "\nTotal : " . money($total);
    $message .= "\nNom : {$name}\nTéléphone : {$phone}\nWhatsApp : {$whatsapp}";
    $message .= "\nMode : " . ($modeLabels[$mode] ?? $mode);
    if ($address) $message .= "\nAdresse : {$address}";
    if ($district) $message .= "\nQuartier : {$district}";
    if ($people) $message .= "\nPersonnes : {$people}";
    if ($requestedTime) $message .= "\nHeure souhaitée : {$requestedTime}";
    if ($instructions) $message .= "\nInstructions : {$instructions}";
    $message .= "\n\nMerci.";

    json_response([
        'ok' => true,
        'order_number' => $orderNumber,
        'whatsapp_url' => 'https://wa.me/237659763338?text=' . rawurlencode($message),
    ]);
} catch (Throwable $error) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
    error_log('SRDVVIP order error: ' . $error->getMessage());
    json_response(['ok' => false, 'message' => 'Le service de commande est momentanément indisponible.'], 503);
}