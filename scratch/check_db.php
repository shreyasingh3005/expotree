<?php
require_once __DIR__ . '/../includes/db.php';
$stmt = $pdo->query("SELECT id, title, image_url FROM events ORDER BY id ASC");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID " . $r['id'] . " | " . $r['title'] . "\n   -> " . $r['image_url'] . "\n";
}
