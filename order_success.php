<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Placed - SmartCart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-lg shadow-lg text-center max-w-md">
        <svg class="w-20 h-20 text-green-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <h1 class="text-2xl font-bold text-green-700 mb-2">Order Placed Successfully!</h1>
        <?php if (isset($_SESSION['success_message'])): ?>
            <p class="text-gray-700 mb-6"><?= $_SESSION['success_message'];
                                            unset($_SESSION['success_message']); ?></p>
        <?php endif; ?>
        <a href="index.php" class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
            Continue Shopping
        </a>
    </div>
</body>

</html>