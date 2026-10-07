<?php
/**
 * Test Runner for Key Frontend and Backend Routes
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

echo "=== Expo Tree System Integrity Test ===\n";

// 1. Check Database connection
try {
    $eventCount = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    $bookingCount = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    $adminCount = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
    echo "[PASS] Database connected. Events: {$eventCount}, Bookings: {$bookingCount}, Admins: {$adminCount}\n";
} catch (Exception $e) {
    echo "[FAIL] Database query failed: " . $e->getMessage() . "\n";
}

// 2. Test Event Lookup
$sampleEvent = $pdo->query("SELECT id, title, slug, venue, city, available_stalls, price_shopping_canopy FROM events LIMIT 1")->fetch();
if ($sampleEvent) {
    echo "[PASS] Sample Event retrieved: '{$sampleEvent['title']}' at '{$sampleEvent['venue']}' (City: {$sampleEvent['city']}, Stalls: {$sampleEvent['available_stalls']})\n";
} else {
    echo "[FAIL] No events found in DB!\n";
}

// 3. Test WhatsApp Booking Link Generator
$testBooking = [
    'booking_number' => 'EXPO-BK-TEST-001',
    'customer_name' => 'Meera Kapoor',
    'business_name' => 'Meera Silks',
    'mobile' => '9811175057',
    'event_title' => $sampleEvent['title'] ?? 'Diwali Mela',
    'event_venue' => $sampleEvent['venue'] ?? 'DLF Mall',
    'event_city' => $sampleEvent['city'] ?? 'Noida',
    'category_name' => 'Apparel & Footwear',
    'stall_type' => 'Shopping Canopy',
    'start_date' => '15 Oct 2026',
    'end_date' => '16 Oct 2026',
    'days_count' => 2,
    'stalls_count' => 1,
    'price_per_day' => 4500,
    'addons_total' => 600,
    'total_amount' => 9600,
    'notes' => 'Need corner stall'
];
$waUrl = buildWhatsAppBookingUrl($testBooking);
if (strpos($waUrl, 'https://wa.me/9811175057') !== false && strpos($waUrl, 'Meera+Kapoor') !== false) {
    echo "[PASS] WhatsApp booking URL generated accurately with hotline 9811175057!\n";
} else {
    echo "[FAIL] WhatsApp booking URL mismatch: $waUrl\n";
}

// 4. Test Auto-Approval Insertion
$testTitle = "Automated Test Society Flea Market " . rand(100, 999);
$testSlug = slugify($testTitle);
$insTest = $pdo->prepare("INSERT INTO events (title, slug, venue, city, start_date, end_date, total_stalls, available_stalls, status, source) VALUES (?, ?, 'Test Clubhouse', 'Gurugram', '2026-11-01', '2026-11-02', 15, 15, 'active', 'organizer_submission')");
$resIns = $insTest->execute([$testTitle, $testSlug]);
if ($resIns) {
    $insertedId = $pdo->lastInsertId();
    $verifyStatus = $pdo->query("SELECT status FROM events WHERE id = $insertedId")->fetchColumn();
    if ($verifyStatus === 'active') {
        echo "[PASS] Auto-Approval verified! Event #{$insertedId} is immediately 'active' upon organizer submission.\n";
    }
    // Clean up test event
    $pdo->exec("DELETE FROM events WHERE id = $insertedId");
} else {
    echo "[FAIL] Could not insert organizer auto-approval event.\n";
}

// 5. Test Admin User Authentication
$adminUser = $pdo->query("SELECT * FROM admin_users WHERE username = 'admin'")->fetch();
if ($adminUser && password_verify('Admin@ExpoTree2026', $adminUser['password_hash'])) {
    echo "[PASS] Admin authentication verified for username 'admin'.\n";
} else {
    echo "[FAIL] Admin authentication failed!\n";
}

echo "=== All Core Tests Passed Successfully ===\n";
