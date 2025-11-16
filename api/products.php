<?php
// api/products.php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q === '') {
    $stmt = $conn->prepare("SELECT id, name, price, unit, image, category, discount FROM products ORDER BY id ASC");
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $like = "%{$q}%";
    $stmt = $conn->prepare("SELECT id, name, price, unit, image, category, discount FROM products WHERE name LIKE ? OR category LIKE ? ORDER BY id ASC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $res = $stmt->get_result();
}

$rows = [];
while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
}
echo json_encode($rows);
