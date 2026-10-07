<?php
/**
 * Global Utility Functions
 * Expo Tree Exhibitions
 */

require_once __DIR__ . '/config.php';

/**
 * Escapes HTML output safely
 */
function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Clean string input
 */
function sanitize($value) {
    if (is_array($value)) {
        return array_map('sanitize', $value);
    }
    return trim(strip_tags((string)$value));
}

/**
 * Generate SEO-Friendly URL Slug
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'event-' . time() : $text;
}

/**
 * Format currency in Indian Rupees
 */
function formatPrice($amount) {
    if ($amount === null || $amount === '') return 'NA';
    if (!is_numeric($amount)) return e($amount);
    return '₹' . number_format((float)$amount, 0, '.', ',');
}

/**
 * Format date display range
 */
function formatDateRange($start, $end) {
    if (empty($start)) return '';
    $s = strtotime($start);
    $e = strtotime($end);
    
    if (!$e || $start === $end) {
        return date('d M Y', $s);
    }
    
    // If same month and year
    if (date('m Y', $s) === date('m Y', $e)) {
        return date('d', $s) . '–' . date('d M Y', $e);
    }
    
    // If same year
    if (date('Y', $s) === date('Y', $e)) {
        return date('d M', $s) . ' – ' . date('d M Y', $e);
    }
    
    return date('d M Y', $s) . ' – ' . date('d M Y', $e);
}

/**
 * Generate unique booking ID
 */
function generateBookingNumber() {
    return 'EXPO-BK-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -5));
}

/**
 * Generate unique shopper VIP pass ID
 */
function generatePassCode() {
    return 'EXPO-VIP-' . rand(1000, 9999);
}

/**
 * CSRF Protection
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Flash messages
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function getFlash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Authentication Helpers
 */
function isAdminLoggedIn() {
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        setFlash('danger', 'Please login to access the admin portal.');
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Get Event by Slug or ID
 */
function getEventBySlugOrId($slugOrId, $pdo) {
    if (is_numeric($slugOrId)) {
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([(int)$slugOrId]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM events WHERE slug = ?");
        $stmt->execute([$slugOrId]);
    }
    return $stmt->fetch();
}

/**
 * Deduct available stalls on booking
 */
function recordStallBooking($pdo, $eventId, $stallsCount) {
    $stalls = (int)$stallsCount;
    $id = (int)$eventId;
    $stmt = $pdo->prepare("
        UPDATE events 
        SET booked_stalls = booked_stalls + ?,
            available_stalls = GREATEST(0, available_stalls - ?)
        WHERE id = ?
    ");
    return $stmt->execute([$stalls, $stalls, $id]);
}

/**
 * Build WhatsApp Pre-filled Redirect URL
 */
function buildWhatsAppBookingUrl($booking) {
    $phone = ADMIN_WHATSAPP;
    $msg = "Namaste Expo Tree Exhibitions! 🎪\n";
    $msg .= "I have submitted a Stall Booking Request on your website:\n\n";
    $msg .= "• Booking Ref: *" . ($booking['booking_number'] ?? 'New Request') . "*\n";
    $msg .= "• Name: *" . ($booking['customer_name'] ?? '') . "*\n";
    if (!empty($booking['business_name'])) {
        $msg .= "• Brand/Company: *" . $booking['business_name'] . "*\n";
    }
    $msg .= "• Mobile: *" . ($booking['mobile'] ?? '') . "*\n";
    $msg .= "• Event: *" . ($booking['event_title'] ?? '') . "*\n";
    if (!empty($booking['event_venue'])) {
        $msg .= "• Venue: *" . $booking['event_venue'] . ", " . ($booking['event_city'] ?? '') . "*\n";
    }
    $msg .= "• Category: *" . ($booking['category_name'] ?? '') . "*\n";
    $msg .= "• Stall Type: *" . ($booking['stall_type'] ?? '') . "*\n";
    $msg .= "• Selected Dates: *" . ($booking['start_date'] ?? '') . " to " . ($booking['end_date'] ?? '') . " (" . ($booking['days_count'] ?? 1) . " Days)*\n";
    $msg .= "• Number of Stalls: *" . ($booking['stalls_count'] ?? 1) . "*\n";
    if (!empty($booking['price_per_day'])) {
        $msg .= "• Rate/Day: *₹" . number_format($booking['price_per_day'], 0) . "*\n";
    }
    if (!empty($booking['addons_total']) && $booking['addons_total'] > 0) {
        $msg .= "• Amenities/Addons: *₹" . number_format($booking['addons_total'], 0) . "*\n";
    }
    $msg .= "• Total Calculated Amount: *₹" . number_format($booking['total_amount'] ?? 0, 0) . "*\n";
    if (!empty($booking['notes'])) {
        $msg .= "• Notes: *" . $booking['notes'] . "*\n";
    }
    $msg .= "\nKindly share the stall blueprint layout, confirmation details & payment link. Thank you!";

    return 'https://wa.me/' . $phone . '?text=' . urlencode($msg);
}

/**
 * Get Site Setting from DB with fallback (polymorphic signature)
 */
function get_setting($keyOrPdo, $defaultOrKey = '', $fallback = '') {
    global $pdo;
    if ($keyOrPdo instanceof PDO) {
        $db = $keyOrPdo;
        $key = $defaultOrKey;
        $default = $fallback;
    } else {
        $db = $pdo;
        $key = $keyOrPdo;
        $default = $defaultOrKey;
    }
    if (!$db) return $default;
    try {
        $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return ($val !== false && $val !== null && $val !== '') ? $val : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Set Site Setting in DB (polymorphic signature)
 */
function set_setting($keyOrPdo, $valueOrKey, $val = null) {
    global $pdo;
    if ($keyOrPdo instanceof PDO) {
        $db = $keyOrPdo;
        $key = $valueOrKey;
        $value = $val;
    } else {
        $db = $pdo;
        $key = $keyOrPdo;
        $value = $valueOrKey;
    }
    if (!$db) return false;
    try {
        $stmt = $db->prepare("
            INSERT INTO site_settings (setting_key, setting_value) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        return $stmt->execute([$key, $value]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Check if event status is publicly visible
 */
function is_public_status($status) {
    return in_array(strtolower((string)$status), ['published', 'active', 'approved']);
}

/**
 * Restore available stalls if booking cancelled
 */
function restoreStallBooking($pdo, $eventId, $stallsCount) {
    $stalls = (int)$stallsCount;
    $id = (int)$eventId;
    $stmt = $pdo->prepare("
        UPDATE events 
        SET booked_stalls = GREATEST(0, booked_stalls - ?),
            available_stalls = LEAST(total_stalls, available_stalls + ?)
        WHERE id = ?
    ");
    return $stmt->execute([$stalls, $stalls, $id]);
}

/**
 * Synchronize / generate event stall records for an event
 */
function sync_event_stalls($pdo, $eventId) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([(int)$eventId]);
    $event = $stmt->fetch();
    if (!$event) return;

    $chk = $pdo->prepare("SELECT COUNT(*) FROM event_stalls WHERE event_id = ?");
    $chk->execute([(int)$eventId]);
    if ($chk->fetchColumn() > 0) return; // Already exists

    $stalls = [];
    if (!empty($event['price_shopping_canopy']) && $event['price_shopping_canopy'] > 0) {
        $stalls[] = [
            'stall_type' => 'Shopping Canopy',
            'stall_name' => 'Shopping Canopy Stall (3x3m)',
            'stall_size' => '3x3m Covered Canopy',
            'price' => (float)$event['price_shopping_canopy'],
            'total_quantity' => 8,
            'facilities' => '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup'
        ];
    }
    if (!empty($event['price_shopping_table1']) && $event['price_shopping_table1'] > 0) {
        $stalls[] = [
            'stall_type' => '1 Open Table',
            'stall_name' => '1 Open Table Space (6x2ft)',
            'stall_size' => '6x2ft Boutique Table',
            'price' => (float)$event['price_shopping_table1'],
            'total_quantity' => 6,
            'facilities' => '1 Draped Table, 1 Cushioned Chair, Light Point'
        ];
    }
    if (!empty($event['price_shopping_table2']) && $event['price_shopping_table2'] > 0) {
        $stalls[] = [
            'stall_type' => '2 Open Tables',
            'stall_name' => '2 Open Tables Space (12x2ft)',
            'stall_size' => '12x2ft Twin Table Setup',
            'price' => (float)$event['price_shopping_table2'],
            'total_quantity' => 4,
            'facilities' => '2 Draped Tables, 2 Chairs, Spotlights'
        ];
    }
    if (!empty($event['price_food_canopy']) && $event['price_food_canopy'] > 0) {
        $stalls[] = [
            'stall_type' => 'Food Canopy',
            'stall_name' => 'Gourmet Food Canopy (3x3m)',
            'stall_size' => '3x3m Food Zone Canopy',
            'price' => (float)$event['price_food_canopy'],
            'total_quantity' => 4,
            'facilities' => '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal'
        ];
    }
    if (!empty($event['price_food_table2']) && $event['price_food_table2'] > 0) {
        $stalls[] = [
            'stall_type' => 'Food 2 Open Tables',
            'stall_name' => 'Bakery / Dryfruits Table Setup',
            'stall_size' => '12x2ft Twin Table Space',
            'price' => (float)$event['price_food_table2'],
            'total_quantity' => 4,
            'facilities' => '2 Tables, 2 Chairs, Tasting Counter Space'
        ];
    }

    // Default fallback if no specific pricing
    if (empty($stalls)) {
        $base = (float)($event['daily_stall_price'] ?? 5000);
        $stalls[] = [
            'stall_type' => 'Prime Corner Canopy',
            'stall_name' => 'Prime Corner Canopy (3x3m)',
            'stall_size' => '3x3m 2-Side Open',
            'price' => $base,
            'total_quantity' => 10,
            'facilities' => '2 Tables, 2 Chairs, Fabric Draping, Spotlights & Power'
        ];
        $stalls[] = [
            'stall_type' => 'Standard Canopy',
            'stall_name' => 'Standard Canopy Stall',
            'stall_size' => '3x3m Covered',
            'price' => max(3000, $base * 0.8),
            'total_quantity' => 10,
            'facilities' => '1 Table, 2 Chairs, Lights & Power'
        ];
    }

    $ins = $pdo->prepare("
        INSERT INTO event_stalls (event_id, stall_type, stall_name, stall_size, price, total_quantity, available_quantity, booked_quantity, status, facilities)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0, 'available', ?)
    ");
    foreach ($stalls as $st) {
        $ins->execute([
            $eventId,
            $st['stall_type'],
            $st['stall_name'],
            $st['stall_size'],
            $st['price'],
            $st['total_quantity'],
            $st['total_quantity'],
            $st['facilities']
        ]);
    }
}

