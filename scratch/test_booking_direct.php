<?php
// Test direct invocation of event.php booking logic
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_HOST'] = '127.0.0.1:8080';
$_SERVER['SCRIPT_NAME'] = '/event.php';
$_SERVER['REQUEST_URI'] = '/event.php?id=1';
$_GET['id'] = 1;

session_start();
require_once __DIR__ . '/../includes/functions.php';
$token = csrf_token();

$_POST = [
    'action' => 'book_stall',
    'csrf_token' => $token,
    'customer_name' => 'Aarti Sharma',
    'business_name' => 'Aarti Boutique',
    'mobile' => '9811122334',
    'whatsapp' => '9811122334',
    'email' => 'aarti@example.com',
    'address' => 'Noida Sec 15',
    'category_name' => 'Jewellery & Accessories',
    'stall_type' => 'Shopping Canopy',
    'start_date' => '2026-10-13',
    'end_date' => '2026-10-13',
    'stalls_count' => 1,
    'addon_fan' => 1,
    'notes' => 'Near entrance'
];

ob_start();
require __DIR__ . '/../event.php';
$output = ob_get_clean();

echo "Form Errors in scope:\n";
var_dump($formErrors ?? []);
echo "Booking Success: " . ($bookingSuccess ? 'YES' : 'NO') . "\n";
if ($bookingSuccess && !empty($confirmedBooking)) {
    echo "Confirmed Ref: " . $confirmedBooking['booking_number'] . " | Amount: " . $confirmedBooking['total_amount'] . "\n";
}
