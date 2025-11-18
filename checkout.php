<?php
require_once "db.php";

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

// Get cart items (same logic you already use in get_cart.php)
function getCartItems($conn, $user_id = null)
{
    if ($user_id) {
        $stmt = $conn->prepare("
            SELECT c.id AS cart_id, c.quantity, 
                   p.id AS product_id, p.name, p.price, p.discount, p.image,
                   (p.price - (p.price * p.discount / 100)) AS final_price
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
    } else {
        $session_id = session_id();
        $stmt = $conn->prepare("
            SELECT c.id AS cart_id, c.quantity, 
                   p.id AS product_id, p.name, p.price, p.discount, p.image,
                   (p.price - (p.price * p.discount / 100)) AS final_price
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.session_id = ?
        ");
        $stmt->bind_param("s", $session_id);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$cartItems = getCartItems($conn, $user['id'] ?? null);

if (empty($cartItems)) {
    header("Location: index.php");
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['final_price'] * $item['quantity'];
}
$delivery_charge = 60;                 // you can change or make dynamic
$grand_total = $subtotal + $delivery_charge;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SmartCart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <h1 class="text-3xl font-bold text-green-700 mb-8">Checkout</h1>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Left side - Order Summary -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                    <?php foreach ($cartItems as $item):
                        $item_total = $item['final_price'] * $item['quantity'];
                    ?>
                        <div class="flex items-center justify-between py-4 border-b">
                            <div class="flex items-center space-x-4">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="" class="w-16 h-16 object-cover rounded">
                                <div>
                                    <h4 class="font-medium"><?= htmlspecialchars($item['name']) ?></h4>
                                    <p class="text-sm text-gray-600">
                                        <?= $item['quantity'] ?> × ৳<?= number_format($item['final_price']) ?>
                                        <?php if ($item['discount'] > 0): ?>
                                            <span class="text-xs text-red-600">(<?= $item['discount'] ?>% off)</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="font-semibold">৳<?= number_format($item_total) ?></div>
                        </div>
                    <?php endforeach; ?>

                    <div class="mt-6 space-y-2 text-lg">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>৳<?= number_format($subtotal) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Delivery Charge</span>
                            <span>৳<?= number_format($delivery_charge) ?></span>
                        </div>
                        <div class="flex justify-between font-bold text-xl text-green-700">
                            <span>Grand Total</span>
                            <span>৳<?= number_format($grand_total) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side - Shipping Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-6">Shipping Details</h2>

                    <form action="place_order.php" method="POST" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="full_name" required
                                value="<?= $user ? htmlspecialchars($user['name']) : '' ?>"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" required placeholder="01xxxxxxxxx"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                            <textarea name="address" rows="4" required placeholder="House no, Road, Area, City..."
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                        </div>

                        <!-- Hidden fields for calculation -->
                        <input type="hidden" name="total_amount" value="<?= $grand_total ?>">

                        <button type="submit"
                            class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                            Place Order – ৳<?= number_format($grand_total) ?>
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <a href="index.php" class="text-green-600 hover:underline text-sm">← Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>