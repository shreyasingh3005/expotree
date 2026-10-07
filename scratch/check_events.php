<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$events = $pdo->query("SELECT id, title, venue, city, start_date, end_date, date_display, price_shopping_canopy, price_shopping_table1, status FROM events ORDER BY start_date ASC")->fetchAll();
echo "Total events in DB: " . count($events) . "\n";
foreach ($events as $ev) {
    echo "- #{$ev['id']} | {$ev['venue']} ({$ev['city']}) | {$ev['start_date']} to {$ev['end_date']} [{$ev['date_display']}] | Status: {$ev['status']}\n";
}
