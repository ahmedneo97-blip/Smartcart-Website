// js/script.js - Main frontend JS which calls backend APIs

async function api(path, data = null, method = 'POST') {
  const opts = { method, headers: {} };
  if (data && !(data instanceof FormData)) {
    opts.headers['Content-Type'] = 'application/json';
    opts.body = JSON.stringify(data);
  } else if (data instanceof FormData) {
    opts.body = data;
  }
  const res = await fetch(path, opts);
  return res.json();
}

async function loadProducts() {
  const res = await fetch('/api/products.php');
  const products = await res.json();
  const grid = document.getElementById('productsGrid');
  if (!grid) return;
  grid.innerHTML = products.map(product => {
    const discountPrice = product.discount > 0 ? Math.round(product.price * (1 - product.discount / 100)) : null;
    return `
      <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow fade-in">
        <div class="text-center mb-3">
          <div class="text-6xl mb-2">${product.image}</div>
          ${product.discount > 0 ? `<span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">${product.discount}% OFF</span>` : ''}
        </div>
        <h4 class="font-semibold text-gray-800 mb-2">${product.name}</h4>
        <p class="text-sm text-gray-600 mb-3">${product.unit}</p>
        <div class="flex items-center justify-between mb-3">
          <div>
            ${discountPrice ? `<span class="text-lg font-bold text-green-600">৳${discountPrice}</span>
              <span class="text-sm text-gray-500 line-through ml-1">৳${product.price}</span>` :
              `<span class="text-lg font-bold text-green-600">৳${product.price}</span>`}
          </div>
        </div>
        <button onclick="addToCart(${product.id})" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-colors font-medium">Add to Cart</button>
      </div>
    `;
  }).join('');
}

async function addToCart(productId) {
  const resp = await api('/api/add_to_cart.php', { product_id: productId });
  if (resp.status === 'success') {
    bounceCart();
    await refreshCart();
  } else {
    alert(resp.msg || 'Failed to add to cart');
  }
}

function bounceCart() {
  const cartBtn = document.getElementById('cartBtn');
  if (!cartBtn) return;
  cartBtn.classList.add('cart-bounce');
  setTimeout(() => cartBtn.classList.remove('cart-bounce'), 300);
}

async function refreshCart() {
  const res = await fetch('/api/get_cart.php');
  const items = await res.json();
  const cartCount = document.getElementById('cartCount');
  const cartItems = document.getElementById('cartItems');
  const cartTotal = document.getElementById('cartTotal');
  const checkoutBtn = document.getElementById('checkoutBtn');

  if (!Array.isArray(items)) {
    if (cartItems) cartItems.innerHTML = '<p class="text-gray-500 text-center py-8">Your cart is empty</p>';
    if (cartCount) cartCount.textContent = '0';
    if (cartTotal) cartTotal.textContent = '৳0';
    if (checkoutBtn) checkoutBtn.disabled = true;
    return;
  }

  const totalItems = items.reduce((s, it) => s + it.quantity, 0);
  const totalPrice = items.reduce((s, it) => s + (it.final_price * it.quantity), 0);

  if (cartCount) cartCount.textContent = totalItems;
  if (cartTotal) cartTotal.textContent = `৳${totalPrice}`;

  if (!cartItems) return;
  if (items.length === 0) {
    cartItems.innerHTML = '<p class="text-gray-500 text-center py-8">Your cart is empty</p>';
    if (checkoutBtn) checkoutBtn.disabled = true;
  } else {
    cartItems.innerHTML = items.map(item => `
      <div class="flex items-center justify-between py-3 border-b">
        <div class="flex items-center space-x-3">
          <span class="text-2xl">${item.image}</span>
          <div>
            <h5 class="font-medium">${item.name}</h5>
            <p class="text-sm text-gray-600">৳${item.final_price} × ${item.quantity}</p>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <button onclick="changeQuantity(${item.id}, -1)" class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">-</button>
          <span class="w-8 text-center">${item.quantity}</span>
          <button onclick="changeQuantity(${item.id}, 1)" class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">+</button>
        </div>
      </div>
    `).join('');
    if (checkoutBtn) checkoutBtn.disabled = false;
  }
}

async function changeQuantity(cartId, delta) {
  const resp = await api('/api/update_cart.php', { cart_id: cartId, change: delta });
  if (resp.status === 'updated') {
    await refreshCart();
  } else {
    alert('Failed to update cart');
  }
}

// Sidebar toggle handlers
document.addEventListener('DOMContentLoaded', () => {
  const cartBtn = document.getElementById('cartBtn');
  if (cartBtn) cartBtn.addEventListener('click', () => {
    const sidebar = document.getElementById('cartSidebar');
    const overlay = document.getElementById('overlay');
    if (sidebar) sidebar.classList.remove('translate-x-full');
    if (overlay) overlay.classList.remove('hidden');
    refreshCart();
  });

  const closeBtn = document.getElementById('closeCart');
  if (closeBtn) closeBtn.addEventListener('click', () => {
    const sidebar = document.getElementById('cartSidebar');
    const overlay = document.getElementById('overlay');
    if (sidebar) sidebar.classList.add('translate-x-full');
    if (overlay) overlay.classList.add('hidden');
  });

  const overlay = document.getElementById('overlay');
  if (overlay) overlay.addEventListener('click', () => {
    const sidebar = document.getElementById('cartSidebar');
    if (sidebar) sidebar.classList.add('translate-x-full');
    overlay.classList.add('hidden');
  });

  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', async (e) => {
      const term = e.target.value.trim();
      if (term === '') return loadProducts();
      const res = await fetch('/api/products.php?q=' + encodeURIComponent(term));
      const products = await res.json();
      const grid = document.getElementById('productsGrid');
      if (!grid) return;
      grid.innerHTML = products.map(product => {
        const discountPrice = product.discount > 0 ? Math.round(product.price * (1 - product.discount / 100)) : null;
        return `
          <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow fade-in">
            <div class="text-center mb-3">
              <div class="text-6xl mb-2">${product.image}</div>
              ${product.discount > 0 ? `<span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">${product.discount}% OFF</span>` : ''}
            </div>
            <h4 class="font-semibold text-gray-800 mb-2">${product.name}</h4>
            <p class="text-sm text-gray-600 mb-3">${product.unit}</p>
            <div class="flex items-center justify-between mb-3">
              <div>
                ${discountPrice ? `<span class="text-lg font-bold text-green-600">৳${discountPrice}</span><span class="text-sm text-gray-500 line-through ml-1">৳${product.price}</span>` : `<span class="text-lg font-bold text-green-600">৳${product.price}</span>`}
              </div>
            </div>
            <button onclick="addToCart(${product.id})" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-colors font-medium">Add to Cart</button>
          </div>
        `;
      }).join('');
    });
  }

  // checkout button
  const checkoutBtn = document.getElementById('checkoutBtn');
  if (checkoutBtn) checkoutBtn.addEventListener('click', async () => {
    const resp = await api('/api/checkout.php', {});
    if (resp.message) {
      alert(resp.message);
      await refreshCart();
      const sidebar = document.getElementById('cartSidebar');
      const overlay = document.getElementById('overlay');
      if (sidebar) sidebar.classList.add('translate-x-full');
      if (overlay) overlay.classList.add('hidden');
    } else {
      alert('Checkout failed');
    }
  });

  // Load initially
  loadProducts();
  refreshCart();
});
