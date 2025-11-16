<?php
require "db.php";

$user_id = $_POST['user_id'];

mysqli_query($conn, "DELETE FROM cart WHERE user_id='$user_id'");

echo json_encode(["message" => "Checkout successful!"]);
?>
