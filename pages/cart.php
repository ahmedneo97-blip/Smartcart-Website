<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Your Cart</title><script src="https://cdn.tailwindcss.com"></script></head><body class="p-6">
  <div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Your Cart</h1>
    <div id="cartItems"></div>
    <a href="/" class="text-green-600">Continue shopping</a>
  </div>
  <script src="/js/script.js"></script>
</body></html>
