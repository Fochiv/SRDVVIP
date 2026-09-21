/* SRDVVIP persistent shopping cart */
(function () {
  'use strict';

  const STORAGE_KEY = 'srdvvip-cart';

  function readCart() {
    try {
      const value = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
      return Array.isArray(value) ? value : [];
    } catch (error) {
      return [];
    }
  }

  function writeCart(cart) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    updateCartCount(cart);
    document.dispatchEvent(new CustomEvent('srdvvip:cart-updated', { detail: cart }));
  }

  function updateCartCount(cart = readCart()) {
    const count = cart.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
    document.querySelectorAll('.cart-count').forEach((node) => {
      node.textContent = count;
      node.classList.toggle('is-empty', count === 0);
    });
  }

  function addToCart(product) {
    const cart = readCart();
    const existing = cart.find((item) => String(item.id) === String(product.id));
    if (existing) {
      existing.quantity = Math.min(99, Number(existing.quantity) + 1);
    } else {
      cart.push({ ...product, quantity: 1 });
    }
    writeCart(cart);
    showToast(`${product.name} a été ajouté au panier.`);
  }

  function syncAvailableMenus() {
    const menuGrid = document.getElementById('menuGrid');
    if (!menuGrid || !document.querySelector('.btn-add-cart')) return;
    fetch('api/menu.php', { headers: { Accept: 'application/json' } })
      .then((response) => response.ok ? response.json() : null)
      .then((result) => {
        if (!result || !result.ok) return;
        result.menus.forEach((menu) => {
          let buttons = [...document.querySelectorAll(`.btn-add-cart[data-cart-id="${menu.id}"]`)];
          if (!buttons.length) {
            buttons = [...document.querySelectorAll('.btn-add-cart')].filter(
              (button) => button.dataset.cartName?.trim().toLocaleLowerCase() === menu.name.trim().toLocaleLowerCase(),
            );
          }
          buttons.forEach((button) => {
            button.dataset.cartName = menu.name;
            button.dataset.cartId = menu.id;
            button.dataset.cartPrice = menu.price;
            button.dataset.cartImage = menu.image_path;
            button.disabled = !Number(menu.is_active);
            button.setAttribute('aria-disabled', String(!Number(menu.is_active)));
            button.querySelector('span').textContent = Number(menu.is_active) ? 'Ajouter' : 'Indisponible';
            updateMenuCard(button.closest('.menu-item'), menu);
          });
          if (!buttons.length && Number(menu.is_active)) appendMenuCard(menu, menuGrid);
        });
      })
      .catch(() => {});
  }

  function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (character) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
    }[character]));
  }

  function updateMenuCard(card, menu) {
    if (!card) return;
    card.dataset.category = menu.category;
    const image = card.querySelector('.menu-img-wrap img');
    const title = card.querySelector('.menu-body h4');
    const description = card.querySelector('.menu-body p');
    const price = card.querySelector('.menu-price strong');
    const whatsapp = card.querySelector('.btn-order');
    if (image) {
      image.src = menu.image_path;
      image.alt = menu.name;
    }
    if (title) title.textContent = menu.name;
    if (description && menu.description) description.textContent = menu.description;
    if (price) price.textContent = `${Number(menu.price).toLocaleString('fr-FR')} FCFA`;
    if (whatsapp) {
      whatsapp.href = `https://wa.me/237659763338?text=${encodeURIComponent(`Je voudrais commander ${menu.name}`)}`;
    }
  }

  function appendMenuCard(menu, menuGrid) {
    const item = document.createElement('div');
    item.className = 'col-sm-6 col-lg-4 menu-item';
    item.dataset.category = menu.category;
    item.innerHTML = `
      <div class="menu-card">
        <div class="menu-img-wrap">
          <img src="${escapeHtml(menu.image_path)}" alt="${escapeHtml(menu.name)}">
        </div>
        <div class="menu-body">
          <h4>${escapeHtml(menu.name)}</h4>
          <p>${escapeHtml(menu.description)}</p>
          <div class="menu-footer">
            <span class="menu-price">À partir de <strong>${Number(menu.price).toLocaleString('fr-FR')} FCFA</strong></span>
            <button type="button" class="btn-add-cart" data-cart-id="${escapeHtml(menu.id)}" data-cart-name="${escapeHtml(menu.name)}" data-cart-price="${escapeHtml(menu.price)}" data-cart-image="${escapeHtml(menu.image_path)}">
              <i class="fas fa-cart-plus me-1"></i><span>Ajouter</span>
            </button>
            <a href="https://wa.me/237659763338?text=${encodeURIComponent(`Je voudrais commander ${menu.name}`)}" target="_blank" class="btn-order" rel="noopener">
              <i class="fab fa-whatsapp"></i>
            </a>
          </div>
        </div>
      </div>`;
    menuGrid.appendChild(item);
  }

  function showToast(message) {
    let toast = document.getElementById('cartToast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'cartToast';
      toast.className = 'cart-toast';
      document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.add('visible');
    window.clearTimeout(showToast.timer);
    showToast.timer = window.setTimeout(() => toast.classList.remove('visible'), 2600);
  }

  document.addEventListener('click', (event) => {
    const button = event.target.closest('.btn-add-cart');
    if (!button) return;
    addToCart({
      id: Number(button.dataset.cartId),
      name: button.dataset.cartName,
      price: Number(button.dataset.cartPrice),
      image: button.dataset.cartImage,
    });
  });

  window.SRDVVIPCart = {
    read: readCart,
    write: writeCart,
    updateCount: updateCartCount,
    formatMoney: (amount) => `${Number(amount).toLocaleString('fr-FR')} FCFA`,
  };

  updateCartCount();
  syncAvailableMenus();
})();