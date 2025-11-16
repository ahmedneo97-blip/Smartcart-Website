<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(['status' => 'error', 'msg' => 'Email and password required']);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'User not found']);
    exit;
}

$user = $res->fetch_assoc();
if (password_verify($password, $user['password'])) {
    unset($user['password']);
    $_SESSION['user'] = $user;
    // migrate cart
    $session_id = session_id();
    $m = $conn->prepare("UPDATE cart SET user_id = ?, session_id = NULL WHERE session_id = ?");
    $m->bind_param("is", $user['id'], $session_id);
    $m->execute();
    echo json_encode(['status' => 'success', 'user' => $user]);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Wrong password']);
}
