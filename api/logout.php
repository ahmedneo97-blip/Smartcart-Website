<?php
require_once __DIR__ . '/../includes/db.php';
$_SESSION = [];
session_regenerate_id(true);
header('Location: /');
exit;
