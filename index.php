<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TARUMT Cafeteria — Order Up!</title>
<link href="https://fonts.googleapis.com/css2?family=Boogaloo&family=Permanent+Marker&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NEON HEADER -->
<header class="header">
  <div class="header-inner">
    <div class="logo-wrap">
      <div class="neon-sign">
        <span class="neon-r">TARUMT</span><span class="neon-text">Cafeteria</span>
      </div>
      <p class="tagline">★ Est. 2026 — Good Food, Great Times ★</p>
    </div>
    <div class="header-actions">
      <button class="cart-btn" id="cartBtn" onclick="toggleCart()">
        <span class="cart-icon">🛒</span>
        <span class="cart-label">My Order</span>
        <span class="cart-badge" id="cartBadge">0</span>
      </button>
    </div>
  </div>
</header>

<!-- MARQUEE STRIP -->
<div class="marquee-strip">
  <div class="marquee-inner">
    ★ TODAY'S SPECIAL: KARI LAKSA RM6.50 &nbsp;&nbsp;&nbsp;  ★ HOMEMADE CURRY FRESH DAILY &nbsp;&nbsp;&nbsp; ★ OPEN 7 DAYS A WEEK &nbsp;&nbsp;&nbsp; ★ DINE-IN OR TAKEAWAY &nbsp;&nbsp;&nbsp; ★ TODAY'S SPECIAL: DOUBLE PATTY BURGER RM10 &nbsp;&nbsp;&nbsp; 
  </div>
</div>

<!-- MAIN LAYOUT -->
<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h3>📋 Menu</h3>
    </div>

    <!-- Search -->
    <div class="search-box">
      <input type="text" id="searchInput" placeholder="🔍 Search menu..." oninput="handleSearch(this.value)">
      <button class="clear-search" id="clearSearch" onclick="clearSearch()" style="display:none">✕</button>
    </div>

    <!-- Categories -->
    <div class="cat-label">Categories</div>
    <nav class="cat-nav" id="catNav">
      <button class="cat-btn active" onclick="filterCategory('')" data-cat="">
        🍽️ All Items
      </button>
      <!-- Dynamically filled -->
    </nav>

    <!-- Cart Preview -->
    <div class="sidebar-cart" id="sidebarCart" style="display:none">
      <div class="sidebar-cart-title">🛒 Current Order</div>
      <div id="sidebarCartItems"></div>
      <div class="sidebar-cart-total">Total: <span id="sidebarTotal">RM0.00</span></div>
      <button class="checkout-sidebar-btn" onclick="openCheckout()">Checkout →</button>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <div id="menuContainer">
      <div class="loading-diner">
        <div class="loading-sign">LOADING MENU...</div>
      </div>
    </div>
  </main>
</div>

<!-- CART DRAWER -->
<div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>
<div class="cart-drawer" id="cartDrawer">
  <div class="cart-header">
    <h2>🛒 Your Order</h2>
    <button class="cart-close" onclick="toggleCart()">✕</button>
  </div>
  <div class="cart-items" id="cartItems">
    <div class="cart-empty">
      <div class="empty-plate">🍽️</div>
      <p>Your plate is empty!<br>Add something delicious.</p>
    </div>
  </div>
  <div class="cart-footer" id="cartFooter" style="display:none">
    <div class="cart-total-row">
      <span>Subtotal</span><span id="cartSubtotal">$0.00</span>
    </div>
    <div class="cart-total-row grand">
      <span>Total</span><span id="cartTotal">RM0.00</span>
    </div>
    <button class="checkout-btn" onclick="openCheckout()">
      Place Order →
    </button>
  </div>
</div>

<!-- CHECKOUT MODAL -->
<div class="modal-overlay" id="checkoutOverlay">
  <div class="modal checkout-modal">
    <button class="modal-close" onclick="closeCheckout()">✕</button>
    <h2 class="modal-title">📝 Place Your Order</h2>

    <div class="checkout-order-summary" id="checkoutSummary"></div>

    <div class="form-section">
      <label class="form-label">Order Type</label>
      <div class="radio-group">
        <label class="radio-card">
          <input type="radio" name="orderType" value="Dine-In" checked>
          <span class="radio-face">🍽️</span>
          <span>Dine-In</span>
        </label>
        <label class="radio-card">
          <input type="radio" name="orderType" value="Takeaway">
          <span class="radio-face">🥡</span>
          <span>Takeaway</span>
        </label>
      </div>
    </div>

    <div class="form-section">
      <label class="form-label">Payment Method</label>
      <div class="radio-group">
        <label class="radio-card">
          <input type="radio" name="payMethod" value="Cash" checked>
          <span class="radio-face">💵</span>
          <span>Cash</span>
        </label>
        <label class="radio-card">
          <input type="radio" name="payMethod" value="Card">
          <span class="radio-face">💳</span>
          <span>Card</span>
        </label>
        <label class="radio-card">
          <input type="radio" name="payMethod" value="Online">
          <span class="radio-face">📱</span>
          <span>Online</span>
        </label>
      </div>
    </div>

    <div class="checkout-total-bar">
      Total: <strong id="checkoutTotal">RM0.00</strong>
    </div>

    <button class="confirm-order-btn" onclick="confirmOrder()">
      ✅ Confirm Order
    </button>
  </div>
</div>

<!-- ORDER SUCCESS MODAL -->
<div class="modal-overlay" id="successOverlay">
  <div class="modal success-modal">
    <div class="success-icon">🎉</div>
    <h2>Order Placed!</h2>
    <div class="success-order-id">Order #<span id="successOrderId"></span></div>
    <p id="successMessage"></p>
    <div class="receipt" id="receipt"></div>
    <button class="new-order-btn" onclick="newOrder()">New Order</button>
  </div>
</div>

<script src="app.js"></script>
</body>
</html>
