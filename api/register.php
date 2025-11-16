<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($name === '' || $email === '' || $password === '') {
    echo json_encode(['status' => 'error', 'msg' => 'Name, email and password required']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Email already registered']);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hash);

if ($stmt->execute()) {
    $id = $stmt->insert_id;
    $_SESSION['user'] = ['id' => $id, 'name' => $name, 'email' => $email, 'role' => 'user'];
    // migrate cart
    $session_id = session_id();
    $m = $conn->prepare("UPDATE cart SET user_id = ?, session_id = NULL WHERE session_id = ?");
    $m->bind_param("is", $id, $session_id);
    $m->execute();

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $conn->error]);
}
