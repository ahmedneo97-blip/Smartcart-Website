<?php
require_once "db.php";

$category = isset($_GET['category']) ? trim($_GET['category']) : '';

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Products - SmartCart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="container mx-auto px-4 py-8">
        <a href="index.php" class="text-green-600 hover:underline">&larr; Back to Home</a>
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Products in Category: <?php echo htmlspecialchars($category); ?></h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php
        if ($category) {
            $stmt = $conn->prepare("SELECT id, name, price, unit, image, discount FROM products WHERE category LIKE ?");
            $like = "%$category%";
            $stmt->bind_param("s", $like);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $discountPrice = $row['discount'] > 0 ? round($row['price'] * (1 - $row['discount'] / 100)) : null;
                    echo "<div class='bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow'>";
                    echo "<div class='text-center mb-3'><div class='text-6xl mb-2'>" . htmlspecialchars($row['image']) . "</div>";
                    if ($row['discount'] > 0) echo "<span class='bg-red-500 text-white text-xs px-2 py-1 rounded-full'>{$row['discount']}% OFF</span>";
                    echo "</div>";
                    echo "<h4 class='font-semibold text-gray-800 mb-2'>" . htmlspecialchars($row['name']) . "</h4>";
                    echo "<p class='text-sm text-gray-600 mb-3'>" . htmlspecialchars($row['unit']) . "</p>";
                    echo "<div class='flex items-center justify-between mb-3'><div>";
                    if ($discountPrice) {
                        echo "<span class='text-lg font-bold text-green-600'>৳$discountPrice</span> <span class='text-sm text-gray-500 line-through ml-1'>৳" . htmlspecialchars($row['price']) . "</span>";
                    } else {
                        echo "<span class='text-lg font-bold text-green-600'>৳" . htmlspecialchars($row['price']) . "</span>";
                    }
                    echo "</div></div>";
                    echo "<form method='post' action='api/add_to_cart.php'>";
                    echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-colors font-medium'>Add to Cart</button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<div class='col-span-full text-center text-gray-500 text-lg'>No products found in this category.</div>";
            }
        } else {
            echo "<div class='col-span-full text-center text-gray-500 text-lg'>No category selected.</div>";
        }
        ?>
        </div>
    </div>
</body>
</html>