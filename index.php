<?php
require_once "db.php";
$user = null;
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SmartCart - Online Super Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            box-sizing: border-box;
            background-color: red;
        }

        .cart-bounce {
            animation: bounce 0.3s ease-in-out;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <div class="bg-green-600 text-white p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-green-600">SmartCart</h1>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl mx-8">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search for products, brands and more..."
                            class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Cart & User -->
                <div class="flex items-center space-x-4">
                    <button id="cartBtn" class="relative bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                            </svg>
                            <span>Cart</span>
                            <span id="cartCount" class="bg-red-500 text-white text-xs rounded-full px-2 py-1 ml-1">0</span>
                        </div>
                    </button>

                    <?php if ($user): ?>
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-700">Hello, <?php echo htmlspecialchars($user['name']); ?></span>
                            <form id="logoutForm" method="post" action="api/logout.php">
                                <button class="text-gray-600 hover:text-green-600 transition-colors ml-2">Logout</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <button id="loginBtn" class="text-gray-600 hover:text-green-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-green-700 text-white">
        <div class="container mx-auto px-4">
            <div class="flex items-center space-x-8 py-3">
                <button id="categoriesBtn" class="flex items-center space-x-2 hover:bg-green-600 px-3 py-2 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
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

    <!-- Hero Banner -->
    <section class="bg-gradient-to-r from-green-600 to-blue-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Fresh Groceries Delivered to Your Door</h2>
            <p class="text-xl mb-8">Shop from thousands of products with same-day delivery</p>
            <button id="heroShop" class="bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                Shop Now
            </button>
        </div>
    </section>

    <!-- Categories Grid -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl font-bold text-gray-800 mb-8">Shop by Category</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                <?php
                // Fetch all categories from DB
                $query = "SELECT category_name, image FROM shop_categories ORDER BY category_name ASC";
                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()) {
                    $category = htmlspecialchars($row['category_name']);
                    $imagePath = $row['image']
                        ? 'catagories/' . htmlspecialchars($row['image'])
                        : 'images/default.png';

                    echo "<a href='categories.php?category=" . urlencode($category) . "' class='text-center group cursor-pointer block'>";
                    echo "<div class='bg-gray-100 p-6 rounded-full mb-3 group-hover:bg-gray-200 transition-colors'>";
                    echo "<img src='{$imagePath}' alt='{$category}' class='w-16 h-16 object-cover rounded-full mx-auto'>";
                    echo "</div>";
                    echo "<p class='font-medium text-gray-700'>{$category}</p>";
                    echo "</a>";
                }
                ?>
            </div>
        </div>
    </section>


    <!-- Featured Products -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-bold text-gray-800">Featured Products</h3>
                <button id="viewAllBtn" class="text-green-600 hover:text-green-700 font-medium">View All →</button>
            </div>
            <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                <!-- Products will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Cart Sidebar -->
    <div id="cartSidebar" class="fixed right-0 top-0 h-full w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-50">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold">Shopping Cart</h3>
                <button id="closeCart" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div id="cartItems" class="flex-1 overflow-y-auto p-6">
            <p class="text-gray-500 text-center py-8">Your cart is empty</p>
        </div>
        <div id="cartFooter" class="border-t p-6 bg-gray-50">
            <div class="flex justify-between items-center mb-4">
                <span class="text-lg font-semibold">Total:</span>
                <span id="cartTotal" class="text-xl font-bold text-green-600">৳0</span>
            </div>
            <!-- <button id="checkoutBtn" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors disabled:bg-gray-300" disabled>
                Proceed to Checkout
            </button> -->
            <button id="checkoutBtn" onclick="window.location.href='checkout.php'"
                class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors disabled:bg-gray-300"
                <?= empty($cartItems) ? 'disabled' : '' ?>>
                Proceed to Checkout
            </button>
        </div>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

    <!-- Login / Register Modal -->
    <div id="authModal" class="fixed inset-0 flex items-center justify-center z-60 hidden">
        <div class="bg-white rounded-lg shadow p-6 w-full max-w-md">
            <h3 id="authTitle" class="text-xl font-bold mb-4">Login</h3>
            <div id="authForms">
                <form id="loginForm" class="space-y-3">
                    <input name="email" required placeholder="Email" class="w-full px-3 py-2 border rounded" />
                    <input name="password" type="password" required placeholder="Password" class="w-full px-3 py-2 border rounded" />
                    <div class="flex justify-between items-center">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Login</button>
                        <button type="button" id="showRegister" class="text-sm text-green-600">Create account</button>
                    </div>
                </form>

                <form id="registerForm" class="space-y-3 hidden">
                    <input name="name" required placeholder="Full name" class="w-full px-3 py-2 border rounded" />
                    <input name="email" required placeholder="Email" class="w-full px-3 py-2 border rounded" />
                    <input name="password" type="password" required placeholder="Password" class="w-full px-3 py-2 border rounded" />
                    <div class="flex justify-between items-center">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Register</button>
                        <button type="button" id="showLogin" class="text-sm text-green-600">Already have an account</button>
                    </div>
                </form>
            </div>
            <div class="text-right mt-4">
                <button id="closeAuth" class="text-gray-500">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Utility: call API
        async function api(path, data = null, method = 'POST') {
            const opts = {
                method,
                headers: {}
            };
            if (data && !(data instanceof FormData)) {
                opts.headers['Content-Type'] = 'application/json';
                opts.body = JSON.stringify(data);
            } else if (data instanceof FormData) {
                opts.body = data;
            }
            const res = await fetch(path, opts);
            return res.json();
        }

        // Load products from backend
        async function loadProducts() {
            const res = await fetch('api/products.php');
            const products = await res.json();
            const grid = document.getElementById('productsGrid');
            grid.innerHTML = products.map(product => {
                const discountPrice = product.discount > 0 ? Math.round(product.price * (1 - product.discount / 100)) : null;
                return `
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow fade-in">
                    <div class="text-center mb-3">
                            <div class="flex items-center justify-center mb-2">
                                <img src="./${product.image}" alt="${product.name}" class="w-12 h-12 object-cover rounded" />
                            </div>
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
                    <button onclick="addToCart(${product.id})" 
                            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-colors font-medium">
                        Add to Cart
                    </button>
                </div>
            `;
            }).join('');
        }

        // Add to cart via API (uses session or logged-in user server-side)
        async function addToCart(productId) {
            const resp = await api('api/add_to_cart.php', {
                product_id: productId
            });
            if (resp.status === 'success') {
                bounceCart();
                await refreshCart();
            } else {
                alert(resp.msg || 'Failed to add to cart');
            }
        }

        function bounceCart() {
            const cartBtn = document.getElementById('cartBtn');
            cartBtn.classList.add('cart-bounce');
            setTimeout(() => cartBtn.classList.remove('cart-bounce'), 300);
        }

        // Get cart from server and render
        async function refreshCart() {
            const res = await fetch('api/get_cart.php');
            const items = await res.json();
            const cartCount = document.getElementById('cartCount');
            const cartItems = document.getElementById('cartItems');
            const cartTotal = document.getElementById('cartTotal');
            const checkoutBtn = document.getElementById('checkoutBtn');

            if (!Array.isArray(items)) {
                cartItems.innerHTML = '<p class="text-gray-500 text-center py-8">Your cart is empty</p>';
                cartCount.textContent = '0';
                cartTotal.textContent = '৳0';
                checkoutBtn.disabled = true;
                return;
            }

            const totalItems = items.reduce((s, it) => s + it.quantity, 0);
            const totalPrice = items.reduce((s, it) => s + (it.final_price * it.quantity), 0);

            cartCount.textContent = totalItems;
            cartTotal.textContent = `৳${totalPrice}`;

            if (items.length === 0) {
                cartItems.innerHTML = '<p class="text-gray-500 text-center py-8">Your cart is empty</p>';
                checkoutBtn.disabled = true;
            } else {
                cartItems.innerHTML = items.map(item => `
                <div class="flex items-center justify-between py-3 border-b">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl"><img src="./${item.image}" alt="${item.name}" class="w-12 h-12 object-cover rounded"></span>
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
                checkoutBtn.disabled = false;
            }
        }

        async function changeQuantity(cartId, delta) {
            const resp = await api('api/update_cart.php', {
                cart_id: cartId,
                change: delta
            });
            if (resp.status === 'updated') {
                await refreshCart();
            } else {
                alert('Failed update');
            }
        }

        // Cart sidebar toggle
        document.getElementById('cartBtn').addEventListener('click', function() {
            document.getElementById('cartSidebar').classList.remove('translate-x-full');
            document.getElementById('overlay').classList.remove('hidden');
            refreshCart();
        });

        document.getElementById('closeCart').addEventListener('click', function() {
            document.getElementById('cartSidebar').classList.add('translate-x-full');
            document.getElementById('overlay').classList.add('hidden');
        });

        document.getElementById('overlay').addEventListener('click', function() {
            document.getElementById('cartSidebar').classList.add('translate-x-full');
            document.getElementById('overlay').classList.add('hidden');
        });

        // Checkout
        document.getElementById('checkoutBtn').addEventListener('click', async function() {
            const resp = await api('api/checkout.php', {});
            if (resp.message) {
                alert(resp.message);
                await refreshCart();
                document.getElementById('cartSidebar').classList.add('translate-x-full');
                document.getElementById('overlay').classList.add('hidden');
            } else {
                alert(resp.msg || 'Checkout failed');
            }
        });

        // Search
        document.getElementById('searchInput').addEventListener('input', async function(e) {
            const term = e.target.value.trim();
            if (term === '') {
                await loadProducts();
                return;
            }
            const res = await fetch('api/products.php?q=' + encodeURIComponent(term));
            const products = await res.json();
            const grid = document.getElementById('productsGrid');
            grid.innerHTML = products.map(product => {
                const discountPrice = product.discount > 0 ? Math.round(product.price * (1 - product.discount / 100)) : null;
                return `
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow fade-in">
                    <div class="text-center mb-3">
                        <span class="text-2xl"><img src="/${product.image}" alt="${product.name}" class="w-12 h-12 object-cover rounded"> </img></span>
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
                    <button onclick="addToCart(${product.id})" 
                            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-colors font-medium">
                        Add to Cart
                    </button>
                </div>
            `;
            }).join('');
        });

        // Login/Register modal
        const authModal = document.getElementById('authModal');
        document.getElementById('loginBtn')?.addEventListener('click', () => {
            showLogin();
            authModal.classList.remove('hidden');
        });
        document.getElementById('closeAuth').addEventListener('click', () => authModal.classList.add('hidden'));

        function showLogin() {
            document.getElementById('authTitle').innerText = 'Login';
            document.getElementById('loginForm').classList.remove('hidden');
            document.getElementById('registerForm').classList.add('hidden');
        }

        function showRegister() {
            document.getElementById('authTitle').innerText = 'Create account';
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('registerForm').classList.remove('hidden');
        }

        document.getElementById('showRegister').addEventListener('click', showRegister);
        document.getElementById('showLogin').addEventListener('click', showLogin);

        // Submit login
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = new FormData(e.target);
            const resp = await fetch('api/login.php', {
                method: 'POST',
                body: form
            });
            const json = await resp.json();
            if (json.status === 'success') {
                location.reload();
            } else {
                alert(json.msg || 'Login failed');
            }
        });

        // Submit register
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = new FormData(e.target);
            const resp = await fetch('api/register.php', {
                method: 'POST',
                body: form
            });
            const json = await resp.json();
            if (json.status === 'success') {
                alert('Registration successful. Logged in.');
                location.reload();
            } else {
                alert(json.msg || 'Register failed');
            }
        });

        // Hero Shop Now
        document.getElementById('heroShop').addEventListener('click', () => {
            window.scrollTo({
                top: document.querySelector('#productsGrid').offsetTop - 80,
                behavior: 'smooth'
            });
        });

        // Init
        loadProducts();
        refreshCart();
    </script>
</body>

</html>