<?php
require_once __DIR__ . '/../includes/db.php';

echo "=== Comprehensive Database Check ===\n";

$events = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
echo "Total Events: $events\n";

$bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
echo "Total Bookings: $bookings\n";

$shoppers = $pdo->query("SELECT COUNT(*) FROM shopper_passes")->fetchColumn();
echo "Total VIP Shoppers: $shoppers\n";

$admins = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
echo "Total Admin Users: $admins\n";

echo "\n--- Recent Bookings ---\n";
$bRows = $pdo->query("SELECT booking_number, customer_name, business_name, total_amount, status FROM bookings ORDER BY id DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
foreach ($bRows as $r) {
    echo "- {$r['booking_number']} | {$r['customer_name']} ({$r['business_name']}) | ₹{$r['total_amount']} | {$r['status']}\n";
}

echo "\n--- Recent Shopper Passes ---\n";
$sRows = $pdo->query("SELECT pass_code, name, phone, city FROM shopper_passes ORDER BY id DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
foreach ($sRows as $sr) {
    echo "- {$sr['pass_code']} | {$sr['name']} | {$sr['phone']} | {$sr['city']}\n";
}
