<?php
require "db.php";

$cart_id = $_POST['cart_id'];
$change = $_POST['change'];  // +1 or -1

mysqli_query($conn, "UPDATE cart SET quantity = quantity + $change WHERE id='$cart_id'");

mysqli_query($conn, "DELETE FROM cart WHERE quantity <= 0");

echo json_encode(["status" => "updated"]);
?>
