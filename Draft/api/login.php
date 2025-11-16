<?php
require "db.php";

$email = $_POST['email'];
$password = $_POST['password'];

$result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($result) == 0){
    echo json_encode(["status" => "error", "msg" => "User not found"]);
    exit;
}

$user = mysqli_fetch_assoc($result);

if(password_verify($password, $user['password'])){
    echo json_encode(["status"=>"success", "user"=>$user]);
} else {
    echo json_encode(["status"=>"error", "msg"=>"Wrong password"]);
}
?>
