<?php
require_once "db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit;
}

$user = $_SESSION['user'] ?? null;
$user_id = $user['id'] ?? null;
$session_id = $user_id ? null : session_id();

$full_name = trim($_POST['full_name']);
$phone     = trim($_POST['phone']);
$address   = trim($_POST['address']);
$total     = (int)$_POST['total_amount'];

if (empty($full_name) || empty($phone) || empty($address)) {
    die("All fields are required.");
}

// 1. Insert main order
$stmt = $conn->prepare("INSERT INTO orders (user_id, session_id, full_name, phone, address, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssi", $user_id, $session_id, $full_name, $phone, $address, $total);
$stmt->execute();
$order_id = $conn->insert_id;
$stmt->close();

// 2. Get current cart items (same function you already use)
function getCartItems($conn, $user_id = null)
{
    if ($user_id) {
        $sql = "SELECT c.quantity, p.id AS product_id, p.price, p.discount, p.name,
                       (p.price - (p.price * p.discount / 100)) AS final_price
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
    } else {
        $sid = session_id();
        $sql = "SELECT c.quantity, p.id AS product_id, p.price, p.discount, p.name,
                       (p.price - (p.price * p.discount / 100)) AS final_price
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.session_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $sid);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$items = getCartItems($conn, $user_id);

// 3. Insert order items
$stmt = $conn->prepare("INSERT INTO order_items 
    (order_id, product_id, quantity, unit_price, discount_percent, final_price) 
    VALUES (?, ?, ?, ?, ?, ?)");

foreach ($items as $it) {
    $unit_price = $it['price'];
    $disc       = $it['discount'];
    $final      = $it['final_price'];
    $stmt->bind_param("iiiiii", $order_id, $it['product_id'], $it['quantity'], $unit_price, $disc, $final);
    $stmt->execute();
}
$stmt->close();

// 4. Clear cart
if ($user_id) {
    $conn->query("DELETE FROM cart WHERE user_id = $user_id");
} else {
    $sid = session_id();
    $conn->query("DELETE FROM cart WHERE session_id = '$sid'");
}

$_SESSION['success_message'] = "Order placed successfully! Order ID: #$order_id";

// Redirect to a thank-you page or back to home
header("Location: order_success.php");
exit;
