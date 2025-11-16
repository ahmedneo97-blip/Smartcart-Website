<?php
require_once "../db.php";
header("Content-Type: application/json");

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $password === '') {
    echo json_encode(["status" => "error", "msg" => "Email and password required"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["status" => "error", "msg" => "User not found"]);
    exit;
}

$user = $res->fetch_assoc();
if (password_verify($password, $user['password'])) {
    unset($user['password']);
    $_SESSION['user'] = $user;

    // If there are cart rows with this session_id, migrate them to user_id
    $session_id = session_id();
    $migrate = $conn->prepare("UPDATE cart SET user_id = ?, session_id = NULL WHERE session_id = ?");
    $migrate->bind_param("is", $user['id'], $session_id);
    $migrate->execute();

    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "msg" => "Wrong password"]);
}
