<?php
require_once "../db.php";
header("Content-Type: application/json");

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($name === '' || $email === '' || $password === '') {
    echo json_encode(["status" => "error", "msg" => "Name, email and password are required"]);
    exit;
}

// check existing email
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    echo json_encode(["status" => "error", "msg" => "Email already registered"]);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hash);
if ($stmt->execute()) {
    $id = $stmt->insert_id;
    // log in the new user
    $_SESSION['user'] = ["id" => $id, "name" => $name, "email" => $email];

    // migrate cart session -> user
    $session_id = session_id();
    $migrate = $conn->prepare("UPDATE cart SET user_id = ?, session_id = NULL WHERE session_id = ?");
    $migrate->bind_param("is", $id, $session_id);
    $migrate->execute();

    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "msg" => "Failed: " . $conn->error]);
}
