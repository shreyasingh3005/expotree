<?php
require_once __DIR__ . '/../includes/db.php';
$users = $pdo->query("SELECT id, username, email, role, status FROM admin_users")->fetchAll(PDO::FETCH_ASSOC);
print_r($users);
