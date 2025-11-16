<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!is_logged_in() || !is_admin()) { header('Location: /login.php'); exit; }

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = intval($_POST['price'] ?? 0);
    $unit = $_POST['unit'] ?? '';
    $image = $_POST['image'] ?? '';
    $category = $_POST['category'] ?? '';
    $discount = intval($_POST['discount'] ?? 0);

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, unit=?, image=?, category=?, discount=? WHERE id=?");
    $stmt->bind_param("sisssii", $name, $price, $unit, $image, $category, $discount, $id);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error = $conn->error;
    }
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();
if (!$product) { header('Location: index.php'); exit; }
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Edit Product</title><script src="https://cdn.tailwindcss.com"></script></head><body class="p-6 bg-gray-50">
  <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl mb-4">Edit Product</h1>
    <?php if (!empty($error)): ?><div class="text-red-600 mb-3"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <input name="name" value="<?php echo htmlspecialchars($product['name']); ?>" placeholder="Name" class="w-full border px-3 py-2 mb-3" required>
      <input name="price" value="<?php echo $product['price']; ?>" type="number" placeholder="Price" class="w-full border px-3 py-2 mb-3" required>
      <input name="unit" value="<?php echo htmlspecialchars($product['unit']); ?>" placeholder="Unit" class="w-full border px-3 py-2 mb-3">
      <input name="image" value="<?php echo htmlspecialchars($product['image']); ?>" placeholder="Emoji or image URL" class="w-full border px-3 py-2 mb-3">
      <input name="category" value="<?php echo htmlspecialchars($product['category']); ?>" placeholder="Category" class="w-full border px-3 py-2 mb-3">
      <input name="discount" value="<?php echo $product['discount']; ?>" type="number" placeholder="Discount %" class="w-full border px-3 py-2 mb-3">
      <button class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
    </form>
  </div>
</body></html>
