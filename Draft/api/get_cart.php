<?php
require "db.php";

$user_id = $_GET['user_id'];

$sql = "SELECT cart.id, products.name, products.image, products.price, products.discount, 
    cart.quantity 
    FROM cart 
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id='$user_id'";

$result = mysqli_query($conn, $sql);

$items = [];

while($row = mysqli_fetch_assoc($result)){
    $row['final_price'] = ($row['discount'] > 0)
        ? round($row['price'] * (1 - $row['discount'] / 100))
        : $row['price'];

    $items[] = $row;
}

echo json_encode($items);
?>
