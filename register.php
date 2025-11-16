<?php
require_once __DIR__ . '/includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = "All fields required";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $error = "Email already registered";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $ins->bind_param("sss", $name, $email, $hash);
            if ($ins->execute()) {
                $id = $ins->insert_id;
                $_SESSION['user'] = ['id'=>$id, 'name'=>$name, 'email'=>$email, 'role'=>'user'];
                // migrate cart
                $session_id = session_id();
                $m = $conn->prepare("UPDATE cart SET user_id = ?, session_id = NULL WHERE session_id = ?");
                $m->bind_param("is", $id, $session_id);
                $m->execute();
                header("Location: /");
                exit;
            } else {
                $error = "DB error: " . $conn->error;
            }
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Register</title><script src="https://cdn.tailwindcss.com"></script></head><body class="p-6">
  <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Register</h2>
    <?php if (!empty($error)): ?><div class="text-red-600 mb-3"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <input name="name" placeholder="Full name" class="w-full border px-3 py-2 mb-3" required>
      <input name="email" placeholder="Email" class="w-full border px-3 py-2 mb-3" required>
      <input name="password" type="password" placeholder="Password" class="w-full border px-3 py-2 mb-3" required>
      <button class="bg-green-600 text-white px-4 py-2 rounded">Register</button>
    </form>
    <p class="mt-4 text-sm">Already have an account? <a href="/login.php" class="text-green-600">Login</a></p>
  </div>
</body></html>
