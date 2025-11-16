<?php
require_once "../db.php";
header("Content-Type: application/json");

$session_id = session_id();
$user_id = isset($_SESSION['user']) ? intval($_SESSION['user']['id']) : null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT cart.id, p.name, p.image, p.price, p.discount, cart.quantity,
        (CASE WHEN p.discount > 0 THEN ROUND(p.price * (1 - p.discount/100)) ELSE p.price END) AS final_price
        FROM cart
        JOIN products p ON cart.product_id = p.id
        WHERE cart.user_id = ?");
    $stmt->bind_param("i", $user_id);
} else {
    $stmt = $conn->prepare("SELECT cart.id, p.name, p.image, p.price, p.discount, cart.quantity,
        (CASE WHEN p.discount > 0 THEN ROUND(p.price * (1 - p.discount/100)) ELSE p.price END) AS final_price
        FROM cart
        JOIN products p ON cart.product_id = p.id
        WHERE cart.session_id = ?");
    $stmt->bind_param("s", $session_id);
}

$stmt->execute();
$res = $stmt->get_result();
$items = [];
while ($row = $res->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
