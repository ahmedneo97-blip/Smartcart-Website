<?php
require "db.php";

$name = $_POST['name'];
$email = $_POST['email'];
$pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

$sql = "INSERT INTO users(name, email, password) VALUES('$name','$email','$pass')";

if(mysqli_query($conn, $sql)){
    echo json_encode(["status"=>"success"]);
} else {
    echo json_encode(["status"=>"error", "msg"=>mysqli_error($conn)]);
}
?>
