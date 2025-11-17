<?php
// File: C:\xampp\htdocs\Smartcart-Website\api\functions.php

// ONLY start session if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Correct path to db.php (db.php is in root folder)
require_once __DIR__ . '/../db.php';

/* ────────────────────── HELPER FUNCTIONS ────────────────────── */

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function current_user_id(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

function current_username(): ?string
{
    return $_SESSION['username'] ?? null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        // If inside admin folder → redirect to admin/login.php
        $in_admin_folder = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false);
        header('Location: ' . ($in_admin_folder ? 'login.php' : '/login.php'));
        exit;
    }
}

function is_admin(int $user_id = null): bool
{
    global $conn;

    $user_id = $user_id ?? current_user_id();
    if (!$user_id) return false;

    $stmt = $conn->prepare("SELECT 1 FROM admin WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 1;
}

function require_admin(): void
{
    require_login(); // first must be logged in

    if (!is_admin()) {
        // Redirect to admin login if not admin
        header('Location: login.php');
        exit;
    }
}

function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

function escape(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
