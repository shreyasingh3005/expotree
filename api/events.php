<?php
/**
 * API: Get Upcoming Events (JSON)
 * Expo Tree Exhibitions
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

try {
    $city = sanitize($_GET['city'] ?? 'all');
    $type = sanitize($_GET['type'] ?? 'all');
    $limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));

    $sql = "SELECT id, title, slug, category, event_type, start_date, end_date, date_display, venue, city, state, location_type, footfall, gentry, timings, layout_type, fan_charge, price_shopping_canopy, price_shopping_table1, price_shopping_table2, price_food_canopy, price_food_table2, price_promotional, daily_stall_price, total_stalls, available_stalls, booked_stalls, image_url, description FROM events WHERE status IN ('published', 'active')";
    $params = [];

    if ($city !== 'all' && !empty($city)) {
        $sql .= " AND LOWER(city) LIKE :city";
        $params[':city'] = '%' . strtolower($city) . '%';
    }
    if ($type !== 'all' && !empty($type)) {
        $sql .= " AND LOWER(event_type) LIKE :type";
        $params[':type'] = '%' . strtolower($type) . '%';
    }

    $sql .= " ORDER BY start_date ASC LIMIT " . (int)$limit;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format for frontend convenience
    foreach ($events as &$ev) {
        $ev['date_formatted'] = formatDateRange($ev['start_date'], $ev['end_date']);
        $ev['detail_url'] = BASE_URL . '/event.php?id=' . $ev['id'] . '&slug=' . urlencode($ev['slug']);
    }

    echo json_encode([
        'status' => 'success',
        'count' => count($events),
        'events' => $events
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch events.'
    ]);
}
