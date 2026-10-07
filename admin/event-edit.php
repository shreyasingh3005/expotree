<?php
/**
 * Edit Exhibition Event
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$adminTitle = 'Edit Exhibition Event';
$activeMenu = 'event-edit';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    setFlash('danger', 'Event not found.');
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token invalid. Please refresh the page.';
    } else {
        $title = sanitize($_POST['title'] ?? '');
        $slug = sanitize($_POST['slug'] ?? '');
        $category = sanitize($_POST['category'] ?? 'Lifestyle & Festive');
        $eventType = sanitize($_POST['event_type'] ?? 'Exhibition');
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
        $footfall = sanitize($_POST['footfall'] ?? '');
        $gentry = sanitize($_POST['gentry'] ?? 'Premium Class');
        $layoutType = sanitize($_POST['layout_type'] ?? 'First come first serve');
        $fanCharge = sanitize($_POST['fan_charge'] ?? '300 RS');
        $totalStalls = max(1, (int)($_POST['total_stalls'] ?? 20));
        $availableStalls = max(0, (int)($_POST['available_stalls'] ?? 0));
        $bookedStalls = max(0, (int)($_POST['booked_stalls'] ?? 0));
        $dailyStallPrice = (float)($_POST['daily_stall_price'] ?? 4000);
        $priceShoppingCanopy = !empty($_POST['price_shopping_canopy']) ? (float)$_POST['price_shopping_canopy'] : null;
        $priceShoppingTable1 = !empty($_POST['price_shopping_table1']) ? (float)$_POST['price_shopping_table1'] : null;
        $priceShoppingTable2 = !empty($_POST['price_shopping_table2']) ? (float)$_POST['price_shopping_table2'] : null;
        $priceFoodCanopy = !empty($_POST['price_food_canopy']) ? (float)$_POST['price_food_canopy'] : null;
        $priceFoodTable2 = !empty($_POST['price_food_table2']) ? (float)$_POST['price_food_table2'] : null;
        $pricePromotional = sanitize($_POST['price_promotional'] ?? '10k onwards');
        $stallSizes = sanitize($_POST['stall_sizes'] ?? 'Canopy: 10x10 ft | Open Table: 6x3 ft');
        $facilities = sanitize($_POST['facilities'] ?? 'Electricity point, 2 Chairs, Tables, Restrooms, Security');
        $bookingInstructions = sanitize($_POST['booking_instructions'] ?? 'Setup allowed from 7:00 AM. Please carry your digital booking confirmation pass.');
        $termsConditions = sanitize($_POST['terms_conditions'] ?? 'Stalls allocated on first-come-first-serve basis. No sub-letting allowed.');
        $description = sanitize($_POST['description'] ?? '');
        $imageUrl = sanitize($_POST['image_url'] ?? '');
        $status = sanitize($_POST['status'] ?? 'published');

        // Handle Image Upload if provided
        if (!empty($_FILES['event_image']['name']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
            $fileInfo = pathinfo($_FILES['event_image']['name']);
            $ext = strtolower($fileInfo['extension'] ?? '');

            if (in_array($ext, $allowedExts) && $_FILES['event_image']['size'] <= 5 * 1024 * 1024) {
                $targetDir = __DIR__ . '/../uploads/events/';
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

        if (empty($title)) $errors[] = 'Event title is required.';
        if (empty($venue)) $errors[] = 'Venue name is required.';
        if (empty($startDate) || empty($endDate)) $errors[] = 'Start date and End date are required.';
        if ($startDate > $endDate) $errors[] = 'End date cannot be earlier than start date.';

        if (empty($slug)) {
            $slug = slugify($title . '-' . $venue . '-' . $city);
        } else {
            $slug = slugify($slug);
        }

        if (empty($dateDisplay)) {
            $dateDisplay = formatDateRange($startDate, $endDate);
        }

        if (empty($errors)) {
            $updStmt = $pdo->prepare("UPDATE events SET
                title = :title,
                slug = :slug,
                category = :cat,
                event_type = :etype,
                start_date = :sdate,
                end_date = :edate,
                date_display = :ddisplay,
                venue = :venue,
                city = :city,
                state = :state,
                pincode = :pin,
                address = :addr,
                location_type = :loctype,
                footfall = :footfall,
                gentry = :gentry,
                timings = :timings,
                layout_type = :layout,
                fan_charge = :fancharge,
                price_shopping_canopy = :ps_canopy,
                price_shopping_table1 = :ps_t1,
                price_shopping_table2 = :ps_t2,
                price_food_canopy = :pf_canopy,
                price_food_table2 = :pf_t2,
                price_promotional = :p_promo,
                daily_stall_price = :daily_price,
                total_stalls = :tot_stalls,
                available_stalls = :avail_stalls,
                booked_stalls = :booked_stalls,
                stall_sizes = :stall_sizes,
                facilities = :facilities,
                booking_instructions = :b_instr,
                terms_conditions = :terms,
                description = :desc,
                image_url = :img,
                status = :status
                WHERE id = :id
            ");

            $res = $updStmt->execute([
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
                ':avail_stalls' => $availableStalls,
                ':booked_stalls' => $bookedStalls,
                ':stall_sizes' => $stallSizes,
                ':facilities' => $facilities,
                ':b_instr' => $bookingInstructions,
                ':terms' => $termsConditions,
                ':desc' => $description,
                ':img' => $imageUrl,
                ':status' => $status,
                ':id' => $id
            ]);

            if ($res) {
                // Synchronize granular stalls table
                sync_event_stalls($pdo, $id);

                setFlash('success', 'Event updated and stall inventory synchronized successfully!');
                header('Location: ' . BASE_URL . '/admin/events.php');
                exit;
            } else {
                $errors[] = 'Update failed. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
  <div>
    <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Edit Exhibition #<?= $event['id'] ?>: <?= e($event['title']) ?></h3>
    <p style="font-size: 0.85rem; color: #6b7280;">Modify event dates, pricing, venue details, or inventory.</p>
  </div>
  <div style="display: flex; gap: 0.5rem;">
    <a href="<?= BASE_URL ?>/event.php?id=<?= $event['id'] ?>" target="_blank" class="admin-btn admin-btn-outline admin-btn-sm">View Live Page ↗</a>
    <a href="events.php" class="admin-btn admin-btn-outline admin-btn-sm">← Back</a>
  </div>
</div>

<?php if (!empty($errors)): ?>
  <div style="background: #fee2e2; border: 1px solid #dc2626; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
    <ul style="margin-left: 1.25rem;">
      <?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" action="event-edit.php?id=<?= $event['id'] ?>" enctype="multipart/form-data" class="admin-card" style="padding: 2rem;">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />

  <!-- Section 1 -->
  <h4 style="font-size: 1rem; font-weight: 700; color: var(--admin-burgundy); border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 1.25rem;">
    ① Exhibition Basic Details
  </h4>

  <div style="margin-bottom: 1.25rem;">
    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Exhibition Title: *</label>
    <input type="text" name="title" value="<?= e($event['title']) ?>" required 
           style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.92rem;" />
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Event Type: *</label>
      <select name="event_type" style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem; background: #fff;" required>
        <option value="Corporate Tech Park" <?= $event['event_type'] === 'Corporate Tech Park' ? 'selected' : '' ?>>Corporate Tech Park</option>
        <option value="Premium Society" <?= $event['event_type'] === 'Premium Society' ? 'selected' : '' ?>>Premium Society</option>
        <option value="Mall Atrium" <?= $event['event_type'] === 'Mall Atrium' ? 'selected' : '' ?>>Mall Atrium</option>
        <option value="Exhibition Mela" <?= stripos($event['event_type'], 'Mela') !== false || stripos($event['event_type'], 'Exhibition') !== false ? 'selected' : '' ?>>Exhibition Mela</option>
      </select>
    </div>

    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Category:</label>
      <input type="text" name="category" value="<?= e($event['category']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>

    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">URL Slug:</label>
      <input type="text" name="slug" value="<?= e($event['slug']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
  </div>

  <!-- Section 2 -->
  <h4 style="font-size: 1rem; font-weight: 700; color: var(--admin-burgundy); border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-top: 2rem; margin-bottom: 1.25rem;">
    ② Venue &amp; Audience Profile
  </h4>

  <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Venue Name: *</label>
      <input type="text" name="venue" value="<?= e($event['venue']) ?>" required 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>

    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">City: *</label>
      <input type="text" name="city" value="<?= e($event['city']) ?>" required 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>

    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">State:</label>
      <input type="text" name="state" value="<?= e($event['state']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Footfall (Excel Column):</label>
      <input type="text" name="footfall" value="<?= e($event['footfall']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>

    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Gentry (Excel Column):</label>
      <input type="text" name="gentry" value="<?= e($event['gentry']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>

    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Setup (Indoors/Outdoors):</label>
      <select name="location_type" style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem; background: #fff;">
        <option value="Indoors" <?= $event['location_type'] === 'Indoors' ? 'selected' : '' ?>>Indoors</option>
        <option value="Outdoors" <?= $event['location_type'] === 'Outdoors' ? 'selected' : '' ?>>Outdoors</option>
      </select>
    </div>

    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Layout Type:</label>
      <input type="text" name="layout_type" value="<?= e($event['layout_type']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
  </div>

  <!-- Section 3 -->
  <h4 style="font-size: 1rem; font-weight: 700; color: var(--admin-burgundy); border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-top: 2rem; margin-bottom: 1.25rem;">
    ③ Dates, Timings &amp; Stall Inventory
  </h4>

  <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Start Date: *</label>
      <input type="date" name="start_date" value="<?= e($event['start_date']) ?>" required 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">End Date: *</label>
      <input type="date" name="end_date" value="<?= e($event['end_date']) ?>" required 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Date Display:</label>
      <input type="text" name="date_display" value="<?= e($event['date_display']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Timings:</label>
      <input type="text" name="timings" value="<?= e($event['timings']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Total Stalls: *</label>
      <input type="number" name="total_stalls" value="<?= e($event['total_stalls']) ?>" required 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Available Stalls:</label>
      <input type="number" name="available_stalls" value="<?= e($event['available_stalls']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Booked Stalls:</label>
      <input type="number" name="booked_stalls" value="<?= e($event['booked_stalls']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Fan Charge:</label>
      <input type="text" name="fan_charge" value="<?= e($event['fan_charge']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
  </div>

  <!-- Section 4 -->
  <h4 style="font-size: 1rem; font-weight: 700; color: var(--admin-burgundy); border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-top: 2rem; margin-bottom: 1.25rem;">
    ④ Stall Pricing Matrix (Excel Columns)
  </h4>

  <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.25rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
      <div>
        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #4b5563; margin-bottom: 0.25rem;">Shopping Canopy (₹):</label>
        <input type="number" name="price_shopping_canopy" value="<?= e($event['price_shopping_canopy']) ?>" 
               style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.88rem;" />
      </div>
      <div>
        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #4b5563; margin-bottom: 0.25rem;">1 Open Table (₹):</label>
        <input type="number" name="price_shopping_table1" value="<?= e($event['price_shopping_table1']) ?>" 
               style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.88rem;" />
      </div>
      <div>
        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #4b5563; margin-bottom: 0.25rem;">2 Open Tables (₹):</label>
        <input type="number" name="price_shopping_table2" value="<?= e($event['price_shopping_table2']) ?>" 
               style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.88rem;" />
      </div>
      <div>
        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #4b5563; margin-bottom: 0.25rem;">Food Canopy (₹):</label>
        <input type="number" name="price_food_canopy" value="<?= e($event['price_food_canopy']) ?>" 
               style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.88rem;" />
      </div>
      <div>
        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #4b5563; margin-bottom: 0.25rem;">Base Daily Stall (₹): *</label>
        <input type="number" name="daily_stall_price" value="<?= e($event['daily_stall_price']) ?>" required 
               style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.88rem;" />
      </div>
    </div>
  </div>

  <!-- Section 5 -->
  <h4 style="font-size: 1rem; font-weight: 700; color: var(--admin-burgundy); border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-top: 2rem; margin-bottom: 1.25rem;">
    ⑤ Stall Configurations, Facilities &amp; Guidelines
  </h4>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Stall Sizes Specification:</label>
      <input type="text" name="stall_sizes" value="<?= e($event['stall_sizes'] ?? 'Canopy: 10x10 ft | Open Table: 6x3 ft') ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Facilities Provided:</label>
      <input type="text" name="facilities" value="<?= e($event['facilities'] ?? 'Electricity point, 2 Chairs, Tables, Restrooms, Security') ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Setup / Booking Instructions:</label>
      <textarea name="booking_instructions" rows="2" 
                style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;"><?= e($event['booking_instructions'] ?? 'Setup allowed from 7:00 AM. Please carry your digital booking confirmation pass.') ?></textarea>
    </div>
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Terms &amp; Stall Policies:</label>
      <textarea name="terms_conditions" rows="2" 
                style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;"><?= e($event['terms_conditions'] ?? 'Stalls allocated on first-come-first-serve basis. No sub-letting allowed.') ?></textarea>
    </div>
  </div>

  <!-- Section 6 -->
  <h4 style="font-size: 1rem; font-weight: 700; color: var(--admin-burgundy); border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-top: 2rem; margin-bottom: 1.25rem;">
    ⑥ Media, Description &amp; Status
  </h4>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Upload Photo File (JPG, PNG, WEBP):</label>
      <input type="file" name="event_image" accept="image/*" 
             style="width: 100%; padding: 0.55rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.85rem; background: #fff;" />
    </div>
    <div>
      <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Or Image URL:</label>
      <input type="url" name="image_url" value="<?= e($event['image_url']) ?>" 
             style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>
  </div>

  <div style="margin-bottom: 1.25rem;">
    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Description:</label>
    <textarea name="description" rows="3" 
              style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;"><?= e($event['description']) ?></textarea>
  </div>

  <div style="margin-bottom: 2rem;">
    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #374151; margin-bottom: 0.4rem;">
      Publishing &amp; Review Status:
    </label>
    <select name="status" style="width: 100%; max-width: 350px; padding: 0.7rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem; background: #fff; font-weight: 600;">
      <option value="published" <?= in_array($event['status'], ['published', 'active']) ? 'selected' : '' ?>>Published (Live on Public Calendar)</option>
      <option value="pending" <?= $event['status'] === 'pending' ? 'selected' : '' ?>>Pending Review (Under Verification)</option>
      <option value="draft" <?= $event['status'] === 'draft' ? 'selected' : '' ?>>Draft (Internal Preparation)</option>
      <option value="unpublished" <?= in_array($event['status'], ['unpublished', 'inactive']) ? 'selected' : '' ?>>Unpublished (Hidden from Public)</option>
      <option value="rejected" <?= $event['status'] === 'rejected' ? 'selected' : '' ?>>Rejected (Declined Proposal)</option>
    </select>
  </div>

  <div style="display: flex; gap: 1rem;">
    <button type="submit" class="admin-btn admin-btn-gold" style="padding: 0.75rem 2rem; font-size: 1rem;">
      Save Changes &amp; Synchronize Stalls
    </button>
    <a href="events.php" class="admin-btn admin-btn-outline" style="padding: 0.75rem 1.5rem;">Cancel</a>
  </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
