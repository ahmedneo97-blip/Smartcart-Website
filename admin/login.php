<?php
// admin/login.php
session_start();

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../api/functions.php';

// Redirect if already logged in as admin
if (isset($_SESSION['user_id']) && is_admin($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// echo password_hash("admin123", PASSWORD_BCRYPT);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please fill both fields';
    } else {

        $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $admin = $result->fetch_assoc();

            // Verify hashed password
            if (password_verify($password, $admin['password'])) {

                // Login success
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['username'] = $username;

                header('Location: index.php');
                exit;
            } else {
                $error = 'Wrong password';
            }
        } else {
            $error = 'Admin not found';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login - Smartcart</title>
</head>

<body style="font-family: Arial; background: #f4f4f4; padding: 50px;">
    <div style="max-width: 400px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center;">Admin Login</h2>

        <?php if ($error): ?>
            <p style="color: red; background: #ffebee; padding: 10px; border-radius: 5px; text-align: center;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="post">
            <p>
                <label><strong>Username</strong><br>
                    <input type="text" name="username" required autofocus
                        style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px;">
                </label>
            </p>

            <p>
                <label><strong>Password</strong><br>
                    <input type="password" name="password" required
                        style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px;">
                </label>
            </p>

            <p>
                <button type="submit"
                    style="width: 100%; padding: 12px; background: #1976d2; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                    Login
                </button>
            </p>
        </form>

        <hr>
        <p style="text-align: center; color: #555; font-size: 14px;">
            <strong>Test Account:</strong><br>
            Username: <code>admin</code><br>
            Password: <code>admin123</code><br>
            <span style="font-size:12px; color:#999;">(Make sure your DB has a bcrypt hash)</span>
        </p>
    </div>
</body>

</html>