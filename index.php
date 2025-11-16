<?php
require_once __DIR__ . '/includes/db.php';
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>SmartCart - Online Super Shop</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="bg-gray-50 font-sans">

  <!-- Header -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
          <div class="bg-green-600 text-white p-2 rounded-lg">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/></svg>
          </div>
          <h1 class="text-2xl font-bold text-green-600">SmartCart</h1>
        </div>

        <div class="flex-1 max-w-2xl mx-8">
          <div class="relative">
            <input type="text" id="searchInput" placeholder="Search for products, brands and more..." class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
        </div>

        <div class="flex items-center space-x-4">
          <button id="cartBtn" class="relative bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
            <div class="flex items-center space-x-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/></svg>
              <span>Cart</span>
              <span id="cartCount" class="bg-red-500 text-white text-xs rounded-full px-2 py-1 ml-1">0</span>
            </div>
          </button>

          <?php if ($user): ?>
            <div class="flex items-center space-x-2">
              <span class="text-gray-700">Hello, <?php echo htmlspecialchars($user['name']); ?></span>
              <form method="post" action="/api/logout.php">
                <button class="text-gray-600 hover:text-green-600 transition-colors ml-2">Logout</button>
              </form>
            </div>
          <?php else: ?>
            <a href="/login.php" class="text-gray-600 hover:text-green-600 transition-colors">Login</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <!-- Nav -->
  <nav class="bg-green-700 text-white">
    <div class="container mx-auto px-4">
      <div class="flex items-center space-x-8 py-3">
        <button id="categoriesBtn" class="flex items-center space-x-2 hover:bg-green-600 px-3 py-2 rounded">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          <span>All Categories</span>
        </button>
        <a href="#" class="hover:text-green-200 transition-colors">Fresh Produce</a>
        <a href="#" class="hover:text-green-200 transition-colors">Dairy & Eggs</a>
        <a href="#" class="hover:text-green-200 transition-colors">Meat & Fish</a>
        <a href="#" class="hover:text-green-200 transition-colors">Pantry</a>
        <a href="#" class="hover:text-green-200 transition-colors">Beverages</a>
        <a href="#" class="hover:text-green-200 transition-colors">Household</a>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <section class="bg-gradient-to-r from-green-600 to-blue-600 text-white py-16">
    <div class="container mx-auto px-4 text-center">
      <h2 class="text-4xl font-bold mb-4">Fresh Groceries Delivered to Your Door</h2>
      <p class="text-xl mb-8">Shop from thousands of products with same-day delivery</p>
      <button onclick="window.scrollTo({ top: document.querySelector('#productsGrid').offsetTop - 80, behavior: 'smooth' })" class="bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">Shop Now</button>
    </div>
  </section>

  <!-- Categories + Products -->
  <section class="py-12">
    <div class="container mx-auto px-4">
      <h3 class="text-2xl font-bold text-gray-800 mb-8">Shop by Category</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
        <!-- categories (same as your html) -->
        <div class="text-center group cursor-pointer">
          <div class="bg-orange-100 p-6 rounded-full mb-3 group-hover:bg-orange-200 transition-colors"><span class="text-3xl">🥕</span></div>
          <p class="font-medium text-gray-700">Fresh Produce</p>
        </div>
        <div class="text-center group cursor-pointer">
          <div class="bg-blue-100 p-6 rounded-full mb-3 group-hover:bg-blue-200 transition-colors"><span class="text-3xl">🥛</span></div>
          <p class="font-medium text-gray-700">Dairy & Eggs</p>
        </div>
        <div class="text-center group cursor-pointer">
          <div class="bg-red-100 p-6 rounded-full mb-3 group-hover:bg-red-200 transition-colors"><span class="text-3xl">🥩</span></div>
          <p class="font-medium text-gray-700">Meat & Fish</p>
        </div>
        <div class="text-center group cursor-pointer">
          <div class="bg-yellow-100 p-6 rounded-full mb-3 group-hover:bg-yellow-200 transition-colors"><span class="text-3xl">🍞</span></div>
          <p class="font-medium text-gray-700">Bakery</p>
        </div>
        <div class="text-center group cursor-pointer">
          <div class="bg-purple-100 p-6 rounded-full mb-3 group-hover:bg-purple-200 transition-colors"><span class="text-3xl">🥤</span></div>
          <p class="font-medium text-gray-700">Beverages</p>
        </div>
        <div class="text-center group cursor-pointer">
          <div class="bg-green-100 p-6 rounded-full mb-3 group-hover:bg-green-200 transition-colors"><span class="text-3xl">🧽</span></div>
          <p class="font-medium text-gray-700">Household</p>
        </div>
      </div>
    </div>
  </section>

  <section class="py-12 bg-white">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center mb-8">
        <h3 class="text-2xl font-bold text-gray-800">Featured Products</h3>
        <button id="viewAllBtn" class="text-green-600 hover:text-green-700 font-medium">View All →</button>
      </div>
      <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6"></div>
    </div>
  </section>

  <!-- Cart Sidebar -->
  <div id="cartSidebar" class="fixed right-0 top-0 h-full w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-50">
    <div class="p-6 border-b">
      <div class="flex justify-between items-center">
        <h3 class="text-xl font-bold">Shopping Cart</h3>
        <button id="closeCart" class="text-gray-500 hover:text-gray-700"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>
    </div>
    <div id="cartItems" class="flex-1 overflow-y-auto p-6"><p class="text-gray-500 text-center py-8">Your cart is empty</p></div>
    <div id="cartFooter" class="border-t p-6 bg-gray-50">
      <div class="flex justify-between items-center mb-4"><span class="text-lg font-semibold">Total:</span><span id="cartTotal" class="text-xl font-bold text-green-600">৳0</span></div>
      <button id="checkoutBtn" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors disabled:bg-gray-300" disabled>Proceed to Checkout</button>
    </div>
  </div>

  <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

  <script src="/js/script.js"></script>
</body>
</html>
