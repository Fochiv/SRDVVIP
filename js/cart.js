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
    if (!document.querySelector('.btn-add-cart')) return;
    fetch('api/menu.php', { headers: { Accept: 'application/json' } })
      .then((response) => response.ok ? response.json() : null)
      .then((result) => {
        if (!result || !result.ok) return;
        result.menus.forEach((menu) => {
          const buttons = document.querySelectorAll(`.btn-add-cart[data-cart-id="${menu.id}"]`);
          buttons.forEach((button) => {
            button.dataset.cartName = menu.name;
            button.dataset.cartPrice = menu.price;
            button.dataset.cartImage = menu.image_path;
            button.disabled = !Number(menu.is_active);
            button.setAttribute('aria-disabled', String(!Number(menu.is_active)));
            button.querySelector('span').textContent = Number(menu.is_active) ? 'Ajouter' : 'Indisponible';
          });
        });
      })
      .catch(() => {});
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