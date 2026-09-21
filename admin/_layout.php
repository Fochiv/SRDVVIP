<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

function admin_header(string $title): void
{
    $flash = pull_flash();
    ?>
    <!doctype html>
    <html lang="fr">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title><?= e($title) ?> | Administration SRDVVIP</title>
      <link rel="icon" href="../favicon.png">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
      <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="../css/admin.css">
    </head>
    <body class="admin-body">
      <div class="admin-shell">
        <aside class="admin-sidebar" id="adminSidebar">
          <button class="admin-menu-close" id="adminMenuClose" type="button" aria-label="Fermer le menu"><i class="fas fa-xmark"></i></button>
          <a href="dashboard.php" class="admin-brand"><img src="../logo_SRD2.png" alt="SRDVVIP" height="58"><span>Administration</span></a>
          <nav class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-chart-line"></i> Tableau de bord</a>
            <a href="menus.php"><i class="fas fa-utensils"></i> Menus & catégories</a>
            <a href="orders.php"><i class="fas fa-receipt"></i> Commandes <span class="admin-badge" id="pendingBadge"></span></a>
            <a href="../index.html" target="_blank"><i class="fas fa-arrow-up-right-from-square"></i> Voir le site</a>
          </nav>
          <div class="admin-sidebar-bottom">
            <span><?= e(env_value('SRDVVIP_ADMIN_USER', 'SRDVVIP')) ?></span>
            <a href="logout.php"><i class="fas fa-right-from-bracket"></i> Déconnexion</a>
          </div>
        </aside>
        <div class="admin-sidebar-backdrop" id="adminSidebarBackdrop"></div>
        <div class="admin-main">
          <header class="admin-topbar">
            <button class="admin-menu-toggle" id="adminMenuToggle" aria-label="Ouvrir le menu"><i class="fas fa-bars"></i></button>
            <div>
              <p class="eyebrow">Espace sécurisé</p>
              <h1><?= e($title) ?></h1>
            </div>
            <span class="admin-live"><span></span> En ligne</span>
          </header>
          <main class="admin-content">
            <?php if ($flash): ?>
              <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
            <?php endif; ?>
    <?php
}

function admin_footer(): void
{
    ?>
          </main>
        </div>
      </div>
      <script>
        const adminSidebar = document.getElementById('adminSidebar');
        const adminMenuToggle = document.getElementById('adminMenuToggle');
        const adminMenuClose = document.getElementById('adminMenuClose');
        const adminSidebarBackdrop = document.getElementById('adminSidebarBackdrop');
        const setAdminMenuOpen = (open) => {
          adminSidebar?.classList.toggle('open', open);
          adminSidebarBackdrop?.classList.toggle('visible', open);
          document.body.classList.toggle('admin-menu-open', open);
          adminMenuToggle?.setAttribute('aria-expanded', String(open));
        };
        adminMenuToggle?.addEventListener('click', () => setAdminMenuOpen(true));
        adminMenuClose?.addEventListener('click', () => setAdminMenuOpen(false));
        adminSidebarBackdrop?.addEventListener('click', () => setAdminMenuOpen(false));
        adminSidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setAdminMenuOpen(false)));
        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape') setAdminMenuOpen(false);
        });
        async function refreshPendingBadge() {
          try {
            const response = await fetch('api.php?action=counts', { headers: { 'Accept': 'application/json' } });
            const data = await response.json();
            const badge = document.getElementById('pendingBadge');
            if (badge) { badge.textContent = data.pending || ''; badge.hidden = !data.pending; }
          } catch (error) {}
        }
        refreshPendingBadge();
        window.setInterval(refreshPendingBadge, 30000);
      </script>
    </body>
    </html>
    <?php
}