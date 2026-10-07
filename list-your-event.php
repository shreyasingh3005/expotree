<?php
/**
 * Event Organizer Self-Listing Portal
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'List Your Exhibition | Partner with Expo Tree Exhibitions';
$pageDesc = 'Are you an RWA, society committee, tech park or mall manager? Submit your exhibition proposal on Expo Tree. Reviewed by our curation desk and promoted to 5,000+ exhibitors across Delhi NCR.';
$currentPage = 'list-your-event';

$formSuccess = false;
$submittedEvent = null;
$formErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_event') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $formErrors[] = 'Security token expired. Please refresh the page and try again.';
    } else {
        $title = sanitize($_POST['title'] ?? '');
        $eventType = sanitize($_POST['event_type'] ?? 'Exhibition');
        $category = sanitize($_POST['category'] ?? 'Lifestyle & Festive');
        $venue = sanitize($_POST['venue'] ?? '');
        $city = sanitize($_POST['city'] ?? 'Gurugram');
        $state = sanitize($_POST['state'] ?? 'Delhi NCR');
        $pincode = sanitize($_POST['pincode'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $startDate = sanitize($_POST['start_date'] ?? '');
        $endDate = sanitize($_POST['end_date'] ?? '');
        $dateDisplay = sanitize($_POST['date_display'] ?? '');
        $timings = sanitize($_POST['timings'] ?? '10:00 AM - 8:00 PM');
        $locationType = sanitize($_POST['location_type'] ?? 'Indoors');
        $footfall = sanitize($_POST['footfall'] ?? '1000+ families');
        $gentry = sanitize($_POST['gentry'] ?? 'Premium Class');
        $layoutType = sanitize($_POST['layout_type'] ?? 'First come first serve');
        $fanCharge = sanitize($_POST['fan_charge'] ?? '300 RS');
        $totalStalls = max(1, (int)($_POST['total_stalls'] ?? 20));
        $dailyStallPrice = (float)($_POST['daily_stall_price'] ?? 4000);
        $priceShoppingCanopy = !empty($_POST['price_shopping_canopy']) ? (float)$_POST['price_shopping_canopy'] : null;
        $priceShoppingTable1 = !empty($_POST['price_shopping_table1']) ? (float)$_POST['price_shopping_table1'] : null;
        $priceShoppingTable2 = !empty($_POST['price_shopping_table2']) ? (float)$_POST['price_shopping_table2'] : null;
        $priceFoodCanopy = !empty($_POST['price_food_canopy']) ? (float)$_POST['price_food_canopy'] : null;
        $priceFoodTable2 = !empty($_POST['price_food_table2']) ? (float)$_POST['price_food_table2'] : null;
        $pricePromotional = sanitize($_POST['price_promotional'] ?? '10k onwards');
        $stallSizes = sanitize($_POST['stall_sizes'] ?? 'Canopy: 10x10 ft | Open Table: 6x3 ft');
        $facilities = sanitize($_POST['facilities'] ?? 'Electricity point, 2 Chairs, Tables, Clean Restrooms, Security & Waste Management');
        $bookingInstructions = sanitize($_POST['booking_instructions'] ?? 'Setup allowed from 7:00 AM. Please carry your digital booking confirmation pass.');
        $termsConditions = sanitize($_POST['terms_conditions'] ?? 'Stalls allocated on first-come-first-serve basis. Sub-letting of stalls is strictly prohibited.');
        $organizerName = sanitize($_POST['organizer_name'] ?? '');
        $organizerCompany = sanitize($_POST['organizer_company'] ?? '');
        $organizerPhone = sanitize($_POST['organizer_phone'] ?? '');
        $organizerWhatsapp = sanitize($_POST['organizer_whatsapp'] ?? $organizerPhone);
        $organizerEmail = sanitize($_POST['organizer_email'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $imageUrl = sanitize($_POST['image_url'] ?? '');

        // Handle File Upload if provided
        if (!empty($_FILES['event_image']['name']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
            $fileInfo = pathinfo($_FILES['event_image']['name']);
            $ext = strtolower($fileInfo['extension'] ?? '');

            if (!in_array($ext, $allowedExts)) {
                $formErrors[] = 'Event image must be a JPG, PNG, or WEBP file.';
            } elseif ($_FILES['event_image']['size'] > 5 * 1024 * 1024) {
                $formErrors[] = 'Image size exceeds maximum limit of 5MB.';
            } else {
                $targetDir = __DIR__ . '/uploads/events/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $newFilename = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetPath = $targetDir . $newFilename;
                if (move_uploaded_file($_FILES['event_image']['tmp_name'], $targetPath)) {
                    $imageUrl = BASE_URL . '/uploads/events/' . $newFilename;
                }
            }
        }

        // Validations
        if (empty($title)) $formErrors[] = 'Exhibition title is required.';
        if (empty($venue)) $formErrors[] = 'Venue name is required.';
        if (empty($city)) $formErrors[] = 'City is required.';
        if (empty($startDate) || empty($endDate)) $formErrors[] = 'Start date and end date are required.';
        if ($startDate > $endDate) $formErrors[] = 'End date cannot be earlier than start date.';
        if (empty($organizerName)) $formErrors[] = 'Organizer contact name is required.';
        if (empty($organizerPhone) || strlen($organizerPhone) < 10) $formErrors[] = 'Valid 10-digit phone number is required.';

        if (empty($imageUrl)) {
            // Authentic exhibition & stall photo fallback based on event type
            if ($eventType === 'Corporate Tech Park') {
                $imageUrl = 'https://images.unsplash.com/photo-1526178613552-2b45c6c302f0?auto=format&fit=crop&w=1200&q=80';
            } elseif ($eventType === 'Premium Society') {
                $imageUrl = 'https://images.unsplash.com/photo-1577900232427-18219b9166a0?auto=format&fit=crop&w=1200&q=80';
            } else {
                $imageUrl = 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=1200&q=80';
            }
        }

        if (empty($dateDisplay)) {
            $dateDisplay = formatDateRange($startDate, $endDate);
        }

        if (empty($formErrors)) {
            // Generate unique slug
            $baseSlug = slugify($title . '-' . $venue . '-' . $city);
            $slug = $baseSlug;
            $counter = 1;
            while (true) {
                $chk = $pdo->prepare("SELECT id FROM events WHERE slug = ?");
                $chk->execute([$slug]);
                if (!$chk->fetch()) break;
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // INSERT WITH PENDING STATUS (requires admin review before going live)
            $insSql = "INSERT INTO events (
                title, slug, category, event_type, start_date, end_date, date_display,
                venue, city, state, pincode, address, location_type, footfall, gentry,
                timings, layout_type, fan_charge, price_shopping_canopy, price_shopping_table1,
                price_shopping_table2, price_food_canopy, price_food_table2, price_promotional,
                daily_stall_price, total_stalls, available_stalls, booked_stalls,
                stall_sizes, facilities, booking_instructions, terms_conditions,
                description, image_url, organizer_name, organizer_company, organizer_phone,
                organizer_whatsapp, organizer_email, status, source
            ) VALUES (
                :title, :slug, :cat, :etype, :sdate, :edate, :ddisplay,
                :venue, :city, :state, :pin, :addr, :loctype, :footfall, :gentry,
                :timings, :layout, :fancharge, :ps_canopy, :ps_t1,
                :ps_t2, :pf_canopy, :pf_t2, :p_promo,
                :daily_price, :tot_stalls, :avail_stalls, 0,
                :stall_sizes, :facilities, :b_instr, :terms,
                :desc, :img, :org_name, :org_comp, :org_phone,
                :org_wa, :org_email, 'pending', 'organizer_submission'
            )";

            $stmtIns = $pdo->prepare($insSql);
            $success = $stmtIns->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':cat' => $category,
                ':etype' => $eventType,
                ':sdate' => $startDate,
                ':edate' => $endDate,
                ':ddisplay' => $dateDisplay,
                ':venue' => $venue,
                ':city' => $city,
                ':state' => $state,
                ':pin' => $pincode,
                ':addr' => $address,
                ':loctype' => $locationType,
                ':footfall' => $footfall,
                ':gentry' => $gentry,
                ':timings' => $timings,
                ':layout' => $layoutType,
                ':fancharge' => $fanCharge,
                ':ps_canopy' => $priceShoppingCanopy,
                ':ps_t1' => $priceShoppingTable1,
                ':ps_t2' => $priceShoppingTable2,
                ':pf_canopy' => $priceFoodCanopy,
                ':pf_t2' => $priceFoodTable2,
                ':p_promo' => $pricePromotional,
                ':daily_price' => $dailyStallPrice,
                ':tot_stalls' => $totalStalls,
                ':avail_stalls' => $totalStalls,
                ':stall_sizes' => $stallSizes,
                ':facilities' => $facilities,
                ':b_instr' => $bookingInstructions,
                ':terms' => $termsConditions,
                ':desc' => $description,
                ':img' => $imageUrl,
                ':org_name' => $organizerName,
                ':org_comp' => $organizerCompany,
                ':org_phone' => $organizerPhone,
                ':org_wa' => $organizerWhatsapp,
                ':org_email' => $organizerEmail
            ]);

            if ($success) {
                $newId = (int)$pdo->lastInsertId();
                // Synchronize granular stalls into event_stalls
                sync_event_stalls($pdo, $newId);

                $submittedEvent = [
                    'id' => $newId,
                    'title' => $title,
                    'venue' => $venue,
                    'city' => $city,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_stalls' => $totalStalls,
                    'organizer_name' => $organizerName,
                    'organizer_phone' => $organizerPhone
                ];
                $formSuccess = true;
                setFlash('success', 'Your exhibition submission has been received and is queued for verification.');
            } else {
                $formErrors[] = 'Failed to submit event. Please contact our support desk at ' . ADMIN_PHONE . '.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="section page-hero-section">
  <div class="hero-bg-photo" aria-hidden="true" style="opacity: 0.18;"></div>
  <div class="container" style="position: relative; z-index: 2;">
    <span class="section-badge badge-gold">✦ Organizer Event Submission Portal</span>
    <h1 class="section-title title-white">List Your Exhibition on Expo Tree</h1>
    <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>
    <p class="section-subtitle subtitle-white" style="max-width: 760px; margin: 0 auto;">
      Are you an RWA representative, Corporate Tech Park Manager, or Mall Events Lead? Submit your exhibition details below. Every submission is verified by our curation team before going live across our network of 5,000+ exhibitors!
    </p>

    <div style="display: flex; justify-content: center; gap: 1.5rem; margin-top: 1.5rem; flex-wrap: wrap;">
      <div style="background: rgba(255,255,255,0.06); padding: 0.5rem 1rem; border-radius: 20px; border: 1px solid rgba(212,175,55,0.3); font-size: 0.88rem;">
        🔍 <strong>Editorial Curation</strong> — Verified Quality
      </div>
      <div style="background: rgba(255,255,255,0.06); padding: 0.5rem 1rem; border-radius: 20px; border: 1px solid rgba(212,175,55,0.3); font-size: 0.88rem;">
        🎪 <strong>Direct Exhibitor Bookings</strong> to your Desk
      </div>
      <div style="background: rgba(255,255,255,0.06); padding: 0.5rem 1rem; border-radius: 20px; border: 1px solid rgba(212,175,55,0.3); font-size: 0.88rem;">
        📍 <strong>Delhi NCR Wide Reach</strong> (50,000+ Shoppers)
      </div>
    </div>
  </div>
</section>

<!-- Form / Success Section -->
<section class="section" style="padding-top: 3rem; padding-bottom: 5rem;">
  <div class="container" style="max-width: 900px;">

    <?php if ($formSuccess && $submittedEvent): ?>
      <!-- Submission Received Banner -->
      <div style="background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); border: 2px solid #3b82f6; border-radius: 20px; padding: 3rem 2rem; color: #ffffff; text-align: center; box-shadow: 0 15px 40px rgba(30, 58, 138, 0.4); margin-bottom: 3rem;">
        <div style="font-size: 3.5rem; margin-bottom: 0.5rem;">📋</div>
        <span class="section-badge badge-gold" style="background: #f59e0b; color: #1a0408; border-color: #fbbf24; margin-bottom: 0.75rem;">Status: Under Review (Ref #<?= $submittedEvent['id'] ?>)</span>
        <h2 style="font-family: var(--font-cinzel); font-size: 2.2rem; color: #93c5fd; margin-bottom: 0.75rem;">Submission Received Successfully!</h2>
        <p style="color: #e0f2fe; font-size: 1.05rem; max-width: 650px; margin: 0 auto 1.5rem; line-height: 1.6;">
          Thank you, <strong><?= e($submittedEvent['organizer_name']) ?></strong>! Your exhibition proposal <strong><?= e($submittedEvent['title']) ?></strong> at <strong><?= e($submittedEvent['venue']) ?>, <?= e($submittedEvent['city']) ?></strong> has been received by our editorial team.
        </p>
        <p style="color: #93c5fd; font-size: 0.95rem; max-width: 600px; margin: 0 auto 1.75rem;">
          Our curation team reviews society/park permissions, footfall credentials, and stall layouts within <strong>2 to 4 business hours</strong>. Once approved, your event will automatically appear on the public events grid and be broadcasted to exhibitors.
        </p>

        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
          <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>?text=<?= urlencode('Hello Expo Tree team, I have submitted an exhibition proposal Ref #' . $submittedEvent['id'] . ' for ' . $submittedEvent['title'] . '. Please verify and approve.') ?>" target="_blank" class="btn btn-gold" style="font-size: 1.05rem; padding: 0.85rem 1.75rem;">
            <span>💬 Expedite Approval via WhatsApp</span>
          </a>
          <a href="<?= BASE_URL ?>/upcoming-exhibitions.php" class="btn btn-outline-white" style="font-size: 1.05rem; padding: 0.85rem 1.75rem;">
            <span>Browse Active Calendar</span>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <!-- Error Alert -->
    <?php if (!empty($formErrors)): ?>
      <div class="site-flash-alert alert-danger" style="margin-bottom: 2rem; border-radius: 12px;">
        <div class="container site-flash-inner" style="flex-direction: column; align-items: flex-start;">
          <strong>Please correct the following fields:</strong>
          <ul style="margin: 0.5rem 0 0 1.25rem;">
            <?php foreach ($formErrors as $err): ?>
              <li><?= e($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>

    <!-- Event Submission Form Card -->
    <div style="background: #ffffff; border: 1.5px solid var(--border-gold); border-radius: 20px; padding: 2.5rem; box-shadow: var(--shadow-card);">
      
      <div style="margin-bottom: 2rem; border-bottom: 1px solid #ebdada; padding-bottom: 1.25rem;">
        <h3 style="font-family: var(--font-cinzel); font-size: 1.5rem; color: var(--burgundy-950); margin-bottom: 0.35rem;">
          Exhibition &amp; Stall Listing Specifications
        </h3>
        <p style="font-size: 0.9rem; color: #666;">
          Complete the details below based on your society/venue layout. The listing will immediately reflect on the public events grid.
        </p>
      </div>

      <form method="POST" action="list-your-event.php" enctype="multipart/form-data">
        <input type="hidden" name="action" value="submit_event" />
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />

        <!-- 1. Basic Exhibition Details -->
        <div style="margin-bottom: 2rem;">
          <h4 style="font-family: var(--font-cinzel); font-size: 1.15rem; color: var(--burgundy-900); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>①</span> Exhibition Basics
          </h4>

          <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
              Exhibition Title: <span style="color: #dc2626;">*</span>
            </label>
            <input type="text" name="title" placeholder="e.g. Festive Lifestyle &amp; Diya Flea Market 2026" required
                   style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.95rem;" />
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Event Type: <span style="color: #dc2626;">*</span>
              </label>
              <select name="event_type" style="width: 100%; padding: 0.75rem 0.85rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem; background: #fff;" required>
                <option value="Corporate Tech Park">Corporate Tech Park</option>
                <option value="Premium Society" selected>Premium Society Flea Market</option>
                <option value="Mall Atrium">Mall Atrium / Retail Hub</option>
                <option value="Exhibition Mela">Grand Exhibition &amp; Festive Mela</option>
              </select>
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Primary Category:
              </label>
              <select name="category" style="width: 100%; padding: 0.75rem 0.85rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem; background: #fff;">
                <option value="Lifestyle & Festive" selected>Lifestyle &amp; Festive</option>
                <option value="Fashion & Jewellery">Fashion &amp; Jewellery</option>
                <option value="Home Decor & Handcrafted">Home Decor &amp; Handcrafted</option>
                <option value="Gourmet & Food Fest">Gourmet &amp; Food Fest</option>
                <option value="All-in-One Grand Exhibition">All-in-One Grand Exhibition</option>
              </select>
            </div>
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
              Event Description &amp; Highlights:
            </label>
            <textarea name="description" rows="3" placeholder="Describe the crowd, festival occasion, high-demand product categories, and event highlights..."
                      style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;"></textarea>
          </div>
        </div>

        <!-- 2. Venue & Location -->
        <div style="margin-bottom: 2rem; border-top: 1px dashed #ebdada; padding-top: 1.5rem;">
          <h4 style="font-family: var(--font-cinzel); font-size: 1.15rem; color: var(--burgundy-900); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>②</span> Venue, Location &amp; Audience
          </h4>

          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Venue / Society / Tech Park Name: <span style="color: #dc2626;">*</span>
              </label>
              <input type="text" name="venue" placeholder="e.g. Ireo Victory Valley, Sector 67" required
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                City: <span style="color: #dc2626;">*</span>
              </label>
              <select name="city" style="width: 100%; padding: 0.75rem 0.85rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem; background: #fff;" required>
                <option value="Gurugram" selected>Gurugram</option>
                <option value="Noida">Noida</option>
                <option value="Delhi">Delhi</option>
                <option value="Faridabad">Faridabad</option>
                <option value="Ghaziabad">Ghaziabad</option>
                <option value="Greater Noida">Greater Noida</option>
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Address / Landmark:
              </label>
              <input type="text" name="address" placeholder="e.g. Golf Course Ext Rd, Badshahpur"
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                State:
              </label>
              <input type="text" name="state" value="Delhi NCR"
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Pincode:
              </label>
              <input type="text" name="pincode" placeholder="122018"
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Expected Footfall:
              </label>
              <input type="text" name="footfall" placeholder="e.g. 1500+ families / 4k employees"
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Gentry Profile:
              </label>
              <select name="gentry" style="width: 100%; padding: 0.75rem 0.85rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem; background: #fff;">
                <option value="Premium Class" selected>Premium Class</option>
                <option value="Middle Class">Middle Class</option>
                <option value="HNIs & Executives">HNIs &amp; Executives</option>
              </select>
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Location Setup:
              </label>
              <select name="location_type" style="width: 100%; padding: 0.75rem 0.85rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem; background: #fff;">
                <option value="Indoors" selected>Indoors (Central AC Atrium)</option>
                <option value="Outdoors">Outdoors (Clubhouse Lawn / Courtyard)</option>
                <option value="Semi-Covered">Semi-Covered Canopy Area</option>
              </select>
            </div>
          </div>
        </div>

        <!-- 3. Dates, Timings & Layout -->
        <div style="margin-bottom: 2rem; border-top: 1px dashed #ebdada; padding-top: 1.5rem;">
          <h4 style="font-family: var(--font-cinzel); font-size: 1.15rem; color: var(--burgundy-900); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>③</span> Dates, Timings &amp; Stall Inventory
          </h4>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Start Date: <span style="color: #dc2626;">*</span>
              </label>
              <input type="date" name="start_date" required min="<?= date('Y-m-d') ?>"
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                End Date: <span style="color: #dc2626;">*</span>
              </label>
              <input type="date" name="end_date" required min="<?= date('Y-m-d') ?>"
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Event Timings:
              </label>
              <input type="text" name="timings" value="10:00 AM - 8:00 PM"
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Total Stalls Capacity: <span style="color: #dc2626;">*</span>
              </label>
              <input type="number" name="total_stalls" value="25" min="1" max="500" required
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Stall Allocation:
              </label>
              <select name="layout_type" style="width: 100%; padding: 0.75rem 0.85rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem; background: #fff;">
                <option value="First come first serve" selected>First Come First Serve</option>
                <option value="Layout blueprint available">Blueprint Layout Allotted</option>
              </select>
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Industrial Fans:
              </label>
              <input type="text" name="fan_charge" value="300 RS"
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>
          </div>

          <!-- Stall Pricing Fields (Matching Excel Schema) -->
          <div style="background: #faf7f8; border: 1px solid #ebdada; border-radius: 12px; padding: 1.25rem;">
            <div style="font-size: 0.88rem; font-weight: 700; color: var(--burgundy-900); margin-bottom: 0.75rem;">
              Stall Pricing Structure (Enter rates applicable for your venue):
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem;">
              <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #555; margin-bottom: 0.25rem;">Shopping Canopy (₹):</label>
                <input type="number" name="price_shopping_canopy" placeholder="e.g. 5000"
                       style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d8c3c7; border-radius: 8px; font-size: 0.88rem;" />
              </div>
              <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #555; margin-bottom: 0.25rem;">1 Open Table (₹):</label>
                <input type="number" name="price_shopping_table1" placeholder="e.g. 3500"
                       style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d8c3c7; border-radius: 8px; font-size: 0.88rem;" />
              </div>
              <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #555; margin-bottom: 0.25rem;">2 Open Tables (₹):</label>
                <input type="number" name="price_shopping_table2" placeholder="e.g. 4500"
                       style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d8c3c7; border-radius: 8px; font-size: 0.88rem;" />
              </div>
              <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #555; margin-bottom: 0.25rem;">Food Canopy (₹):</label>
                <input type="number" name="price_food_canopy" placeholder="e.g. 6000"
                       style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d8c3c7; border-radius: 8px; font-size: 0.88rem;" />
              </div>
              <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #555; margin-bottom: 0.25rem;">Base Daily Stall (₹):</label>
                <input type="number" name="daily_stall_price" value="4000" required
                       style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d8c3c7; border-radius: 8px; font-size: 0.88rem;" />
              </div>
            </div>
          </div>
          <!-- Facilities, Sizes & Guidelines -->
          <div style="background: #faf7f8; border: 1px solid #ebdada; border-radius: 12px; padding: 1.25rem; margin-top: 1rem;">
            <div style="font-size: 0.88rem; font-weight: 700; color: var(--burgundy-900); margin-bottom: 0.75rem;">
              Stall Sizes &amp; Facilities Provided:
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
              <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 0.25rem;">Stall Sizes Available:</label>
                <input type="text" name="stall_sizes" value="Canopy: 10x10 ft | Open Table: 6x3 ft"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d8c3c7; border-radius: 8px; font-size: 0.88rem;" />
              </div>
              <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 0.25rem;">Key Facilities &amp; Amenities:</label>
                <input type="text" name="facilities" value="Electricity point, 2 Chairs, Tables, Dustbins, Security, Clean Restrooms"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d8c3c7; border-radius: 8px; font-size: 0.88rem;" />
              </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
              <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 0.25rem;">Exhibitor Setup Instructions:</label>
                <input type="text" name="booking_instructions" value="Setup from 7:00 AM on opening day. Carry digital ID &amp; booking reference."
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d8c3c7; border-radius: 8px; font-size: 0.88rem;" />
              </div>
              <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 0.25rem;">Terms &amp; Stall Policies:</label>
                <input type="text" name="terms_conditions" value="No subletting. Strict compliance with society noise &amp; waste management rules."
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d8c3c7; border-radius: 8px; font-size: 0.88rem;" />
              </div>
            </div>
          </div>

          <!-- Event Photo Upload -->
          <div style="background: #faf7f8; border: 1px solid #ebdada; border-radius: 12px; padding: 1.25rem; margin-top: 1rem;">
            <div style="font-size: 0.88rem; font-weight: 700; color: var(--burgundy-900); margin-bottom: 0.75rem;">
              Venue / Exhibition Photo:
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
              <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 0.25rem;">Upload Photo File (JPG, PNG, WEBP):</label>
                <input type="file" name="event_image" accept="image/*"
                       style="width: 100%; padding: 0.55rem; border: 1px solid #d8c3c7; border-radius: 8px; font-size: 0.85rem; background: #fff;" />
              </div>
              <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 0.25rem;">Or Image URL:</label>
                <input type="url" name="image_url" placeholder="https://..."
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d8c3c7; border-radius: 8px; font-size: 0.88rem;" />
              </div>
            </div>
          </div>
        </div>

        <!-- 4. Organizer Contact Information -->
        <div style="margin-bottom: 2.25rem; border-top: 1px dashed #ebdada; padding-top: 1.5rem;">
          <h4 style="font-family: var(--font-cinzel); font-size: 1.15rem; color: var(--burgundy-900); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>④</span> Organizer &amp; Contact Details
          </h4>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Organizer / Contact Person: <span style="color: #dc2626;">*</span>
              </label>
              <input type="text" name="organizer_name" placeholder="e.g. Rajesh Khurana" required
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Organization / RWA / Company:
              </label>
              <input type="text" name="organizer_company" placeholder="e.g. Ireo Victory Valley RWA"
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Phone Number: <span style="color: #dc2626;">*</span>
              </label>
              <input type="tel" name="organizer_phone" placeholder="98111XXXXX" pattern="[0-9]{10}" required
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                WhatsApp Number:
              </label>
              <input type="tel" name="organizer_whatsapp" placeholder="98111XXXXX"
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.4rem;">
                Email Address:
              </label>
              <input type="email" name="organizer_email" placeholder="contact@venue.org"
                     style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-gold" style="width: 100%; justify-content: center; font-size: 1.15rem; padding: 1rem; box-shadow: 0 6px 20px rgba(212,175,55,0.45); border: none; cursor: pointer;">
          <span>✦ Submit Exhibition for Verification</span>
        </button>

        <div style="text-align: center; font-size: 0.82rem; color: #888; margin-top: 1rem;">
          ⚡ All organizer submissions are verified by our curation desk before going live. Typical review turnaround is 2-4 business hours.
        </div>
      </form>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
