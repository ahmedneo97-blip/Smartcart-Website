<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$cart_id = isset($input['cart_id']) ? intval($input['cart_id']) : 0;
$change = isset($input['change']) ? intval($input['change']) : 0;

if (!$cart_id || $change === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid parameters']);
    exit;
}

$stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
$stmt->bind_param("ii", $change, $cart_id);
$stmt->execute();

$stmt2 = $conn->prepare("DELETE FROM cart WHERE id = ? AND quantity <= 0");
$stmt2->bind_param("i", $cart_id);
$stmt2->execute();

echo json_encode(['status' => 'updated']);
