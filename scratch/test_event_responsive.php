<?php
require_once __DIR__ . '/../includes/db.php';
$events = $pdo->query('SELECT id, slug, title FROM events LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
foreach ($events as $ev) {
    $url = 'http://localhost/expotree/event.php?slug=' . $ev['slug'];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "ID {$ev['id']}: {$ev['title']} => HTTP $code\n";
}
