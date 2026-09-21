<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();

$pdo = db_or_null();
$error = null;
$editing = null;
$categories = [];
$menus = [];
$menuFormOpen = false;
$categoryFormOpen = false;
if ($pdo) {
    $categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
    $menus = $pdo->query('SELECT m.*, c.name category_name FROM menus m JOIN categories c ON c.id = m.category_id ORDER BY m.display_order, m.id')->fetchAll();
} else {
    $error = 'Connexion à la base indisponible.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    require_csrf();
    $action = (string) ($_POST['action'] ?? '');
    $menuFormOpen = $action === 'save';
    $categoryFormOpen = $action === 'category';
    try {
        if ($action === 'category') {
            $name = trim((string) ($_POST['name'] ?? ''));
            if (mb_strlen($name) < 2) throw new RuntimeException('Le nom de catégorie est trop court.');
            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $name)), '-'));
            $statement = $pdo->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)');
            $statement->execute([$name, $slug]);
            flash('success', 'Catégorie ajoutée.');
        } elseif ($action === 'delete_category') {
            $id = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT);
            $statement = $pdo->prepare('DELETE FROM categories WHERE id = ? AND NOT EXISTS (SELECT 1 FROM menus WHERE category_id = categories.id)');
            $statement->execute([$id]);
            flash($statement->rowCount() ? 'success' : 'warning', $statement->rowCount() ? 'Catégorie supprimée.' : 'Cette catégorie contient encore des menus.');
        } elseif ($action === 'delete') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            $pdo->prepare('UPDATE menus SET is_active = 0 WHERE id = ?')->execute([$id]);
            flash('success', 'Menu désactivé. Il reste conservé dans la base.');
        } elseif ($action === 'save') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: null;
            $categoryId = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT);
            $name = trim((string) ($_POST['name'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_INT);
            $displayOrder = filter_var($_POST['display_order'] ?? 0, FILTER_VALIDATE_INT);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            if (!$categoryId || mb_strlen($name) < 2 || !$price || $price < 1) throw new RuntimeException('Vérifiez le nom, la catégorie et le prix.');
            $imagePath = trim((string) ($_POST['image_path'] ?? ''));
            if (!empty($_FILES['image']['name'])) {
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['image']['tmp_name']);
                $allowedMime = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
                if (($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !in_array($extension, $allowed, true) || ($allowedMime[$extension] ?? '') !== $mime || ($_FILES['image']['size'] ?? 0) > 5 * 1024 * 1024) throw new RuntimeException('Image invalide (JPG, JPEG, PNG ou WebP, 5 Mo maximum).');
                $uploadDir = dirname(__DIR__) . '/uploads/menu';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $filename = bin2hex(random_bytes(12)) . '.' . $extension;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . '/' . $filename)) throw new RuntimeException('Impossible d’enregistrer l’image.');
                $imagePath = 'uploads/menu/' . $filename;
            }
            if ($imagePath === '') throw new RuntimeException('Ajoutez une image ou un chemin d’image.');
            if ($id) {
                $statement = $pdo->prepare('UPDATE menus SET category_id=?, name=?, description=?, price=?, image_path=?, is_active=?, display_order=? WHERE id=?');
                $statement->execute([$categoryId, $name, $description, $price, $imagePath, $isActive, $displayOrder, $id]);
                flash('success', 'Menu mis à jour.');
            } else {
                $statement = $pdo->prepare('INSERT INTO menus (category_id, name, description, price, image_path, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $statement->execute([$categoryId, $name, $description, $price, $imagePath, $isActive, $displayOrder]);
                flash('success', 'Menu ajouté.');
            }
        }
        header('Location: menus.php');
        exit;
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

if (isset($_GET['edit']) && $pdo) {
    $statement = $pdo->prepare('SELECT * FROM menus WHERE id = ?');
    $statement->execute([filter_var($_GET['edit'], FILTER_VALIDATE_INT)]);
    $editing = $statement->fetch() ?: null;
}
$menuFormOpen = $menuFormOpen || $editing !== null;

admin_header('Menus & catégories');
?>
<?php if ($error): ?><div class="alert alert-warning"><?= e($error) ?></div><?php endif; ?>
<div class="row g-4">
  <div class="col-xl-5">
    <section class="admin-panel">
      <div class="admin-panel-heading"><h2><?= $editing ? 'Modifier le menu' : 'Ajouter un menu' ?></h2><?php if ($editing): ?><a href="menus.php" class="btn btn-sm btn-outline-gold">Annuler</a><?php endif; ?></div>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
        <label class="form-label">Nom du plat *</label>
        <input class="form-control mb-3" name="name" required value="<?= e($editing['name'] ?? '') ?>">
        <label class="form-label">Description</label>
        <textarea class="form-control mb-3" name="description" rows="3"><?= e($editing['description'] ?? '') ?></textarea>
        <div class="row g-3">
          <div class="col-6"><label class="form-label">Prix (FCFA) *</label><input class="form-control" type="number" min="1" name="price" required value="<?= (int) ($editing['price'] ?? 1000) ?>"></div>
          <div class="col-6"><label class="form-label">Ordre</label><input class="form-control" type="number" name="display_order" value="<?= (int) ($editing['display_order'] ?? 0) ?>"></div>
        </div>
        <label class="form-label mt-3">Catégorie *</label>
        <select class="form-select mb-3" name="category_id" required>
          <option value="">Choisir</option>
          <?php foreach ($categories as $category): ?><option value="<?= (int) $category['id'] ?>" <?= ((int) ($editing['category_id'] ?? 0) === (int) $category['id']) ? 'selected' : '' ?>><?= e($category['name']) ?></option><?php endforeach; ?>
        </select>
        <label class="form-label">Chemin de l’image <?= $editing ? '' : '*' ?></label>
        <input class="form-control mb-2" name="image_path" placeholder="SRD_VVIP/poulet.jpg" value="<?= e($editing['image_path'] ?? '') ?>">
        <label class="form-label">ou téléverser une image</label>
        <input class="form-control mb-3" type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
        <label class="form-check mb-3"><input class="form-check-input" type="checkbox" name="is_active" <?= (!$editing || $editing['is_active']) ? 'checked' : '' ?>> <span class="form-check-label">Disponible à la commande</span></label>
        <button class="btn btn-gold w-100" type="submit"><?= $editing ? 'Enregistrer les modifications' : 'Ajouter le menu' ?></button>
      </form>
    </section>
  </div>
  <div class="col-xl-7">
    <section class="admin-panel mb-4">
      <div class="admin-panel-heading"><h2>Catégories</h2></div>
      <form method="post" class="row g-2 mb-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="category">
        <div class="col"><input class="form-control" name="name" placeholder="Nouvelle catégorie" required></div>
        <div class="col-auto"><button class="btn btn-outline-gold" type="submit">Ajouter</button></div>
      </form>
      <div class="d-flex flex-wrap gap-2"><?php foreach ($categories as $category): ?><form method="post" onsubmit="return confirm('Supprimer cette catégorie ?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete_category"><input type="hidden" name="category_id" value="<?= (int) $category['id'] ?>"><button class="btn btn-sm btn-outline-secondary" type="submit"><?= e($category['name']) ?> ×</button></form><?php endforeach; ?></div>
    </section>
    <section class="admin-panel">
      <div class="admin-panel-heading"><h2>Menus enregistrés</h2></div>
      <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Plat</th><th>Prix</th><th>Disponibilité</th><th></th></tr></thead><tbody>
      <?php foreach ($menus as $menu): ?><tr><td><div class="d-flex align-items-center gap-2"><img class="menu-thumb" src="../<?= e($menu['image_path']) ?>" alt=""><span><?= e($menu['name']) ?><small class="d-block text-secondary"><?= e($menu['category_name']) ?></small></span></div></td><td><?= e(money($menu['price'])) ?></td><td><span class="status-pill <?= $menu['is_active'] ? 'status-delivered' : 'status-cancelled' ?>"><?= $menu['is_active'] ? 'Disponible' : 'Indisponible' ?></span></td><td class="text-nowrap"><a href="menus.php?edit=<?= (int) $menu['id'] ?>" class="btn btn-sm btn-outline-gold">Modifier</a> <form method="post" class="d-inline" onsubmit="return confirm('Désactiver ce menu ?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $menu['id'] ?>"><button class="btn btn-sm btn-outline-danger">Désactiver</button></form></td></tr><?php endforeach; ?>
      </tbody></table></div>
    </section>
  </div>
</div>
<?php admin_footer(); ?>