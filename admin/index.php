<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin - Products</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="p-6 bg-gray-50">
  <div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Admin - Products</h1>
    <a href="add_product.php" class="bg-green-600 text-white px-3 py-2 rounded">Add Product</a>
    <table class="w-full mt-4 bg-white">
      <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Discount</th><th>Category</th><th>Actions</th></tr></thead>
      <tbody>
      <?php while($p = $res->fetch_assoc()): ?>
        <tr>
          <td><?php echo $p['id']; ?></td>
          <td><?php echo htmlspecialchars($p['name']); ?></td>
          <td><?php echo $p['price']; ?></td>
          <td><?php echo $p['discount']; ?></td>
          <td><?php echo htmlspecialchars($p['category']); ?></td>
          <td>
            <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="px-2">Edit</a>
            <form method="post" style="display:inline-block" onsubmit="return confirm('Delete product?');">
              <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
              <button name="delete" class="text-red-600">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
