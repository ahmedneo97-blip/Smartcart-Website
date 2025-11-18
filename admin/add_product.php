<?php


require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../api/functions.php';
require_once __DIR__ . '/../api/upload_image.php'; // <-- new helper

if (!is_logged_in() || !is_admin()) {
  header('Location: /login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'] ?? '';
  $price = intval($_POST['price'] ?? 0);
  $unit = $_POST['unit'] ?? '';
  $category = $_POST['category'] ?? '';
  $discount = intval($_POST['discount'] ?? 0);

  // handle uploaded image file (from <input type="file" name="image">)
  $imageFilename = '';
  if (isset($_FILES['image'])) {
      $res = upload_product_image($_FILES['image']); // returns ['success'=>bool,'filename'=>str,'error'=>str]
      if ($res['success']) {
          $imageFilename = $res['filename'];
      } else {
          // optional: set $error to show message in form
          $error = $res['error'];
      }
  }

  $stmt = $conn->prepare("INSERT INTO products (name, price, unit, image, category, discount) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sisssi", $name, $price, $unit, $imageFilename, $category, $discount);
  if ($stmt->execute()) {
    header("Location: index.php");
    exit;
  } else {
    $error = $conn->error;
  }
}
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Add Product</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-6 bg-gray-50">
  <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl mb-4">Add Product</h1>
    <?php if (!empty($error)): ?><div class="text-red-600 mb-3"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <input name="name" placeholder="Name" class="w-full border px-3 py-2 mb-3" required>
      <input name="price" type="number" placeholder="Price" class="w-full border px-3 py-2 mb-3" required>
      <input name="unit" placeholder="Unit" class="w-full border px-3 py-2 mb-3">
      <input name="image" type="file" accept="image/*" class="w-full border px-3 py-2 mb-3">
      <input name="category" placeholder="Category" class="w-full border px-3 py-2 mb-3">
      <script>document.querySelector('form').setAttribute('enctype','multipart/form-data');</script>
      <input name="discount" type="number" placeholder="Discount %" value="0" class="w-full border px-3 py-2 mb-3">
      <button class="bg-green-600 text-white px-4 py-2 rounded">Add</button>
    </form>
  </div>
</body>

</html>