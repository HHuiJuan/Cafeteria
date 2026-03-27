// ===== STATE =====
let cart = [];
let allItems = [];
let currentCategory = '';
let currentSearch = '';

const categoryEmojis = {
  'Burger': '🍔', 'Burgers': '🍔',
  'Pizza': '🍕',
  'Sides': '🍟', 'Side': '🍟',
  'Drinks': '🥤', 'Drink': '🥤', 'Beverages': '🥤',
  'Desserts': '🍰', 'Dessert': '🍰',
  'Breakfast': '🍳',
  'Sandwiches': '🥪', 'Sandwich': '🥪',
  'Salads': '🥗', 'Salad': '🥗',
  'Soups': '🍲', 'Soup': '🍲',
  'Pasta': '🍝',
  'Seafood': '🦞',
  'Steak': '🥩', 'Steaks': '🥩',
  'Kids': '🧒',
  'Specials': '⭐',
  'Noodles': '🍜',
  'Rice': '🍚',
  'Instant Food': '🥫',
};

function getCatEmoji(cat) {
  return categoryEmojis[cat] || '🍽️';
}

// ===== INIT =====
document.addEventListener('DOMContentLoaded', () => {
  loadMenu();
});

// ===== LOAD MENU =====
async function loadMenu() {
  try {
    const [menuRes, catRes] = await Promise.all([
      fetch('api.php?action=get_menu'),
      fetch('api.php?action=get_categories')
    ]);
    allItems = await menuRes.json();
    const categories = await catRes.json();
    buildSidebar(categories);
    renderMenu(allItems);
  } catch (err) {
    document.getElementById('menuContainer').innerHTML = `
      <div class="no-results">
        <div class="no-results-icon">⚠️</div>
        <h3>Connection Error</h3>
        <p>Could not connect to the database.<br>Please check your connection.</p>
      </div>`;
  }
}

// ===== SIDEBAR =====
function buildSidebar(categories) {
  const nav = document.getElementById('catNav');
  // Keep the "All Items" button
  const allBtn = nav.querySelector('[data-cat=""]');
  nav.innerHTML = '';
  nav.appendChild(allBtn);

  categories.forEach(cat => {
    const btn = document.createElement('button');
    btn.className = 'cat-btn';
    btn.dataset.cat = cat;
    btn.onclick = () => filterCategory(cat);
    btn.innerHTML = `${getCatEmoji(cat)} ${cat}`;
    nav.appendChild(btn);
  });
}

// ===== FILTER =====
function filterCategory(cat) {
  currentCategory = cat;
  currentSearch = '';
  document.getElementById('searchInput').value = '';
  document.getElementById('clearSearch').style.display = 'none';

  // Update active button
  document.querySelectorAll('.cat-btn').forEach(b => {
    b.classList.toggle('active', b.dataset.cat === cat);
  });

  const filtered = cat ? allItems.filter(i => i.Category === cat) : allItems;
  renderMenu(filtered);

  // Scroll to top of main
  document.querySelector('.main-content').scrollTop = 0;
}

function handleSearch(val) {
  currentSearch = val.trim().toLowerCase();
  document.getElementById('clearSearch').style.display = val ? 'block' : 'none';

  // Deactivate category filter
  document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
  document.querySelector('[data-cat=""]').classList.add('active');
  currentCategory = '';

  const filtered = currentSearch
    ? allItems.filter(i =>
        i.Name.toLowerCase().includes(currentSearch) ||
        i.Category.toLowerCase().includes(currentSearch))
    : allItems;
  renderMenu(filtered);
}

function clearSearch() {
  document.getElementById('searchInput').value = '';
  handleSearch('');
}

// ===== RENDER MENU =====
function renderMenu(items) {
  const container = document.getElementById('menuContainer');

  if (!items.length) {
    container.innerHTML = `
      <div class="no-results">
        <div class="no-results-icon">🔍</div>
        <h3>Nothing Found!</h3>
        <p>Try a different search or category.</p>
      </div>`;
    return;
  }

  // Group by category
  const grouped = {};
  items.forEach(item => {
    if (!grouped[item.Category]) grouped[item.Category] = [];
    grouped[item.Category].push(item);
  });

  let html = '';
  Object.entries(grouped).forEach(([cat, catItems]) => {
    html += `
      <section class="category-section" id="cat-${slugify(cat)}">
        <div class="category-heading">
          <h2>${getCatEmoji(cat)} ${cat}</h2>
          <div class="cat-divider"></div>
        </div>
        <div class="menu-grid">
          ${catItems.map(item => buildCard(item)).join('')}
        </div>
      </section>`;
  });
  container.innerHTML = html;
}

function slugify(str) {
  return str.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]/g, '');
}

function buildCard(item) {
  const unavailable = item.Availability && item.Availability.toLowerCase() === 'no';
  const imgHtml = item.Image
    ? `<img src="${escHtml(item.Image)}" alt="${escHtml(item.Name)}" onerror="this.parentNode.innerHTML='<div class=card-img-placeholder>${getCatEmoji(item.Category)}</div>'">`
    : `<div class="card-img-placeholder">${getCatEmoji(item.Category)}</div>`;

  return `
    <div class="menu-card ${unavailable ? 'unavailable' : ''}" onclick="${unavailable ? '' : `addToCart(${item.MenuID})`}">
      ${unavailable ? '<div class="unavailable-badge">UNAVAILABLE</div>' : ''}
      <div class="card-img-wrap">${imgHtml}</div>
      <div class="card-body">
        <div class="card-category">${escHtml(item.Category)}</div>
        <div class="card-name">${escHtml(item.Name)}</div>
        <div class="card-footer">
          <div class="card-price">RM${parseFloat(item.Price).toFixed(2)}</div>
          <button class="add-btn" ${unavailable ? 'disabled' : `onclick="event.stopPropagation(); addToCart(${item.MenuID})"`}>+</button>
        </div>
      </div>
    </div>`;
}

function escHtml(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ===== CART =====
function addToCart(menuId) {
  const item = allItems.find(i => parseInt(i.MenuID) === parseInt(menuId));
  if (!item || (item.Availability && item.Availability.toLowerCase() === 'no')) return;

  const existing = cart.find(c => c.menu_id === menuId);
  if (existing) {
    existing.quantity++;
  } else {
    cart.push({
      menu_id: menuId,
      name: item.Name,
      price: parseFloat(item.Price),
      quantity: 1
    });
  }
  updateCartUI();
  flashCartBtn();
}

function changeQty(menuId, delta) {
  const idx = cart.findIndex(c => c.menu_id === menuId);
  if (idx === -1) return;
  cart[idx].quantity += delta;
  if (cart[idx].quantity <= 0) cart.splice(idx, 1);
  updateCartUI();
}

function updateCartUI() {
  const total = cart.reduce((s, c) => s + c.price * c.quantity, 0);
  const count = cart.reduce((s, c) => s + c.quantity, 0);

  // Badge
  document.getElementById('cartBadge').textContent = count;

  // Drawer items
  const itemsEl = document.getElementById('cartItems');
  const footerEl = document.getElementById('cartFooter');

  if (!cart.length) {
    itemsEl.innerHTML = `<div class="cart-empty"><div class="empty-plate">🍽️</div><p>Your plate is empty!<br>Add something delicious.</p></div>`;
    footerEl.style.display = 'none';
  } else {
    itemsEl.innerHTML = cart.map(c => `
      <div class="cart-item">
        <div class="cart-item-name">${escHtml(c.name)}</div>
        <div class="qty-controls">
          <button class="qty-btn" onclick="changeQty(${c.menu_id},-1)">−</button>
          <span class="qty-num">${c.quantity}</span>
          <button class="qty-btn" onclick="changeQty(${c.menu_id},1)">+</button>
        </div>
        <div class="cart-item-price">RM${(c.price * c.quantity).toFixed(2)}</div>
      </div>`).join('');
    footerEl.style.display = 'block';
    document.getElementById('cartSubtotal').textContent = `RM${total.toFixed(2)}`;
    document.getElementById('cartTotal').textContent = `RM${total.toFixed(2)}`;
  }

  // Sidebar cart
  const sidebarCart = document.getElementById('sidebarCart');
  if (cart.length) {
    sidebarCart.style.display = 'block';
    document.getElementById('sidebarCartItems').innerHTML =
      cart.map(c => `<div class="sidebar-cart-item"><span>${escHtml(c.name)} x${c.quantity}</span><span>RM${(c.price*c.quantity).toFixed(2)}</span></div>`).join('');
    document.getElementById('sidebarTotal').textContent = `RM${total.toFixed(2)}`;
  } else {
    sidebarCart.style.display = 'none';
  }
}

function flashCartBtn() {
  const btn = document.getElementById('cartBtn');
  btn.style.transform = 'scale(1.15)';
  setTimeout(() => btn.style.transform = '', 200);
}

// ===== CART DRAWER TOGGLE =====
function toggleCart() {
  document.getElementById('cartDrawer').classList.toggle('open');
  document.getElementById('cartOverlay').classList.toggle('open');
}

// ===== CHECKOUT =====
function openCheckout() {
  if (!cart.length) return;

  // Close cart drawer
  document.getElementById('cartDrawer').classList.remove('open');
  document.getElementById('cartOverlay').classList.remove('open');

  const total = cart.reduce((s, c) => s + c.price * c.quantity, 0);

  // Build summary
  document.getElementById('checkoutSummary').innerHTML =
    cart.map(c => `
      <div class="summary-item">
        <span>${escHtml(c.name)} × ${c.quantity}</span>
        <span>RM${(c.price * c.quantity).toFixed(2)}</span>
      </div>`).join('');
  document.getElementById('checkoutTotal').textContent = `RM${total.toFixed(2)}`;

  document.getElementById('checkoutOverlay').classList.add('open');
}

function closeCheckout() {
  document.getElementById('checkoutOverlay').classList.remove('open');
}

async function confirmOrder() {
  const type = document.querySelector('input[name="orderType"]:checked').value;
  const method = document.querySelector('input[name="payMethod"]:checked').value;
  const btn = document.querySelector('.confirm-order-btn');
  btn.disabled = true;
  btn.textContent = '⏳ Placing Order...';

  const payload = {
    type,
    payment_method: method,
    items: cart.map(c => ({ menu_id: c.menu_id, quantity: c.quantity, price: c.price }))
  };

  try {
    const res = await fetch('api.php?action=place_order', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();

    if (data.success) {
      closeCheckout();
      showSuccess(data.order_id, data.total, type, method);
      cart = [];
      updateCartUI();
    } else {
      alert('Error: ' + (data.error || 'Unknown error'));
    }
  } catch (err) {
    alert('Network error. Please try again.');
  } finally {
    btn.disabled = false;
    btn.textContent = '✅ Confirm Order';
  }
}

function showSuccess(orderId, total, type, method) {
  document.getElementById('successOrderId').textContent = orderId;
  document.getElementById('successMessage').textContent = `${type} • ${method} Payment`;

  const receiptItems = cart.length
    ? cart.map(c => `<div class="receipt-row"><span>${escHtml(c.name)} x${c.quantity}</span><span>RM${(c.price*c.quantity).toFixed(2)}</span></div>`).join('')
    : '<p style="color:#888;font-size:12px">Items processed.</p>';

  document.getElementById('receipt').innerHTML = `
    ${receiptItems}
    <hr class="receipt-divider">
    <div class="receipt-total"><span>TOTAL</span><span>RM${parseFloat(total).toFixed(2)}</span></div>
    <div style="margin-top:8px;font-size:11px;color:#888;text-align:center">Thank you! Order #${orderId} confirmed ✓</div>`;

  document.getElementById('successOverlay').classList.add('open');
}

function newOrder() {
  document.getElementById('successOverlay').classList.remove('open');
  cart = [];
  updateCartUI();
}
