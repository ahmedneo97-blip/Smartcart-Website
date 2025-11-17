<?php
session_start();

// FIX 1: Correct paths from admin/ folder
require_once __DIR__ . '/../db.php';           // goes up one level to root
require_once __DIR__ . '/../api/functions.php'; // your helper functions

// FIX 2: Proper login & admin check
if (!isset($_SESSION['user_id']) || !is_admin($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// FIX 3: Delete product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'], $_POST['id'])) {
  $id = (int)$_POST['id'];
  $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  // Optional: add success message
  $_SESSION['message'] = "Product deleted successfully!";
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// Fetch all products
$stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Products</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/css/styles.css">
</head>

<body class="bg-gray-100 min-h-screen">

  <div class="container mx-auto p-6 max-w-6xl">
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Admin - Products Management</h1>
        <a href="/logout.php" class="text-red-600 hover:underline">Logout</a>
      </div>

      <?php if (isset($_SESSION['message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          <?= htmlspecialchars($_SESSION['message']) ?>
          <?php unset($_SESSION['message']); ?>
        </div>
      <?php endif; ?>

      <div class="mb-6">
        <a href="add_product.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg inline-block">
          Add New Product
        </a>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
          <thead>
            <tr class="bg-gray-800 text-white">
              <th class="px-4 py-3 text-left">ID</th>
              <th class="px-4 py-3 text-left">Name</th>
              <th class="px-4 py-3 text-left">Price</th>
              <th class="px-4 py-3 text-left">Discount</th>
              <th class="px-4 py-3 text-left">Category</th>
              <th class="px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($res->num_rows === 0): ?>
              <tr>
                <td colspan="6" class="text-center py-8 text-gray-500">No products found</td>
              </tr>
            <?php else: ?>
              <?php while ($p = $res->fetch_assoc()): ?>
                <tr class lac="border-b hover:bg-gray-50">
                  <td class="px-4 py-3"><?= $p['id'] ?></td>
                  <td class="px-4 py-3 font-medium"><?= htmlspecialchars($p['name']) ?></td>
                  <td class="px-4 py-3">৳<?= number_format($p['price'], 2) ?></td>
                  <td class="px-4 py-3"><?= $p['discount'] ?>%</td>
                  <td class="px-4 py-3"><?= htmlspecialchars($p['category']) ?></td>
                  <td class="px-4 py-3 text-center space-x-3">
                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                    <form method="POST" class="inline" onsubmit="return confirm('Sure to delete this product?');">
                      <input type="hidden" name="id" value="<?= $p['id'] ?>">
                      <button type="submit" name="delete" class="text-red-600 hover:underline">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</body>

</html>