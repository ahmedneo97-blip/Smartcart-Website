<?php
require_once __DIR__ . '/includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        $error = "User not found";
    } else {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            // migrate guest cart
            $session_id = session_id();
            $m = $conn->prepare("UPDATE cart SET user_id = ?, session_id = NULL WHERE session_id = ?");
            $m->bind_param("is", $user['id'], $session_id);
            $m->execute();
            header("Location: /");
            exit;
        } else {
            $error = "Wrong password";
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title><script src="https://cdn.tailwindcss.com"></script></head><body class="p-6">
  <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Login</h2>
    <?php if (!empty($error)): ?><div class="text-red-600 mb-3"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <input name="email" placeholder="Email" class="w-full border px-3 py-2 mb-3" required>
      <input name="password" type="password" placeholder="Password" class="w-full border px-3 py-2 mb-3" required>
      <button class="bg-green-600 text-white px-4 py-2 rounded">Login</button>
    </form>
    <p class="mt-4 text-sm">Don't have an account? <a href="/register.php" class="text-green-600">Register</a></p>
  </div>
</body></html>
