<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$user_id = isset($_SESSION['user']) ? intval($_SESSION['user']['id']) : null;
$session_id = session_id();

if ($user_id) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ?");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
}

echo json_encode(['message' => 'Thank you for your order! This demo clears the cart server-side. In production redirect to a payment gateway.']);
