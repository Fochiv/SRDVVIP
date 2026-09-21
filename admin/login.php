<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

if (is_admin()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Votre session a expiré. Rechargez la page.';
    } elseif (!empty($_SESSION['login_locked_until']) && time() < $_SESSION['login_locked_until']) {
        $error = 'Trop de tentatives. Réessayez dans quelques minutes.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $configuredUser = env_value('SRDVVIP_ADMIN_USER', 'SRDVVIP');
        $passwordHash = env_value('SRDVVIP_ADMIN_PASSWORD_HASH');
        $valid = $passwordHash && hash_equals($configuredUser, $username) && password_verify($password, $passwordHash);
        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_login_at'] = time();
            $_SESSION['login_attempts'] = 0;
            header('Location: dashboard.php');
            exit;
        }
        $_SESSION['login_attempts'] = (int) ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_locked_until'] = time() + 300;
            $_SESSION['login_attempts'] = 0;
        }
        $error = 'Identifiant ou mot de passe incorrect.';
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion admin | SRDVVIP</title>
  <link rel="icon" href="../favicon.png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-login">
  <main class="login-card">
    <a href="../index.html" class="login-logo"><img src="../logo_SRD2.png" alt="SRDVVIP" height="72"></a>
    <p class="eyebrow">Espace privé</p>
    <h1>Connexion administration</h1>
    <p class="login-intro">Gérez vos menus et suivez les commandes depuis un espace sécurisé.</p>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <?php if (!env_value('SRDVVIP_ADMIN_PASSWORD_HASH')): ?><div class="alert alert-warning">Configurez <code>SRDVVIP_ADMIN_PASSWORD_HASH</code> dans l’environnement avant la première connexion.</div><?php endif; ?>
    <form method="post" novalidate>
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <label class="form-label" for="username">Identifiant ou email</label>
      <input class="form-control mb-3" id="username" name="username" autocomplete="username" required>
      <label class="form-label" for="password">Mot de passe</label>
      <input class="form-control mb-4" id="password" name="password" type="password" autocomplete="current-password" required>
      <button class="btn btn-gold w-100" type="submit">Se connecter</button>
    </form>
    <a class="back-site" href="../index.html"><i class="fas fa-arrow-left me-2"></i>Retour au site</a>
  </main>
</body>
</html>