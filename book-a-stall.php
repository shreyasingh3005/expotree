<?php
/**
 * Dedicated Stall Booking Center
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Book a Stall | Exhibitor Registration | Expo Tree Exhibitions';
$pageDesc = 'Reserve your exhibition stall across premier tech parks, luxury malls, and societies in Delhi NCR. Instant WhatsApp layout confirmation with hotline 9811175057.';
$currentPage = 'book-a-stall';

// Fetch all active events for the dropdown
$eventsStmt = $pdo->query("SELECT id, title, venue, city, start_date, end_date, date_display, available_stalls, daily_stall_price, price_shopping_canopy, price_shopping_table1, price_shopping_table2 FROM events WHERE status IN ('published', 'active') ORDER BY start_date ASC");
$activeEvents = $eventsStmt->fetchAll();

// Pre-selected event ID if passed
$preSelectedId = (int)($_GET['event_id'] ?? 0);

// Process Submission
$bookingSuccess = false;
$confirmedBooking = null;
$waRedirectUrl = '';
$formErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book_stall_hub') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $formErrors[] = 'Security token invalid. Please refresh the page.';
    } else {
        $eventId = (int)($_POST['event_id'] ?? 0);
        $customerName = sanitize($_POST['customer_name'] ?? '');
        $businessName = sanitize($_POST['business_name'] ?? '');
        $mobile = sanitize($_POST['mobile'] ?? '');
        $whatsapp = sanitize($_POST['whatsapp'] ?? $mobile);
        $email = sanitize($_POST['email'] ?? '');
        $categoryName = sanitize($_POST['category_name'] ?? 'Jewellery & Accessories');
        $stallType = sanitize($_POST['stall_type'] ?? 'Prime Corner Canopy');
        $stallsCount = max(1, (int)($_POST['stalls_count'] ?? 1));
        $startDate = sanitize($_POST['start_date'] ?? '');
        $endDate = sanitize($_POST['end_date'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');

        // Fetch chosen event
        $evStmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $evStmt->execute([$eventId]);
        $targetEvent = $evStmt->fetch();

        if (!$targetEvent) {
            $formErrors[] = 'Please select a valid upcoming exhibition.';
        }
        if (empty($customerName)) $formErrors[] = 'Full name is required.';
        if (empty($mobile) || strlen($mobile) < 10) $formErrors[] = 'Valid 10-digit mobile number is required.';

        if ($targetEvent) {
            if (empty($startDate)) $startDate = $targetEvent['start_date'];
            if (empty($endDate)) $endDate = $targetEvent['end_date'];

            if ($targetEvent['available_stalls'] <= 0) {
                $formErrors[] = 'This exhibition is currently sold out.';
            } elseif ($stallsCount > $targetEvent['available_stalls']) {
                $formErrors[] = "Only {$targetEvent['available_stalls']} stalls left for this exhibition.";
            }

            if (empty($formErrors)) {
                $startTs = strtotime($startDate);
                $endTs = strtotime($endDate);
                $daysCount = max(1, round(($endTs - $startTs) / 86400) + 1);

                $rate = (float)$targetEvent['daily_stall_price'];
                if (stripos($stallType, 'Canopy') !== false && !empty($targetEvent['price_shopping_canopy'])) {
                    $rate = (float)$targetEvent['price_shopping_canopy'];
                } elseif (stripos($stallType, 'Table') !== false && !empty($targetEvent['price_shopping_table1'])) {
                    $rate = (float)$targetEvent['price_shopping_table1'];
                }

                $totalAmount = $rate * $daysCount * $stallsCount;
                $bookingNumber = generateBookingNumber();

                $insStmt = $pdo->prepare("INSERT INTO bookings (
                    booking_number, event_id, customer_name, business_name, mobile, whatsapp,
                    email, category_name, stall_type, start_date, end_date, days_count,
                    stalls_count, price_per_day, total_amount, notes, status, whatsapp_sent
                ) VALUES (
                    :bnum, :eid, :cname, :bname, :mob, :wa,
                    :email, :cat, :stype, :sdate, :edate, :days,
                    :stalls, :rate, :total, :notes, 'confirmed', 1
                )");

                $insStmt->execute([
                    ':bnum' => $bookingNumber,
                    ':eid' => $targetEvent['id'],
                    ':cname' => $customerName,
                    ':bname' => $businessName,
                    ':mob' => $mobile,
                    ':wa' => $whatsapp,
                    ':email' => $email,
                    ':cat' => $categoryName,
                    ':stype' => $stallType,
                    ':sdate' => $startDate,
                    ':edate' => $endDate,
                    ':days' => $daysCount,
                    ':stalls' => $stallsCount,
                    ':rate' => $rate,
                    ':total' => $totalAmount,
                    ':notes' => $notes
                ]);

                // Decrement stalls
                recordStallBooking($pdo, $targetEvent['id'], $stallsCount);

                $confirmedBooking = [
                    'booking_number' => $bookingNumber,
                    'customer_name' => $customerName,
                    'business_name' => $businessName,
                    'mobile' => $mobile,
                    'event_title' => $targetEvent['title'],
                    'event_venue' => $targetEvent['venue'],
                    'event_city' => $targetEvent['city'],
                    'category_name' => $categoryName,
                    'stall_type' => $stallType,
                    'start_date' => date('d M Y', strtotime($startDate)),
                    'end_date' => date('d M Y', strtotime($endDate)),
                    'days_count' => $daysCount,
                    'stalls_count' => $stallsCount,
                    'price_per_day' => $rate,
                    'total_amount' => $totalAmount,
                    'notes' => $notes
                ];

                $waRedirectUrl = buildWhatsAppBookingUrl($confirmedBooking);
                $bookingSuccess = true;
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero Header -->
<section class="section page-hero-section">
  <div class="hero-bg-photo" aria-hidden="true" style="opacity: 0.18;"></div>
  <div class="container" style="position: relative; z-index: 2;">
    <span class="section-badge badge-gold">For Exhibitors, Designers &amp; Food Creators</span>
    <h1 class="section-title title-white">Book Your Stall &amp; Maximize Your Sales</h1>
    <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>
    <p class="section-subtitle subtitle-white" style="max-width: 800px; margin: 0 auto;">
      Connect your brand directly with 15,000 to 35,000+ affluent shoppers in Delhi NCR’s top luxury tech parks, gated societies, and prime mall atriums. Stalls allotted on a first-come basis!
    </p>
  </div>
</section>

<!-- Interactive Stall Booking Studio Section -->
<section id="booking-studio" class="section booking-studio-section" style="padding-top: 3rem; padding-bottom: 5rem;">
  <div class="container">

    <!-- Confirmation Modal / Banner -->
    <?php if ($bookingSuccess && $confirmedBooking): ?>
      <div style="background: linear-gradient(135deg, #064E3B 0%, #022c22 100%); border: 2px solid #10B981; border-radius: 20px; padding: 2.5rem 2rem; color: #ffffff; margin-bottom: 3rem; text-align: center; box-shadow: 0 15px 40px rgba(6, 78, 59, 0.4);">
        <div style="font-size: 3.5rem; margin-bottom: 0.5rem;">🎉</div>
        <span class="section-badge badge-gold" style="background: #10B981; color: #ffffff; border-color: #34D399; margin-bottom: 0.75rem;">Stall Registration Received</span>
        <h2 style="font-family: var(--font-cinzel); font-size: 2rem; color: #A7F3D0; margin-bottom: 0.5rem;">Reservation Request Confirmed!</h2>
        <p style="color: #D1FAE5; max-width: 650px; margin: 0 auto 1.5rem; font-size: 1.05rem;">
          Thank you, <strong><?= e($confirmedBooking['customer_name']) ?></strong>! Your booking reference is <code><?= e($confirmedBooking['booking_number']) ?></code> for <strong><?= e($confirmedBooking['event_title']) ?></strong>.
        </p>
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
          <a href="<?= $waRedirectUrl ?>" target="_blank" rel="noopener noreferrer" class="btn" style="background: #25D366; color: #ffffff; font-size: 1.1rem; padding: 0.85rem 2rem; font-weight: 700;">
            <span>💬 Confirm on WhatsApp Now (9811175057)</span>
          </a>
          <a href="<?= BASE_URL ?>/upcoming-exhibitions.php" class="btn btn-outline-white">
            <span>Explore Other Exhibitions</span>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($formErrors)): ?>
      <div class="site-flash-alert alert-danger" style="margin-bottom: 2rem; border-radius: 12px;">
        <div class="container site-flash-inner" style="flex-direction: column; align-items: flex-start;">
          <strong>Please correct the following:</strong>
          <ul style="margin: 0.5rem 0 0 1.25rem;">
            <?php foreach ($formErrors as $err): ?>
              <li><?= e($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>

    <form method="POST" action="book-a-stall.php#booking-studio">
      <input type="hidden" name="action" value="book_stall_hub" />
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
      <input type="hidden" name="stall_type" id="hidden_stall_type" value="Shopping Canopy (2 Tables + 2 Chairs)" />

      <div class="booking-studio-layout">
        
        <!-- Left: Interactive Wizard -->
        <div class="wizard-panel">
          
          <!-- Step 1: Exhibition Selection -->
          <div class="wizard-group">
            <div class="wizard-step-title">
              <span class="step-number-circle">1</span>
              <span>Select Target Exhibition &amp; City</span>
            </div>
            <label class="wizard-label" for="wizard-exhibition-select">Upcoming Exhibition: <span style="color: #dc2626;">*</span></label>
            <select id="wizard-exhibition-select" name="event_id" class="wizard-select" required>
              <?php foreach ($activeEvents as $aev): ?>
                <?php $isSelected = ($preSelectedId === (int)$aev['id']); ?>
                <option value="<?= $aev['id'] ?>" 
                        data-city="<?= e($aev['city']) ?>" 
                        data-dates="<?= e($aev['date_display'] ?? formatDateRange($aev['start_date'], $aev['end_date'])) ?>" 
                        data-start="<?= e($aev['start_date']) ?>"
                        data-end="<?= e($aev['end_date']) ?>"
                        data-stalls="<?= $aev['available_stalls'] ?>"
                        data-price="<?= $aev['daily_stall_price'] ?>"
                        <?= $isSelected ? 'selected' : '' ?>>
                  <?= e($aev['title']) ?> (<?= e($aev['venue']) ?>, <?= e($aev['city']) ?>) — <?= $aev['available_stalls'] ?> Stalls Open
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Step 2: Brand Category & Personal Info -->
          <div class="wizard-group">
            <div class="wizard-step-title">
              <span class="step-number-circle">2</span>
              <span>Your Brand &amp; Contact Details</span>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
              <div>
                <label class="wizard-label">Full Name: <span style="color: #dc2626;">*</span></label>
                <input type="text" name="customer_name" class="wizard-input" placeholder="e.g. Ananya Mehra" required />
              </div>
              <div>
                <label class="wizard-label">Brand / Business Name:</label>
                <input type="text" name="business_name" class="wizard-input" placeholder="e.g. Aura Handmade" />
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
              <div>
                <label class="wizard-label">Phone Number: <span style="color: #dc2626;">*</span></label>
                <input type="tel" name="mobile" class="wizard-input" placeholder="98111XXXXX" pattern="[0-9]{10}" required />
              </div>
              <div>
                <label class="wizard-label">Product Category:</label>
                <select name="category_name" id="wizard-category-select" class="wizard-select">
                  <option value="Jewellery & Accessories">Jewellery &amp; Accessories</option>
                  <option value="Handbags">Handbags</option>
                  <option value="Apparel & Footwear">Apparel &amp; Footwear</option>
                  <option value="Gifting & Home Décor">Gifting &amp; Home Décor</option>
                  <option value="Skincare & Perfumes">Skincare &amp; Perfumes</option>
                  <option value="Crochet & Handmade">Crochet &amp; Handmade</option>
                  <option value="Candles & Soaps">Candles &amp; Soaps</option>
                  <option value="Wooden Toys">Wooden Toys</option>
                  <option value="Home Furnishings">Home Furnishings</option>
                  <option value="Bakery & Dryfruits">Bakery &amp; Dryfruits</option>
                  <option value="Food Stalls">Food Stalls / Live Snacks</option>
                  <option value="Handmade Products">Handmade Products</option>
                  <option value="Kidswear">Kidswear</option>
                  <option value="Tarot Card Reading">Tarot Card Reading</option>
                  <option value="Healing Products">Healing Products</option>
                  <option value="Toys & Stationery">Toys &amp; Stationery</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Step 3: Stall Type Selection -->
          <div class="wizard-group">
            <div class="wizard-step-title">
              <span class="step-number-circle">3</span>
              <span>Choose Your Stall Configuration</span>
            </div>
            <div class="stall-options-grid">
              <div class="stall-type-card selected" data-stall="Shopping Canopy (2 Tables + 2 Chairs)" data-rate-multiplier="1">
                <div class="stall-type-header">
                  <div class="stall-name">Prime Shopping Canopy</div>
                  <div class="stall-badge">Most Popular</div>
                </div>
                <div class="stall-desc">Complete canopy setup with 2 draped tables, 2 chairs, power point &amp; prime corridor visibility.</div>
              </div>

              <div class="stall-type-card" data-stall="1 Open Table (1 Table + 1 Chair)" data-rate-multiplier="0.8">
                <div class="stall-type-header">
                  <div class="stall-name">1 Open Table Setup</div>
                  <div class="stall-badge">Startup Friendly</div>
                </div>
                <div class="stall-desc">Compact 1 draped table + 1 chair. Ideal for jewellery, organic skincare, tarot &amp; crafts.</div>
              </div>

              <div class="stall-type-card" data-stall="2 Open Tables (2 Tables + 2 Chairs)" data-rate-multiplier="0.95">
                <div class="stall-type-header">
                  <div class="stall-name">2 Open Tables Setup</div>
                  <div class="stall-badge">Spacious</div>
                </div>
                <div class="stall-desc">Open display counter with 2 tables and 2 chairs for apparel, footwear &amp; home decor.</div>
              </div>

              <div class="stall-type-card" data-stall="Food Canopy Stall" data-rate-multiplier="1.15">
                <div class="stall-type-header">
                  <div class="stall-name">Food &amp; Snacks Canopy</div>
                  <div class="stall-badge">Gourmet Zone</div>
                </div>
                <div class="stall-desc">Special food zone setup with power point, tables, and high evening crowd footfall.</div>
              </div>
            </div>
          </div>

          <!-- Step 4: Stalls Count & Duration -->
          <div class="wizard-group">
            <div class="wizard-step-title">
              <span class="step-number-circle">4</span>
              <span>Stalls Quantity &amp; Special Requirements</span>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
              <div>
                <label class="wizard-label">Number of Stalls:</label>
                <select name="stalls_count" id="wizard_stalls_count" class="wizard-select">
                  <option value="1">1 Stall</option>
                  <option value="2">2 Stalls</option>
                  <option value="3">3 Stalls</option>
                </select>
              </div>
              <div>
                <label class="wizard-label">Special Notes / Display Equipment:</label>
                <input type="text" name="notes" class="wizard-input" placeholder="e.g. Bringing 1 extra clothing rack, corner preference..." />
              </div>
            </div>
          </div>

        </div>

        <!-- Right: Live Summary & WhatsApp CTA -->
        <div class="booking-summary-card">
          <div class="summary-crest">
            <img src="<?= BASE_URL ?>/images/logo.svg" alt="Expo Tree Emblem" width="130" />
            <div class="summary-title">STALL BOOKING SUMMARY</div>
          </div>

          <div class="summary-rows">
            <div class="summary-row">
              <span class="summary-label">Exhibition:</span>
              <span class="summary-value" id="summary-event-name">Grand Festive Exhibition</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Dates:</span>
              <span class="summary-value" id="summary-event-dates">Scheduled Calendar</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Category:</span>
              <span class="summary-value" id="summary-category-name">Jewellery &amp; Accessories</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Stall Type:</span>
              <span class="summary-value" id="summary-stall-type">Prime Shopping Canopy</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Stall Count:</span>
              <span class="summary-value" id="summary-stalls-count">1 Stall</span>
            </div>
          </div>

          <ul class="summary-perks">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Guaranteed Prime Footfall Placement</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Tables, Chairs, Fabric Draping &amp; Lighting Included</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Promotion on 88.9K+ Follower Instagram Page</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Instant Dedicated WhatsApp Support (9811175057)</span>
            </li>
          </ul>

          <button type="submit" class="btn btn-gold" style="width: 100%; justify-content: center; font-size: 1.05rem; padding: 0.9rem; margin-top: 1rem; box-shadow: 0 4px 15px rgba(212,175,55,0.4);">
            <span>🎪 Reserve Stall Now</span>
          </button>

          <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>?text=<?= urlencode('Hello Expo Tree! I want to check available stalls and rates.') ?>" target="_blank" rel="noopener noreferrer" class="btn" style="width: 100%; justify-content: center; background: #25D366; color: #ffffff; margin-top: 0.5rem; font-size: 0.95rem; padding: 0.75rem;">
            <span>💬 Inquire on WhatsApp</span>
          </a>
        </div>

      </div>
    </form>

  </div>
</section>

<!-- Wizard Dynamic JS -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const eventSelect = document.getElementById("wizard-exhibition-select");
  const categorySelect = document.getElementById("wizard-category-select");
  const stallCards = document.querySelectorAll(".stall-type-card");
  const hiddenStallType = document.getElementById("hidden_stall_type");
  const stallsCountSelect = document.getElementById("wizard_stalls_count");

  const sumEventName = document.getElementById("summary-event-name");
  const sumEventDates = document.getElementById("summary-event-dates");
  const sumCategory = document.getElementById("summary-category-name");
  const sumStallType = document.getElementById("summary-stall-type");
  const sumStallsCount = document.getElementById("summary-stalls-count");

  function updateSummary() {
    if (eventSelect && sumEventName && sumEventDates) {
      const opt = eventSelect.options[eventSelect.selectedIndex];
      if (opt) {
        sumEventName.textContent = opt.text.split('—')[0].trim();
        sumEventDates.textContent = opt.getAttribute("data-dates") || '';
      }
    }
    if (categorySelect && sumCategory) {
      sumCategory.textContent = categorySelect.value;
    }
    if (stallsCountSelect && sumStallsCount) {
      sumStallsCount.textContent = `${stallsCountSelect.value} Stall(s)`;
    }
  }

  stallCards.forEach(card => {
    card.addEventListener("click", function() {
      stallCards.forEach(c => c.classList.remove("selected"));
      this.classList.add("selected");
      const stallName = this.getAttribute("data-stall");
      if (hiddenStallType) hiddenStallType.value = stallName;
      if (sumStallType) sumStallType.textContent = stallName;
    });
  });

  eventSelect?.addEventListener("change", updateSummary);
  categorySelect?.addEventListener("change", updateSummary);
  stallsCountSelect?.addEventListener("change", updateSummary);

  updateSummary();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
