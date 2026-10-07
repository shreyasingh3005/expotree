<?php
/**
 * API: Stall Price Calculator
 * Expo Tree Exhibitions
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

try {
    $eventId = (int)($_GET['event_id'] ?? $_POST['event_id'] ?? 0);
    $stallType = sanitize($_GET['stall_type'] ?? $_POST['stall_type'] ?? 'Shopping Canopy');
    $startDate = sanitize($_GET['start_date'] ?? $_POST['start_date'] ?? '');
    $endDate = sanitize($_GET['end_date'] ?? $_POST['end_date'] ?? '');
    $stallsCount = max(1, (int)($_GET['stalls_count'] ?? $_POST['stalls_count'] ?? 1));
    $hasFan = !empty($_GET['addon_fan'] ?? $_POST['addon_fan'] ?? 0);

    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Event not found.']);
        exit;
    }

    if (empty($startDate)) $startDate = $event['start_date'];
    if (empty($endDate)) $endDate = $event['end_date'];

    $startTs = strtotime($startDate);
    $endTs = strtotime($endDate);
    $daysCount = max(1, round(($endTs - $startTs) / 86400) + 1);

    // Rate
    $ratePerDay = (float)$event['daily_stall_price'];
    if (stripos($stallType, 'Shopping Canopy') !== false && !empty($event['price_shopping_canopy'])) {
        $ratePerDay = (float)$event['price_shopping_canopy'];
    } elseif (stripos($stallType, '1 Open Table') !== false && !empty($event['price_shopping_table1'])) {
        $ratePerDay = (float)$event['price_shopping_table1'];
    } elseif (stripos($stallType, '2 Open Table') !== false && !empty($event['price_shopping_table2'])) {
        $ratePerDay = (float)$event['price_shopping_table2'];
    } elseif (stripos($stallType, 'Food Canopy') !== false && !empty($event['price_food_canopy'])) {
        $ratePerDay = (float)$event['price_food_canopy'];
    } elseif (stripos($stallType, 'Food 2') !== false && !empty($event['price_food_table2'])) {
        $ratePerDay = (float)$event['price_food_table2'];
    }

    $stallsSubtotal = $ratePerDay * $stallsCount * $daysCount;
    $addonsTotal = $hasFan ? (300.00 * $daysCount * $stallsCount) : 0.00;
    $totalAmount = $stallsSubtotal + $addonsTotal;

    echo json_encode([
        'status' => 'success',
        'event_id' => $event['id'],
        'event_title' => $event['title'],
        'stall_type' => $stallType,
        'rate_per_day' => $ratePerDay,
        'stalls_count' => $stallsCount,
        'days_count' => $daysCount,
        'stalls_subtotal' => $stallsSubtotal,
        'addons_total' => $addonsTotal,
        'total_amount' => $totalAmount,
        'available_stalls' => $event['available_stalls']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Calculator error.']);
}
