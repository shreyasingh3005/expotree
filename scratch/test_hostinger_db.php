<?php
$host = 'orangered-cobra-615456.hostingersite.com';
$user = 'u424679052_expotree123';
$pass = 'yY7S@C=#';
$db   = 'u424679052_expotree';

echo "Testing connection to Hostinger MySQL at $host...\n";
try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    echo "[SUCCESS] Connected directly to remote Hostinger database!\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in remote DB: " . implode(', ', $tables) . "\n";
} catch (Exception $e) {
    echo "[NOTE] Direct remote MySQL connection failed (standard Hostinger security blocks external IPs): " . $e->getMessage() . "\n";
}
