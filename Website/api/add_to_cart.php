<?php
require_once "../db.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents('php://input'), true);
$product_id = isset($input['product_id']) ? intval($input['product_id']) : null;

if (!$product_id) {
    echo json_encode(["status" => "error", "msg" => "No product id supplied"]);
    exit;
}

$session_id = session_id();
$user_id = isset($_SESSION['user']) ? intval($_SESSION['user']['id']) : null;

// Try update existing cart row (session or user)
if ($user_id) {
    // logged in: prefer user_id
    $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $stmt2 = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
        $stmt2->bind_param("i", $row['id']);
        $stmt2->execute();
    } else {
        $stmt2 = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt2->bind_param("ii", $user_id, $product_id);
        $stmt2->execute();
    }
} else {
    // guest: use session_id
    $stmt = $conn->prepare("SELECT id FROM cart WHERE session_id = ? AND product_id = ?");
    $stmt->bind_param("si", $session_id, $product_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $stmt2 = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
        $stmt2->bind_param("i", $row['id']);
        $stmt2->execute();
    } else {
        $stmt2 = $conn->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt2->bind_param("si", $session_id, $product_id);
        $stmt2->execute();
    }
}

echo json_encode(["status" => "success"]);
