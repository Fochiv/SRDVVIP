<?php
declare(strict_types=1);
require_once __DIR__ . '/config/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Votre panier | SRDVVIP</title>
  <meta name="description" content="Finalisez votre commande SRDVVIP et contactez-nous via WhatsApp.">
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="cart-page">
  <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand me-auto" href="index.html#accueil"><img src="logo_SRD2.png" alt="SRDVVIP Logo" height="55"></a>
      <div class="d-flex align-items-center gap-2">
        <button class="btn-theme" id="themeToggle" aria-label="Changer de thème">🌙</button>
        <a href="index.html#menu" class="btn btn-outline-gold btn-sm">Continuer mes achats</a>
      </div>
    </div>
  </nav>

  <main class="cart-shell">
    <div class="container">
      <div class="text-center mb-5">
        <p class="section-tag">Votre sélection</p>
        <h1 class="section-title">Votre <span class="gold-text">panier</span></h1>
        <p class="section-sub">Vérifiez votre commande puis choisissez comment la récupérer.</p>
      </div>

      <div id="cartEmpty" class="empty-cart d-none">
        <i class="fas fa-basket-shopping"></i>
        <h2>Votre panier est actuellement vide.</h2>
        <p>Ajoutez vos plats préférés pour commencer votre commande.</p>
        <a href="index.html#menu" class="btn btn-gold">Découvrir notre menu</a>
      </div>

      <div id="cartContent" class="row g-4 align-items-start d-none">
        <div class="col-lg-7">
          <div class="cart-panel">
            <div class="cart-panel-heading">
              <h2>Articles sélectionnés</h2>
              <span id="cartItemCount" class="text-muted"></span>
            </div>
            <div id="cartItems"></div>
            <div class="cart-total-row">
              <span>Total général</span>
              <strong id="cartTotal">0 FCFA</strong>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <form id="checkoutForm" class="cart-panel checkout-form" novalidate>
            <h2>Informations de commande</h2>
            <p class="form-hint">Votre commande est enregistrée avant l’ouverture de WhatsApp.</p>
            <div id="checkoutError" class="alert alert-danger d-none"></div>
            <div class="mb-3">
              <label for="customerName" class="form-label">Nom complet *</label>
              <input id="customerName" name="customer_name" class="form-control" required maxlength="160">
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="phone" class="form-label">Téléphone *</label>
                <input id="phone" name="phone" type="tel" class="form-control" required maxlength="40" placeholder="+237 6XX XX XX XX">
              </div>
              <div class="col-md-6">
                <label for="whatsapp" class="form-label">WhatsApp *</label>
                <input id="whatsapp" name="whatsapp" type="tel" class="form-control" required maxlength="40" placeholder="+237 6XX XX XX XX">
              </div>
            </div>
            <div class="mb-3 mt-3">
              <label for="recoveryMode" class="form-label">Mode de récupération *</label>
              <select id="recoveryMode" name="recovery_mode" class="form-select" required>
                <option value="">Choisir</option>
                <option value="delivery">Livraison</option>
                <option value="takeaway">À emporter</option>
                <option value="onsite">Sur place</option>
              </select>
            </div>
            <div id="deliveryFields" class="d-none">
              <div class="mb-3">
                <label for="address" class="form-label">Adresse / lieu de livraison *</label>
                <input id="address" name="address" class="form-control" maxlength="255">
              </div>
              <div class="mb-3">
                <label for="district" class="form-label">Quartier</label>
                <input id="district" name="district" class="form-control" maxlength="120">
              </div>
            </div>
            <div id="onsiteFields" class="row g-3 d-none">
              <div class="col-md-6">
                <label for="peopleCount" class="form-label">Nombre de personnes</label>
                <input id="peopleCount" name="people_count" class="form-control" maxlength="20">
              </div>
              <div class="col-md-6">
                <label for="requestedTime" class="form-label">Heure souhaitée</label>
                <input id="requestedTime" name="requested_time" type="time" class="form-control" min="10:00" max="23:00">
              </div>
            </div>
            <div class="mb-3 mt-3">
              <label for="instructions" class="form-label">Instructions particulières</label>
              <textarea id="instructions" name="instructions" class="form-control" rows="3" maxlength="1000" placeholder="Précisions pour le restaurant (optionnel)"></textarea>
            </div>
            <button type="submit" class="btn btn-gold w-100" id="submitOrder">
              <i class="fab fa-whatsapp me-2"></i>Enregistrer et commander via WhatsApp
            </button>
            <p class="checkout-secure"><i class="fas fa-lock me-1"></i>Les prix sont revérifiés côté serveur avant validation.</p>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script>window.SRD_CSRF = <?= json_encode(csrf_token(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
  <script src="js/cart.js"></script>
  <script>
    (() => {
      const cartContent = document.getElementById('cartContent');
      const cartEmpty = document.getElementById('cartEmpty');
      const itemsNode = document.getElementById('cartItems');
      const totalNode = document.getElementById('cartTotal');
      const countNode = document.getElementById('cartItemCount');
      const modeNode = document.getElementById('recoveryMode');
      const money = SRDVVIPCart.formatMoney;

      function render() {
        const cart = SRDVVIPCart.read();
        const hasItems = cart.length > 0;
        cartContent.classList.toggle('d-none', !hasItems);
        cartEmpty.classList.toggle('d-none', hasItems);
        if (!hasItems) return;
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        countNode.textContent = `${count} article${count > 1 ? 's' : ''}`;
        totalNode.textContent = money(total);
        itemsNode.innerHTML = cart.map((item) => `
          <div class="cart-line">
            <img src="${item.image}" alt="${item.name}">
            <div class="cart-line-info">
              <h3>${item.name}</h3>
              <span>${money(item.price)} l’unité</span>
            </div>
            <div class="quantity-control" aria-label="Quantité de ${item.name}">
              <button type="button" data-action="decrease" data-id="${item.id}" aria-label="Diminuer">−</button>
              <strong>${item.quantity}</strong>
              <button type="button" data-action="increase" data-id="${item.id}" aria-label="Augmenter">+</button>
            </div>
            <strong class="cart-line-subtotal">${money(item.price * item.quantity)}</strong>
            <button type="button" class="cart-remove" data-action="remove" data-id="${item.id}" aria-label="Supprimer ${item.name}"><i class="fas fa-trash"></i></button>
          </div>`).join('');
      }

      itemsNode.addEventListener('click', (event) => {
        const button = event.target.closest('[data-action]');
        if (!button) return;
        const cart = SRDVVIPCart.read();
        const item = cart.find((entry) => String(entry.id) === button.dataset.id);
        if (!item) return;
        if (button.dataset.action === 'increase') item.quantity = Math.min(99, item.quantity + 1);
        if (button.dataset.action === 'decrease') item.quantity -= 1;
        if (button.dataset.action === 'remove' || item.quantity < 1) {
          const index = cart.indexOf(item);
          cart.splice(index, 1);
        }
        SRDVVIPCart.write(cart);
        render();
      });

      modeNode.addEventListener('change', () => {
        const delivery = modeNode.value === 'delivery';
        const onsite = modeNode.value === 'onsite';
        document.getElementById('deliveryFields').classList.toggle('d-none', !delivery);
        document.getElementById('onsiteFields').classList.toggle('d-none', !onsite);
        document.getElementById('address').required = delivery;
      });

      document.getElementById('checkoutForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const errorNode = document.getElementById('checkoutError');
        const submit = document.getElementById('submitOrder');
        errorNode.classList.add('d-none');
        if (!form.checkValidity()) {
          form.classList.add('was-validated');
          return;
        }
        submit.disabled = true;
        submit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement…';
        const body = Object.fromEntries(new FormData(form).entries());
        body.items = SRDVVIPCart.read().map(({ id, quantity }) => ({ id, quantity }));
        try {
          const response = await fetch('api/order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.SRD_CSRF },
            body: JSON.stringify(body),
          });
          const result = await response.json();
          if (!response.ok || !result.ok) throw new Error(result.message || 'La commande n’a pas pu être enregistrée.');
          SRDVVIPCart.write([]);
          window.location.href = result.whatsapp_url;
        } catch (error) {
          errorNode.textContent = error.message;
          errorNode.classList.remove('d-none');
          submit.disabled = false;
          submit.innerHTML = '<i class="fab fa-whatsapp me-2"></i>Enregistrer et commander via WhatsApp';
        }
      });

      document.addEventListener('srdvvip:cart-updated', render);
      render();
    })();
  </script>
</body>
</html>