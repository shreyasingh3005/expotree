<?php
/**
 * Event Details & Live Stall Booking Studio
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$eventId = sanitize($_GET['id'] ?? null);
$eventSlug = sanitize($_GET['slug'] ?? null);

$event = null;
if (!empty($eventId)) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([(int)$eventId]);
    $event = $stmt->fetch();
} elseif (!empty($eventSlug)) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE slug = ?");
    $stmt->execute([$eventSlug]);
    $event = $stmt->fetch();
}

if (!$event) {
    setFlash('danger', 'Exhibition not found or has concluded. Please explore our upcoming calendar.');
    header('Location: ' . BASE_URL . '/upcoming-exhibitions.php');
    exit;
}

// Access check: only published/active events are publicly visible (admins can preview anything)
$isAdmin = !empty($_SESSION['admin_logged_in']);
if (!in_array($event['status'], ['published', 'active']) && !$isAdmin) {
    setFlash('warning', 'This exhibition is currently under editorial review or has not been published yet.');
    header('Location: ' . BASE_URL . '/upcoming-exhibitions.php');
    exit;
}

$pageTitle = e($event['title']) . ' | Stall Booking & Details | Expo Tree Exhibitions';
$pageDesc = 'Book stalls for ' . e($event['title']) . ' at ' . e($event['venue']) . ', ' . e($event['city']) . '. Dates: ' . e($event['date_display'] ?? formatDateRange($event['start_date'], $event['end_date'])) . '. Expected footfall: ' . e($event['footfall'] ?? 'High footfall') . '.';
$currentPage = 'event';

// Handle Booking Form Submission
$bookingSuccess = false;
$confirmedBooking = null;
$waRedirectUrl = '';
$formErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book_stall') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $formErrors[] = 'Security verification failed. Please refresh and try again.';
    } else {
        $customerName = sanitize($_POST['customer_name'] ?? '');
        $businessName = sanitize($_POST['business_name'] ?? '');
        $mobile = sanitize($_POST['mobile'] ?? '');
        $whatsapp = sanitize($_POST['whatsapp'] ?? $mobile);
        $email = sanitize($_POST['email'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $categoryName = sanitize($_POST['category_name'] ?? 'Jewellery & Accessories');
        $stallType = sanitize($_POST['stall_type'] ?? 'Shopping Canopy');
        $startDate = sanitize($_POST['start_date'] ?? $event['start_date']);
        $endDate = sanitize($_POST['end_date'] ?? $event['end_date']);
        $stallsCount = max(1, (int)($_POST['stalls_count'] ?? 1));
        $addonFan = !empty($_POST['addon_fan']);
        $notes = sanitize($_POST['notes'] ?? '');

        // Validation
        if (empty($customerName)) $formErrors[] = 'Full name is required.';
        if (empty($mobile) || strlen($mobile) < 10) $formErrors[] = 'Valid 10-digit mobile number is required.';
        if (empty($startDate) || empty($endDate)) $formErrors[] = 'Please select valid booking dates.';

        // Check date range within event bounds
        if ($startDate < $event['start_date'] || $endDate > $event['end_date']) {
            $formErrors[] = 'Selected booking dates must be within event dates (' . $event['start_date'] . ' to ' . $event['end_date'] . ').';
        }
        if ($startDate > $endDate) {
            $formErrors[] = 'End date cannot be earlier than start date.';
        }

        // Availability check
        if ($event['available_stalls'] <= 0) {
            $formErrors[] = 'Sorry, all stalls for this exhibition are currently sold out.';
        } elseif ($stallsCount > $event['available_stalls']) {
            $formErrors[] = "Only {$event['available_stalls']} stall(s) currently available. You cannot book {$stallsCount}.";
        }

        if (empty($formErrors)) {
            // Calculate days count
            $startTs = strtotime($startDate);
            $endTs = strtotime($endDate);
            $daysCount = max(1, round(($endTs - $startTs) / 86400) + 1);

            // Determine stall daily price based on stall type
            $pricePerDay = (float)$event['daily_stall_price'];
            if (stripos($stallType, 'Shopping Canopy') !== false && !empty($event['price_shopping_canopy'])) {
                $pricePerDay = (float)$event['price_shopping_canopy'];
            } elseif (stripos($stallType, '1 Open Table') !== false && !empty($event['price_shopping_table1'])) {
                $pricePerDay = (float)$event['price_shopping_table1'];
            } elseif (stripos($stallType, '2 Open Table') !== false && !empty($event['price_shopping_table2'])) {
                $pricePerDay = (float)$event['price_shopping_table2'];
            } elseif (stripos($stallType, 'Food Canopy') !== false && !empty($event['price_food_canopy'])) {
                $pricePerDay = (float)$event['price_food_canopy'];
            } elseif (stripos($stallType, 'Food 2') !== false && !empty($event['price_food_table2'])) {
                $pricePerDay = (float)$event['price_food_table2'];
            }

            // Calculate Addons
            $addonsTotal = 0.00;
            $addonsDetails = [];
            if ($addonFan) {
                $fanRate = 300.00;
                $fanCost = $fanRate * $daysCount * $stallsCount;
                $addonsTotal += $fanCost;
                $addonsDetails[] = "Industrial Fan (₹300/day x {$daysCount} days x {$stallsCount} stalls) = ₹" . number_format($fanCost, 0);
            }

            // Total amount
            $stallsSubtotal = $pricePerDay * $stallsCount * $daysCount;
            $totalAmount = $stallsSubtotal + $addonsTotal;

            // Generate Booking Ref
            $bookingNumber = generateBookingNumber();

            // Insert into Database
            $insertSql = "INSERT INTO bookings (
                booking_number, event_id, customer_name, business_name, mobile, whatsapp,
                email, address, category_name, stall_type, start_date, end_date,
                days_count, stalls_count, price_per_day, addons_json, addons_total,
                total_amount, notes, status, whatsapp_sent
            ) VALUES (
                :b_num, :e_id, :c_name, :b_name, :mobile, :wa,
                :email, :addr, :cat, :stype, :sdate, :edate,
                :days, :stalls, :rate, :addons_json, :addons_tot,
                :tot_amt, :notes, 'confirmed', 1
            )";

            $stmtIns = $pdo->prepare($insertSql);
            $res = $stmtIns->execute([
                ':b_num' => $bookingNumber,
                ':e_id' => $event['id'],
                ':c_name' => $customerName,
                ':b_name' => $businessName,
                ':mobile' => $mobile,
                ':wa' => $whatsapp,
                ':email' => $email,
                ':addr' => $address,
                ':cat' => $categoryName,
                ':stype' => $stallType,
                ':sdate' => $startDate,
                ':edate' => $endDate,
                ':days' => $daysCount,
                ':stalls' => $stallsCount,
                ':rate' => $pricePerDay,
                ':addons_json' => json_encode($addonsDetails),
                ':addons_tot' => $addonsTotal,
                ':tot_amt' => $totalAmount,
                ':notes' => $notes
            ]);

            if ($res) {
                // Deduct stall count from available stalls
                recordStallBooking($pdo, $event['id'], $stallsCount);

                // Prepare Booking Data for WhatsApp URL
                $confirmedBooking = [
                    'booking_number' => $bookingNumber,
                    'customer_name' => $customerName,
                    'business_name' => $businessName,
                    'mobile' => $mobile,
                    'event_title' => $event['title'],
                    'event_venue' => $event['venue'],
                    'event_city' => $event['city'],
                    'category_name' => $categoryName,
                    'stall_type' => $stallType,
                    'start_date' => date('d M Y', strtotime($startDate)),
                    'end_date' => date('d M Y', strtotime($endDate)),
                    'days_count' => $daysCount,
                    'stalls_count' => $stallsCount,
                    'price_per_day' => $pricePerDay,
                    'addons_total' => $addonsTotal,
                    'total_amount' => $totalAmount,
                    'notes' => $notes
                ];

                $waRedirectUrl = buildWhatsAppBookingUrl($confirmedBooking);
                $bookingSuccess = true;

                // Refresh event data to show updated stall availability
                $stmtRef = $pdo->prepare("SELECT * FROM events WHERE id = ?");
                $stmtRef->execute([$event['id']]);
                $event = $stmtRef->fetch();
            } else {
                $formErrors[] = 'Failed to record booking. Please try again or contact support at ' . ADMIN_PHONE . '.';
            }
        }
    }
}

// Load Granular Stalls from event_stalls if available
$stallsQuery = $pdo->prepare("SELECT * FROM event_stalls WHERE event_id = ? ORDER BY price ASC");
$stallsQuery->execute([$event['id']]);
$granularStalls = $stallsQuery->fetchAll();

$stallOptions = [];
if (!empty($granularStalls)) {
    foreach ($granularStalls as $gst) {
        $availQty = (int)($gst['available_quantity'] ?? 0);
        $stallOptions[] = [
            'name' => $gst['stall_type'] . (!empty($gst['stall_size']) ? ' (' . $gst['stall_size'] . ')' : ''),
            'price' => (float)$gst['price'],
            'desc' => !empty($gst['facilities']) ? $gst['facilities'] : 'Setup included. Inventory: ' . $availQty . ' stalls available'
        ];
    }
} else {
    // Fallback to columns on events table
    if (!empty($event['price_shopping_canopy'])) {
        $stallOptions[] = [
            'name' => 'Shopping Canopy (2 Tables + 2 Chairs + Light)',
            'price' => (float)$event['price_shopping_canopy'],
            'desc' => 'Premium covered canopy with complete electrical point & setup'
        ];
    }
    if (!empty($event['price_shopping_table1'])) {
        $stallOptions[] = [
            'name' => '1 Open Table (1 Table + 1 Chair)',
            'price' => (float)$event['price_shopping_table1'],
            'desc' => 'Best for boutique jewellery, fragrances & small craft counters'
        ];
    }
    if (!empty($event['price_shopping_table2'])) {
        $stallOptions[] = [
            'name' => '2 Open Tables (2 Tables + 2 Chairs)',
            'price' => (float)$event['price_shopping_table2'],
            'desc' => 'Spacious open stall setup for apparel, footwear & home decor'
        ];
    }
    if (!empty($event['price_food_canopy'])) {
        $stallOptions[] = [
            'name' => 'Food Canopy (2 Tables + 2 Chairs + Power)',
            'price' => (float)$event['price_food_canopy'],
            'desc' => 'Designated gourmet & festive food stall zone with waste management'
        ];
    }
    if (!empty($event['price_food_table2'])) {
        $stallOptions[] = [
            'name' => 'Food 2 Open Tables',
            'price' => (float)$event['price_food_table2'],
            'desc' => 'Open counter for bakery, dryfruits, chocolates & packaged snacks'
        ];
    }
    if (empty($stallOptions)) {
        $stallOptions[] = [
            'name' => 'Standard Exhibition Stall',
            'price' => (float)$event['daily_stall_price'],
            'desc' => 'Complete exhibition stall setup including tables, chairs and canopy'
        ];
    }
}

$defaultOption = $stallOptions[0];
$img = !empty($event['image_url']) ? $event['image_url'] : 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=1200&q=80';

// Prepare Schema.org Event JSON-LD structured data
$schemaEventData = [
    '@context' => 'https://schema.org',
    '@type' => 'Event',
    'name' => $event['title'],
    'startDate' => $event['start_date'] . 'T10:00:00+05:30',
    'endDate' => $event['end_date'] . 'T20:00:00+05:30',
    'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
    'eventStatus' => 'https://schema.org/EventScheduled',
    'location' => [
        '@type' => 'Place',
        'name' => $event['venue'],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => !empty($event['address']) ? $event['address'] : $event['venue'],
            'addressLocality' => $event['city'],
            'addressRegion' => $event['state'],
            'postalCode' => $event['pincode'] ?? '122001',
            'addressCountry' => 'IN'
        ]
    ],
    'image' => [$img],
    'description' => !empty($event['description']) ? $event['description'] : ($event['title'] . ' at ' . $event['venue']),
    'offers' => [
        '@type' => 'Offer',
        'url' => BASE_URL . '/event.php?id=' . $event['id'],
        'price' => $defaultOption['price'],
        'priceCurrency' => 'INR',
        'availability' => ($event['available_stalls'] > 0) ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut',
        'validFrom' => date('Y-m-d')
    ],
    'organizer' => [
        '@type' => 'Organization',
        'name' => $event['organizer_name'] ?? 'Expo Tree Exhibitions',
        'url' => BASE_URL
    ]
];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Schema.org JSON-LD Structured Data -->
<script type="application/ld+json">
<?= json_encode($schemaEventData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>

<style>
/* Scoped Mobile Responsiveness for event.php */
.event-hero-section { padding: 3rem 1.25rem 2.25rem; text-align: center; position: relative; overflow: hidden; }
.event-breadcrumb-wrap { display: flex; gap: 0.5rem; align-items: center; justify-content: center; margin-bottom: 0.85rem; flex-wrap: wrap; }
.event-hero-title { font-family: var(--font-cinzel); font-size: clamp(1.4rem, 4vw, 2.5rem); color: #ffffff; margin-bottom: 0.75rem; text-align: center; line-height: 1.25; word-wrap: break-word; overflow-wrap: break-word; }
.event-hero-meta { display: flex; justify-content: center; gap: 1rem 1.5rem; flex-wrap: wrap; margin-top: 1rem; font-size: 0.95rem; color: #ffd6df; }
.event-hero-meta > div { display: flex; align-items: center; gap: 0.4rem; word-break: break-word; }
.event-main-section { padding-top: 2.5rem; padding-bottom: 4.5rem; }
.event-details-layout { display: grid; grid-template-columns: 1.2fr 1fr; gap: 2.5rem; align-items: start; }
.event-hero-media { position: relative; border-radius: 20px; overflow: hidden; border: 1.5px solid var(--border-gold); box-shadow: var(--shadow-card); margin-bottom: 1.75rem; background: #1a0408; }
.event-hero-img { width: 100%; height: 380px; object-fit: cover; display: block; }
.event-media-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(20,2,5,0.85) 100%); }
.event-media-info { position: absolute; bottom: 1.25rem; left: 1.25rem; right: 1.25rem; display: flex; justify-content: space-between; align-items: flex-end; color: #ffffff; gap: 1rem; }
.event-category-badge { display: inline-block; background: var(--gold-500); color: #1a0408; font-size: 0.78rem; font-weight: 800; padding: 0.25rem 0.75rem; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.04em; }
.event-media-venue-title { font-family: var(--font-cinzel); font-size: 1.5rem; color: #ffffff; margin-top: 0.4rem; line-height: 1.25; text-shadow: 0 2px 8px rgba(0,0,0,0.85); word-wrap: break-word; overflow-wrap: break-word; }
.event-card { background: #ffffff; border: 1.5px solid var(--border-gold); border-radius: 20px; padding: 2rem; box-shadow: var(--shadow-card); margin-bottom: 1.75rem; word-wrap: break-word; overflow-wrap: break-word; }
.event-card-title { font-family: var(--font-cinzel); font-size: 1.35rem; color: var(--burgundy-950); margin-bottom: 1.25rem; border-bottom: 1px solid #ebdada; padding-bottom: 0.75rem; }
.event-specs-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.15rem; margin-bottom: 1.5rem; }
.event-spec-box { background: #faf7f8; padding: 0.9rem 1rem; border-radius: 12px; border: 1px solid #ebdada; }
.event-spec-label { font-size: 0.75rem; color: #777; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.25rem; }
.event-spec-value { font-size: 1.02rem; font-weight: 700; color: var(--burgundy-900); word-break: break-word; }
.event-address-box { background: #fdfaf6; border-left: 4px solid var(--gold-500); padding: 1rem 1.25rem; border-radius: 0 10px 10px 0; margin-bottom: 1.25rem; word-break: break-word; }
.event-info-subbox { background: #faf7f8; border: 1px solid #ebdada; border-radius: 12px; padding: 1.25rem; margin-top: 1.15rem; word-break: break-word; }
.event-info-subbox-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
.event-table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 10px; }
.event-pricing-table { width: 100%; min-width: 480px; border-collapse: collapse; font-size: 0.88rem; text-align: left; }
.event-pricing-table th { padding: 0.85rem 1rem; background: #faf4f5; color: var(--burgundy-950); border-bottom: 2px solid var(--border-gold); font-weight: 700; font-size: 0.82rem; text-transform: uppercase; }
.event-pricing-table td { padding: 0.85rem 1rem; border-bottom: 1px solid #ebdada; vertical-align: middle; }
.event-booking-studio { background: #ffffff; border: 2px solid var(--gold-500); border-radius: 20px; padding: 2rem 1.75rem; box-shadow: 0 15px 35px rgba(43, 7, 13, 0.12); position: sticky; top: 90px; }
.event-booking-header { text-align: center; margin-bottom: 1.5rem; border-bottom: 1px solid #ebdada; padding-bottom: 1.25rem; }
.form-row-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.15rem; }
.form-input-control { width: 100%; box-sizing: border-box; padding: 0.68rem 0.85rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.9rem; background: #ffffff; color: var(--text-dark); font-family: inherit; transition: border-color 0.2s ease; }
.form-input-control:focus { border-color: var(--burgundy-800); outline: none; box-shadow: 0 0 0 3px rgba(107, 15, 26, 0.1); }
.event-cost-estimate-box { background: linear-gradient(135deg, #fdf6f7 0%, #faecee 100%); border: 1.5px solid var(--border-gold); border-radius: 14px; padding: 1.15rem; margin-bottom: 1.5rem; }
.voucher-card { background: linear-gradient(135deg, #064E3B 0%, #022c22 100%); border: 2px solid #10B981; border-radius: 20px; padding: 2.5rem 1.75rem; color: #ffffff; margin-bottom: 3rem; box-shadow: 0 15px 40px rgba(6, 78, 59, 0.4); text-align: center; }
.voucher-details-box { background: rgba(0,0,0,0.3); border: 1px solid rgba(167, 243, 208, 0.3); border-radius: 14px; max-width: 600px; margin: 0 auto 2rem; padding: 1.5rem; text-align: left; font-size: 0.92rem; }
.voucher-grid-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; }

@media (max-width: 991px) {
  .event-details-layout { grid-template-columns: 1fr !important; gap: 2rem !important; }
  .event-booking-studio { position: static !important; top: auto !important; }
  .event-hero-img { height: 320px !important; }
}

@media (max-width: 767px) {
  .event-hero-section { padding: 2.25rem 1rem 1.75rem !important; }
  .event-hero-title { font-size: 1.5rem !important; line-height: 1.3 !important; }
  .event-hero-meta { flex-direction: column !important; align-items: center !important; gap: 0.4rem !important; text-align: center !important; font-size: 0.88rem !important; }
  .event-main-section { padding-top: 1.5rem !important; padding-bottom: 3.5rem !important; }
  .event-card, .event-booking-studio { padding: 1.35rem 1rem !important; border-radius: 16px !important; }
  .event-hero-img { height: 240px !important; }
  .event-media-info { flex-direction: column !important; align-items: flex-start !important; gap: 0.5rem !important; bottom: 1rem !important; left: 1rem !important; right: 1rem !important; }
  .event-media-venue-title { font-size: 1.25rem !important; }
  .event-specs-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 0.75rem !important; }
  .event-spec-box { padding: 0.75rem 0.85rem !important; }
  .event-spec-value { font-size: 0.9rem !important; }
  .event-info-subbox-grid { grid-template-columns: 1fr !important; gap: 0.75rem !important; }
  .form-row-2col { grid-template-columns: 1fr !important; gap: 0.85rem !important; margin-bottom: 0.85rem !important; }
  .voucher-grid-2col { grid-template-columns: 1fr !important; }
  .voucher-card { padding: 1.5rem 1rem !important; }
  .voucher-details-box { padding: 1rem !important; }
}

@media (max-width: 480px) {
  .event-hero-section { padding: 2rem 0.75rem 1.5rem !important; }
  .event-specs-grid { grid-template-columns: 1fr !important; gap: 0.65rem !important; }
  .event-hero-img { height: 210px !important; }
  .event-pricing-table { min-width: 420px !important; }
  .btn-wa-voucher { font-size: 0.95rem !important; padding: 0.85rem 1.25rem !important; width: 100% !important; text-align: center !important; justify-content: center !important; }
}
</style>

<!-- Event Header Banner Section -->
<section class="section page-hero-section event-hero-section">
  <div class="hero-bg-photo" aria-hidden="true" style="opacity: 0.18;"></div>
  <div class="container" style="position: relative; z-index: 2;">
    <div class="event-breadcrumb-wrap">
      <a href="<?= BASE_URL ?>/upcoming-exhibitions.php" style="color: var(--gold-300); text-decoration: none; font-size: 0.85rem;">← Back to Exhibitions Calendar</a>
      <span style="color: rgba(255,255,255,0.4);">/</span>
      <span style="background: var(--gold-500); color: #1a0408; font-size: 0.75rem; font-weight: 800; padding: 0.2rem 0.6rem; border-radius: 12px;">📍 <?= e($event['city']) ?></span>
      <span style="background: rgba(255,255,255,0.1); color: #ffffff; font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);"><?= e($event['event_type']) ?></span>
    </div>

    <h1 class="event-hero-title">
      <?= e($event['title']) ?>
    </h1>
    <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>

    <div class="event-hero-meta">
      <div>📍 <strong><?= e($event['venue']) ?></strong>, <?= e($event['city']) ?></div>
      <div>📅 <strong><?= e($event['date_display'] ?? formatDateRange($event['start_date'], $event['end_date'])) ?></strong></div>
      <div>⏰ <strong><?= e($event['timings'] ?? '10:00 AM - 8:00 PM') ?></strong></div>
    </div>
  </div>
</section>

<!-- Main Details & Booking Section -->
<section class="section event-main-section">
  <div class="container">

    <!-- Admin Preview Banner -->
    <?php if ($isAdmin && !in_array($event['status'], ['published', 'active'])): ?>
      <div style="background: #fef3c7; border: 2px solid #f59e0b; color: #92400e; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
        <div>
          <strong>⚠️ Admin Preview Mode:</strong> This exhibition has status <code style="background:#fde68a; padding: 0.15rem 0.4rem; border-radius: 4px;"><?= e($event['status']) ?></code> and is only visible to logged-in administrators.
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <a href="<?= BASE_URL ?>/admin/event-actions.php?action=publish&id=<?= $event['id'] ?>&csrf_token=<?= csrf_token() ?>" class="btn btn-gold" style="padding: 0.45rem 1rem; font-size: 0.85rem;">
            <span>Publish Live</span>
          </a>
          <a href="<?= BASE_URL ?>/admin/event-edit.php?id=<?= $event['id'] ?>" class="btn btn-outline-white" style="padding: 0.45rem 1rem; font-size: 0.85rem; color: #92400e; border-color: #b45309;">
            <span>Edit Exhibition</span>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <!-- Booking Success Voucher Modal / Card -->
    <?php if ($bookingSuccess && $confirmedBooking): ?>
      <div class="voucher-card">
        <div style="font-size: 3rem; margin-bottom: 0.5rem;">🎉</div>
        <span class="section-badge badge-gold" style="background: #10B981; color: #ffffff; border-color: #34D399; margin-bottom: 0.75rem;">Booking Request Registered</span>
        <h2 style="font-family: var(--font-cinzel); font-size: clamp(1.4rem, 3.5vw, 2rem); color: #A7F3D0; margin-bottom: 0.5rem;">Stall Request Confirmed!</h2>
        <p style="color: #D1FAE5; max-width: 650px; margin: 0 auto 1.5rem; font-size: 1rem; line-height: 1.5;">
          Thank you, <strong><?= e($confirmedBooking['customer_name']) ?></strong>! Your request for <strong><?= e($confirmedBooking['stall_type']) ?></strong> (Ref: <code><?= e($confirmedBooking['booking_number']) ?></code>) has been recorded in our system.
        </p>

        <!-- Voucher Details Box -->
        <div class="voucher-details-box">
          <div class="voucher-grid-2col">
            <div><strong>Exhibition:</strong><br><span style="color: #A7F3D0;"><?= e($confirmedBooking['event_title']) ?></span></div>
            <div><strong>Venue:</strong><br><span style="color: #A7F3D0;"><?= e($confirmedBooking['event_venue']) ?>, <?= e($confirmedBooking['event_city']) ?></span></div>
            <div><strong>Dates:</strong><br><?= e($confirmedBooking['start_date']) ?> to <?= e($confirmedBooking['end_date']) ?> (<?= $confirmedBooking['days_count'] ?> Days)</div>
            <div><strong>Stall Count:</strong><br><?= e($confirmedBooking['stalls_count']) ?> Stall(s)</div>
            <div><strong>Rate / Day:</strong><br>₹<?= number_format($confirmedBooking['price_per_day'], 0) ?></div>
            <div><strong>Total Estimated:</strong><br><span style="color: #FCD34D; font-size: 1.15rem; font-weight: 800;">₹<?= number_format($confirmedBooking['total_amount'], 0) ?></span></div>
          </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 0.85rem; flex-wrap: wrap;">
          <a href="<?= $waRedirectUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-wa-voucher" style="background: #25D366; color: #ffffff; font-size: 1rem; padding: 0.85rem 1.75rem; font-weight: 700; box-shadow: 0 8px 24px rgba(37, 211, 102, 0.4);">
            <span>💬 Confirm on WhatsApp (9811175057)</span>
          </a>
          <a href="<?= BASE_URL ?>/upcoming-exhibitions.php" class="btn btn-outline-white" style="font-size: 0.95rem; padding: 0.85rem 1.5rem;">
            <span>Explore More Exhibitions</span>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <!-- Form Errors Alert -->
    <?php if (!empty($formErrors)): ?>
      <div class="site-flash-alert alert-danger" style="margin-bottom: 2rem; border-radius: 12px;">
        <div class="container site-flash-inner" style="flex-direction: column; align-items: flex-start;">
          <strong>Please resolve the following before proceeding:</strong>
          <ul style="margin: 0.5rem 0 0 1.25rem;">
            <?php foreach ($formErrors as $err): ?>
              <li><?= e($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>

    <!-- Main Two-Column Layout (Fully Responsive) -->
    <div class="event-details-layout">

      <!-- Left Column: Event Overview & Excel Specifications -->
      <div>
        <!-- Hero Event Image with Badges -->
        <div class="event-hero-media">
          <img src="<?= e($img) ?>" alt="<?= e($event['title']) ?>" class="event-hero-img" loading="eager" />
          <div class="event-media-overlay"></div>

          <div class="event-media-info">
            <div>
              <span class="event-category-badge">
                <?= e($event['category']) ?>
              </span>
              <h2 class="event-media-venue-title">
                <?= e($event['venue']) ?>
              </h2>
            </div>

            <!-- Availability Status Pill -->
            <div>
              <?php if ($event['available_stalls'] <= 0): ?>
                <span style="background: #DC2626; color: #ffffff; padding: 0.4rem 0.85rem; border-radius: 20px; font-weight: 700; font-size: 0.82rem; white-space: nowrap; display: inline-block;">Sold Out</span>
              <?php elseif ($event['available_stalls'] <= 5): ?>
                <span style="background: #D97706; color: #ffffff; padding: 0.4rem 0.85rem; border-radius: 20px; font-weight: 700; font-size: 0.82rem; white-space: nowrap; display: inline-block;">🔥 <?= $event['available_stalls'] ?> Stalls Left</span>
              <?php else: ?>
                <span style="background: #059669; color: #ffffff; padding: 0.4rem 0.85rem; border-radius: 20px; font-weight: 700; font-size: 0.82rem; white-space: nowrap; display: inline-block;">✅ <?= $event['available_stalls'] ?> Available</span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Venue & Facility Highlights (Excel Grounding) -->
        <div class="event-card">
          <h3 class="event-card-title">
            Exhibition Overview &amp; Specifications
          </h3>

          <div class="event-specs-grid">
            <div class="event-spec-box">
              <div class="event-spec-label">Expected Footfall</div>
              <div class="event-spec-value">👥 <?= e($event['footfall'] ?? 'High Density Crowd') ?></div>
            </div>

            <div class="event-spec-box">
              <div class="event-spec-label">Gentry Profile</div>
              <div class="event-spec-value">👑 <?= e($event['gentry'] ?? 'Premium Class') ?></div>
            </div>

            <div class="event-spec-box">
              <div class="event-spec-label">Environment Setup</div>
              <div class="event-spec-value">❄️ <?= e($event['location_type'] ?? 'Indoors / Atrium') ?></div>
            </div>

            <div class="event-spec-box">
              <div class="event-spec-label">Stall Allocation</div>
              <div class="event-spec-value">📐 <?= e($event['layout_type'] ?? 'First Come First Serve') ?></div>
            </div>

            <div class="event-spec-box">
              <div class="event-spec-label">Event Timings</div>
              <div class="event-spec-value">⏰ <?= e($event['timings'] ?? '10:00 AM - 8:00 PM') ?></div>
            </div>

            <div class="event-spec-box">
              <div class="event-spec-label">Industrial Fans</div>
              <div class="event-spec-value">💨 <?= e($event['fan_charge'] ?? '₹300 / Day') ?></div>
            </div>
          </div>

          <?php if (!empty($event['description'])): ?>
            <div style="font-size: 0.95rem; color: #4a4a4a; line-height: 1.7; margin-bottom: 1.5rem;">
              <?= nl2br(e($event['description'])) ?>
            </div>
          <?php endif; ?>

          <!-- Address & Map Link -->
          <div class="event-address-box">
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--burgundy-900); margin-bottom: 0.25rem;">📍 Complete Address:</div>
            <div style="font-size: 0.92rem; color: #555; line-height: 1.5;">
              <?= e($event['venue']) ?>, <?= !empty($event['address']) ? e($event['address']) . ', ' : '' ?><?= e($event['city']) ?> (<?= e($event['state']) ?>) <?= !empty($event['pincode']) ? ' - ' . e($event['pincode']) : '' ?>
            </div>
          </div>

          <?php if (!empty($event['stall_sizes']) || !empty($event['facilities'])): ?>
            <div class="event-info-subbox">
              <div class="event-info-subbox-grid">
                <?php if (!empty($event['stall_sizes'])): ?>
                  <div>
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--burgundy-900); margin-bottom: 0.25rem;">🎪 Stall Dimensions &amp; Sizes:</div>
                    <div style="font-size: 0.88rem; color: #444; line-height: 1.5;"><?= e($event['stall_sizes']) ?></div>
                  </div>
                <?php endif; ?>
                <?php if (!empty($event['facilities'])): ?>
                  <div>
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--burgundy-900); margin-bottom: 0.25rem;">⚡ Amenities &amp; Facilities:</div>
                    <div style="font-size: 0.88rem; color: #444; line-height: 1.5;"><?= e($event['facilities']) ?></div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($event['booking_instructions']) || !empty($event['terms_conditions'])): ?>
            <div class="event-info-subbox" style="margin-top: 1rem; background: #fdfaf6;">
              <?php if (!empty($event['booking_instructions'])): ?>
                <div style="margin-bottom: 0.75rem;">
                  <div style="font-size: 0.8rem; font-weight: 700; color: var(--burgundy-900); margin-bottom: 0.2rem;">📋 Setup Guidelines:</div>
                  <div style="font-size: 0.86rem; color: #555; line-height: 1.5;"><?= e($event['booking_instructions']) ?></div>
                </div>
              <?php endif; ?>
              <?php if (!empty($event['terms_conditions'])): ?>
                <div>
                  <div style="font-size: 0.8rem; font-weight: 700; color: var(--burgundy-900); margin-bottom: 0.2rem;">⚖️ Terms &amp; Stall Allocation Rules:</div>
                  <div style="font-size: 0.86rem; color: #555; line-height: 1.5;"><?= e($event['terms_conditions']) ?></div>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Stall Pricing Matrix Table (Excel Grounded) -->
        <div class="event-card">
          <h3 class="event-card-title">
            Stall Options &amp; Inventory Rates
          </h3>
          <div class="event-table-responsive">
            <table class="event-pricing-table">
              <thead>
                <tr>
                  <th>Stall Configuration</th>
                  <th>Equipment Included</th>
                  <th style="text-align: right;">Rate / Day</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($stallOptions as $opt): ?>
                  <tr>
                    <td style="font-weight: 600; color: var(--burgundy-900);"><?= e($opt['name']) ?></td>
                    <td style="color: #666; font-size: 0.82rem;"><?= e($opt['desc']) ?></td>
                    <td style="font-weight: 800; color: var(--burgundy-950); text-align: right; font-family: var(--font-cinzel);">
                      <?= formatPrice($opt['price']) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if (!empty($event['price_promotional'])): ?>
                  <tr>
                    <td style="font-weight: 600; color: var(--burgundy-900);">Promotional Brand Stall</td>
                    <td style="color: #666; font-size: 0.82rem;">Corporate sampling, kiosks &amp; brand activation</td>
                    <td style="font-weight: 800; color: var(--burgundy-950); text-align: right; font-family: var(--font-cinzel);">
                      <?= e($event['price_promotional']) ?>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Right Column: Interactive Live Stall Booking Studio & Calculator -->
      <div>
        <div class="event-booking-studio" id="booking-studio">
          
          <div class="event-booking-header">
            <span class="section-badge badge-gold" style="font-size: 0.72rem;">Live Booking Studio</span>
            <h3 style="font-family: var(--font-cinzel); font-size: 1.45rem; color: var(--burgundy-950); margin-top: 0.35rem;">
              Reserve Your Stall Now
            </h3>
            <p style="font-size: 0.85rem; color: #666; margin-top: 0.25rem;">
              Instant automated calculation • WhatsApp layout verification
            </p>
          </div>

          <?php if ($event['available_stalls'] <= 0): ?>
            <!-- Sold Out Message -->
            <div style="background: #FEF2F2; border: 1.5px solid #DC2626; border-radius: 12px; padding: 1.5rem; text-align: center; color: #991B1B;">
              <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">❌</div>
              <h4 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.4rem;">All Stalls are Sold Out!</h4>
              <p style="font-size: 0.88rem; margin-bottom: 1.25rem;">
                All <?= $event['total_stalls'] ?> stalls for this exhibition have been booked. You can join the waiting list or check our other upcoming exhibitions.
              </p>
              <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>?text=<?= urlencode('Hello Expo Tree! I want to join the waiting list for ' . $event['title'] . ' at ' . $event['venue']) ?>" target="_blank" rel="noopener noreferrer" class="btn" style="background: #25D366; color: #ffffff; width: 100%; text-align: center; justify-content: center;">
                <span>💬 Join WhatsApp Waiting List</span>
              </a>
            </div>
          <?php else: ?>

            <!-- Interactive Booking Form -->
            <form method="POST" action="event.php?id=<?= $event['id'] ?>#booking-studio" id="stall-booking-form">
              <input type="hidden" name="action" value="book_stall" />
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />

              <!-- Stall Type Selection -->
              <div style="margin-bottom: 1.15rem;">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                  1. Select Stall Configuration: <span style="color: #dc2626;">*</span>
                </label>
                <select name="stall_type" id="booking_stall_type" class="form-input-control" required>
                  <?php foreach ($stallOptions as $idx => $opt): ?>
                    <option value="<?= e($opt['name']) ?>" data-price="<?= $opt['price'] ?>" <?= $idx === 0 ? 'selected' : '' ?>>
                      <?= e($opt['name']) ?> — <?= formatPrice($opt['price']) ?> / day
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Date Selection (Responsive Grid) -->
              <div class="form-row-2col">
                <div>
                  <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.35rem;">
                    Start Date: <span style="color: #dc2626;">*</span>
                  </label>
                  <input type="date" name="start_date" id="booking_start_date" 
                         value="<?= e($event['start_date']) ?>" 
                         min="<?= e($event['start_date']) ?>" 
                         max="<?= e($event['end_date']) ?>" 
                         class="form-input-control" required />
                </div>
                <div>
                  <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.35rem;">
                    End Date: <span style="color: #dc2626;">*</span>
                  </label>
                  <input type="date" name="end_date" id="booking_end_date" 
                         value="<?= e($event['end_date']) ?>" 
                         min="<?= e($event['start_date']) ?>" 
                         max="<?= e($event['end_date']) ?>" 
                         class="form-input-control" required />
                </div>
              </div>

              <!-- Stalls Count & Addons (Responsive Grid) -->
              <div class="form-row-2col" style="align-items: end;">
                <div>
                  <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.35rem;">
                    Number of Stalls:
                  </label>
                  <select name="stalls_count" id="booking_stalls_count" class="form-input-control">
                    <?php for ($i = 1; $i <= min(5, $event['available_stalls']); $i++): ?>
                      <option value="<?= $i ?>"><?= $i ?> Stall<?= $i > 1 ? 's' : '' ?></option>
                    <?php endfor; ?>
                  </select>
                </div>

                <div style="padding-bottom: 0.35rem;">
                  <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.84rem; color: var(--burgundy-950); cursor: pointer;">
                    <input type="checkbox" name="addon_fan" id="booking_addon_fan" value="1" style="width: 18px; height: 18px; accent-color: var(--burgundy-800); flex-shrink: 0;" />
                    <span>Industrial Fan (+₹300/day)</span>
                  </label>
                </div>
              </div>

              <!-- Live Automatic Calculation Summary Box -->
              <div class="event-cost-estimate-box">
                <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--burgundy-800); margin-bottom: 0.6rem; letter-spacing: 0.05em;">
                  Live Cost Estimate
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.86rem; color: #555; margin-bottom: 0.35rem;">
                  <span>Selected Duration:</span>
                  <span id="calc_days_display"><strong>1 Day</strong></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.86rem; color: #555; margin-bottom: 0.35rem;">
                  <span>Base Rate:</span>
                  <span id="calc_rate_display">₹0 / day</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.86rem; color: #555; margin-bottom: 0.35rem;">
                  <span>Amenities / Addons:</span>
                  <span id="calc_addons_display">₹0</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 800; color: var(--burgundy-950); border-top: 1px dashed rgba(212,175,55,0.4); padding-top: 0.6rem; margin-top: 0.4rem; font-family: var(--font-cinzel);">
                  <span>Estimated Total:</span>
                  <span id="calc_total_display" style="color: var(--burgundy-800);">₹0</span>
                </div>
              </div>

              <!-- Exhibitor Personal / Business Information (Responsive Grids) -->
              <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.3rem;">
                  Your Full Name: <span style="color: #dc2626;">*</span>
                </label>
                <input type="text" name="customer_name" placeholder="e.g. Pooja Sharma" class="form-input-control" required />
              </div>

              <div class="form-row-2col">
                <div>
                  <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.3rem;">
                    Brand / Business:
                  </label>
                  <input type="text" name="business_name" placeholder="e.g. Zoya Jewels" class="form-input-control" />
                </div>
                <div>
                  <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.3rem;">
                    Phone (10 Digits): <span style="color: #dc2626;">*</span>
                  </label>
                  <input type="tel" name="mobile" placeholder="98111XXXXX" pattern="[0-9]{10}" class="form-input-control" required />
                </div>
              </div>

              <div class="form-row-2col">
                <div>
                  <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.3rem;">
                    Product Category:
                  </label>
                  <select name="category_name" class="form-input-control">
                    <option value="Jewellery & Accessories">Jewellery &amp; Accessories</option>
                    <option value="Apparel & Footwear">Apparel &amp; Footwear</option>
                    <option value="Handbags">Handbags</option>
                    <option value="Gifting & Home Décor">Gifting &amp; Home Décor</option>
                    <option value="Skincare & Perfumes">Skincare &amp; Perfumes</option>
                    <option value="Bakery & Dryfruits">Bakery &amp; Dryfruits</option>
                    <option value="Food Stalls">Food Stalls / Live Snacks</option>
                    <option value="Crochet & Handmade">Crochet &amp; Handmade</option>
                    <option value="Candles & Soaps">Candles &amp; Soaps</option>
                    <option value="Kidswear & Toys">Kidswear &amp; Toys</option>
                    <option value="Promotional / Services">Promotional / Corporate</option>
                    <option value="Other Category">Other Category</option>
                  </select>
                </div>
                <div>
                  <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.3rem;">
                    Email Address:
                  </label>
                  <input type="email" name="email" placeholder="pooja@example.com" class="form-input-control" />
                </div>
              </div>

              <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.3rem;">
                  Special Notes / Corner Preference:
                </label>
                <textarea name="notes" rows="2" placeholder="e.g. Need corner stall near entrance, bringing 1 extra display rack..." class="form-input-control"></textarea>
              </div>

              <!-- Submit Buttons -->
              <button type="submit" class="btn btn-gold" style="width: 100%; justify-content: center; font-size: 1rem; padding: 0.85rem; margin-bottom: 0.75rem; box-shadow: 0 4px 15px rgba(212,175,55,0.4);">
                <span>🎪 Submit Stall Reservation</span>
              </button>

              <div style="text-align: center; font-size: 0.78rem; color: #888;">
                🔒 No immediate online payment required. Our team will verify your category &amp; send stall layout via WhatsApp.
              </div>
            </form>
          <?php endif; ?>

        </div>
      </div>

    </div>

  </div>
</section>

<!-- Interactive Live Calculator Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const stallTypeSelect = document.getElementById("booking_stall_type");
  const startDateInput = document.getElementById("booking_start_date");
  const endDateInput = document.getElementById("booking_end_date");
  const stallsCountSelect = document.getElementById("booking_stalls_count");
  const addonFanCheckbox = document.getElementById("booking_addon_fan");

  const calcDaysDisplay = document.getElementById("calc_days_display");
  const calcRateDisplay = document.getElementById("calc_rate_display");
  const calcAddonsDisplay = document.getElementById("calc_addons_display");
  const calcTotalDisplay = document.getElementById("calc_total_display");

  function recalculate() {
    if (!stallTypeSelect || !startDateInput || !endDateInput) return;

    // Get selected option price
    const selectedOpt = stallTypeSelect.options[stallTypeSelect.selectedIndex];
    const ratePerDay = parseFloat(selectedOpt?.getAttribute("data-price") || 0);

    // Calculate days
    const sDate = new Date(startDateInput.value);
    const eDate = new Date(endDateInput.value);
    let diffDays = 1;
    if (sDate && eDate && !isNaN(sDate) && !isNaN(eDate) && eDate >= sDate) {
      const diffTime = Math.abs(eDate - sDate);
      diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    } else {
      diffDays = 1;
    }

    const stallCount = parseInt(stallsCountSelect?.value || 1, 10);
    const hasFan = addonFanCheckbox?.checked || false;

    // Addons
    let addonsTotal = 0;
    if (hasFan) {
      addonsTotal = 300 * diffDays * stallCount;
    }

    // Totals
    const subtotal = ratePerDay * stallCount * diffDays;
    const total = subtotal + addonsTotal;

    // Update UI
    if (calcDaysDisplay) calcDaysDisplay.innerHTML = `<strong>${diffDays} Day${diffDays > 1 ? 's' : ''}</strong> (${stallCount} Stall${stallCount > 1 ? 's' : ''})`;
    if (calcRateDisplay) calcRateDisplay.textContent = `₹${ratePerDay.toLocaleString('en-IN')} / day`;
    if (calcAddonsDisplay) calcAddonsDisplay.textContent = addonsTotal > 0 ? `₹${addonsTotal.toLocaleString('en-IN')}` : '₹0';
    if (calcTotalDisplay) calcTotalDisplay.textContent = `₹${total.toLocaleString('en-IN')}`;
  }

  stallTypeSelect?.addEventListener("change", recalculate);
  startDateInput?.addEventListener("change", recalculate);
  endDateInput?.addEventListener("change", recalculate);
  stallsCountSelect?.addEventListener("change", recalculate);
  addonFanCheckbox?.addEventListener("change", recalculate);

  // Initial calculation
  recalculate();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
