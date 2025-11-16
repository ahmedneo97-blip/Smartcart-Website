<?php
// Start session (if you want admin login protection)
session_start();

// OPTIONAL: Admin auth check
// if (!isset($_SESSION['admin_logged_in'])) {
//     header("Location: login.php");
//     exit();
// }

// Database connection
include '../includes/db.php';

// Check if product ID exists in URL
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Delete product
    $query = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        // Redirect back with success
        header("Location: products.php?msg=deleted");
        exit();
    } else {
        // Redirect back with error
        header("Location: products.php?msg=error");
        exit();
    }
} else {
    // If no ID provided
    header("Location: products.php?msg=invalid");
    exit();
}
?>
