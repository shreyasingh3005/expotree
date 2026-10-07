<?php
/**
 * Excel / CSV Listing Importer
 * Expo Tree Exhibitions Admin
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$adminTitle = 'Excel / CSV Exhibition Importer';
$activeMenu = 'import';

$importResults = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security verification failed. Please refresh and try again.';
    } else {
        $csvContent = '';

        // Check file upload or pasted text
        if (!empty($_FILES['csv_file']['tmp_name'])) {
            $csvContent = file_get_contents($_FILES['csv_file']['tmp_name']);
        } elseif (!empty($_POST['csv_text'])) {
            $csvContent = trim($_POST['csv_text']);
        }

        if (empty($csvContent)) {
            $errors[] = 'Please upload a CSV file or paste CSV text to import.';
        } else {
            $lines = preg_split('/\r\n|\r|\n/', trim($csvContent));
            $imported = 0;
            $updated = 0;
            $skipped = 0;
            $rowDetails = [];

            $cleanNum = function($v) {
                if (strtoupper($v) === 'NA' || empty($v) || $v === '-') return null;
                $n = preg_replace('/[^0-9.]/', '', $v);
                return is_numeric($n) ? (float)$n : null;
            };

            foreach ($lines as $idx => $line) {
                $line = trim($line);
                if (empty($line)) continue;
                $cols = array_map('trim', str_getcsv($line));

                // Skip header lines or section markers
                if (count($cols) < 5) continue;
                if (stripos($cols[0], 'DATE') !== false || stripos($cols[0], 'OCTOBER') !== false || stripos($cols[0], 'NOVEMBER') !== false) continue;
                if (empty($cols[1])) continue; // No venue

                $dateRaw = $cols[0];
                $venue = $cols[1];
                $city = $cols[2] ?? 'Delhi NCR';
                $footfall = $cols[3] ?? 'High Footfall';
                $eventType = $cols[4] ?? 'Exhibition';
                $pShopCanopy = $cleanNum($cols[5] ?? '');
                $pShopT1 = $cleanNum($cols[6] ?? '');
                $pShopT2 = $cleanNum($cols[7] ?? '');
                $pFoodCanopy = $cleanNum($cols[8] ?? '');
                $pFoodT2 = $cleanNum($cols[9] ?? '');
                $pPromo = trim($cols[10] ?? '10k onwards');
                $totStallsRaw = $cols[11] ?? '20';
                $gentry = $cols[12] ?? 'Middle Class';
                $timings = $cols[13] ?? '10:00 AM - 8:00 PM';
                $locationType = $cols[14] ?? 'Outdoors';
                $layoutType = $cols[15] ?? 'First come first serve';
                $fanCharge = $cols[16] ?? '-';

                // Parse dates
                $startDate = '';
                $endDate = '';
                $dateDisplay = $dateRaw;

                if (preg_match('/^(\d{4}-\d{2}-\d{2})$/', $dateRaw)) {
                    $startDate = $dateRaw;
                    $endDate = $dateRaw;
                } elseif (preg_match('/(\d{1,2})[-–](\d{1,2})[-–](\d{1,2})\s*([A-Za-z]+)/i', $dateRaw, $m)) {
                    $mNum = date('m', strtotime($m[4] . ' 1 2026'));
                    $startDate = "2026-{$mNum}-" . sprintf('%02d', $m[1]);
                    $endDate = "2026-{$mNum}-" . sprintf('%02d', $m[3]);
                } elseif (preg_match('/(\d{1,2})[-–](\d{1,2})\s*([A-Za-z]+)/i', $dateRaw, $m)) {
                    $mNum = date('m', strtotime($m[3] . ' 1 2026'));
                    $startDate = "2026-{$mNum}-" . sprintf('%02d', $m[1]);
                    $endDate = "2026-{$mNum}-" . sprintf('%02d', $m[2]);
                } else {
                    $startDate = date('Y-m-d');
                    $endDate = date('Y-m-d');
                }

                $totalStalls = max(10, (int)preg_replace('/[^0-9]/', '', $totStallsRaw));
                $dailyPrice = $pShopCanopy ?: ($pShopT1 ?: 5000);

                // Check duplicate by venue & start_date
                $chk = $pdo->prepare("SELECT id FROM events WHERE venue LIKE ? AND start_date = ?");
                $chk->execute(['%' . $venue . '%', $startDate]);
                $existingId = $chk->fetchColumn();

                if ($existingId) {
                    $upd = $pdo->prepare("
                        UPDATE events SET
                            price_shopping_canopy = COALESCE(price_shopping_canopy, ?),
                            price_shopping_table1 = COALESCE(price_shopping_table1, ?),
                            price_shopping_table2 = COALESCE(price_shopping_table2, ?),
                            price_food_canopy = COALESCE(price_food_canopy, ?),
                            price_food_table2 = COALESCE(price_food_table2, ?),
                            price_promotional = COALESCE(price_promotional, ?),
                            status = 'published'
                        WHERE id = ?
                    ");
                    $upd->execute([$pShopCanopy, $pShopT1, $pShopT2, $pFoodCanopy, $pFoodT2, $pPromo, $existingId]);
                    sync_event_stalls($pdo, $existingId);
                    $updated++;
                    $rowDetails[] = [
                        'status' => 'Updated',
                        'venue' => $venue,
                        'city' => $city,
                        'date' => $startDate,
                        'price' => formatPrice($dailyPrice)
                    ];
                } else {
                    $title = "{$venue} {$eventType} Lifestyle Exhibition";
                    $slug = slugify("{$title}-{$startDate}");

                    // Authentic Exhibition & Stall Photo selector
                    if (stripos($eventType, 'Corporate') !== false) {
                        $img = 'https://images.unsplash.com/photo-1526178613552-2b45c6c302f0?auto=format&fit=crop&w=800&q=80';
                    } elseif (stripos($eventType, 'Society') !== false) {
                        $img = 'https://images.unsplash.com/photo-1577900232427-18219b9166a0?auto=format&fit=crop&w=800&q=80';
                    } else {
                        $img = 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=800&q=80';
                    }

                    $desc = "Curated {$eventType} exhibition hosted at {$venue}, {$city}. Direct exposure to {$footfall} affluent attendees with dedicated setups for Jewellery, Apparel, Home Decor, Bakery & Food counters.";

                    $ins = $pdo->prepare("
                        INSERT INTO events (
                            title, slug, category, event_type, start_date, end_date, date_display,
                            venue, city, state, location_type, footfall, gentry, timings, layout_type,
                            fan_charge, price_shopping_canopy, price_shopping_table1, price_shopping_table2,
                            price_food_canopy, price_food_table2, price_promotional, daily_stall_price,
                            total_stalls, available_stalls, booked_stalls, description, image_url,
                            status, is_featured, source
                        ) VALUES (
                            :title, :slug, 'Lifestyle & Festive', :etype, :sdate, :edate, :ddisplay,
                            :venue, :city, 'Delhi NCR', :loctype, :footfall, :gentry, :timings, :layout,
                            :fancharge, :ps_canopy, :ps_t1, :ps_t2, :pf_canopy, :pf_t2, :p_promo, :daily_price,
                            :tot_stalls, :avail_stalls, 0, :desc, :img, 'published', 1, 'admin'
                        )
                    ");

                    $ins->execute([
                        ':title' => $title,
                        ':slug' => $slug,
                        ':etype' => $eventType,
                        ':sdate' => $startDate,
                        ':edate' => $endDate,
                        ':ddisplay' => $dateDisplay,
                        ':venue' => $venue,
                        ':city' => $city,
                        ':loctype' => $locationType,
                        ':footfall' => $footfall,
                        ':gentry' => $gentry,
                        ':timings' => $timings,
                        ':layout' => $layoutType,
                        ':fancharge' => $fanCharge,
                        ':ps_canopy' => $pShopCanopy,
                        ':ps_t1' => $pShopT1,
                        ':ps_t2' => $pShopT2,
                        ':pf_canopy' => $pFoodCanopy,
                        ':pf_t2' => $pFoodT2,
                        ':p_promo' => $pPromo,
                        ':daily_price' => $dailyPrice,
                        ':tot_stalls' => $totalStalls,
                        ':avail_stalls' => $totalStalls,
                        ':desc' => $desc,
                        ':img' => $img
                    ]);

                    $newId = $pdo->lastInsertId();
                    sync_event_stalls($pdo, $newId);
                    $imported++;
                    $rowDetails[] = [
                        'status' => 'Imported',
                        'venue' => $venue,
                        'city' => $city,
                        'date' => $startDate,
                        'price' => formatPrice($dailyPrice)
                    ];
                }
            }

            $importResults = [
                'imported' => $imported,
                'updated' => $updated,
                'rows' => $rowDetails
            ];
            setFlash('success', "Import completed successfully! {$imported} new exhibitions created, {$updated} existing records synchronized.");
        }
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Excel &amp; CSV Exhibition Listing Importer</h3>
    <p style="font-size: 0.85rem; color: #6b7280;">
      Batch import event dates, venues, stall rates, footfall, and timings directly from Excel or CSV files.
    </p>
  </div>
</div>

<?php if (!empty($errors)): ?>
  <div style="background: #fee2e2; border: 1px solid #f87171; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
    <strong>Please review the following errors:</strong>
    <ul style="margin: 0.5rem 0 0 1.25rem;">
      <?php foreach ($errors as $e): ?>
        <li><?= e($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if ($importResults): ?>
  <div class="admin-card" style="border-left: 4px solid #10b981; margin-bottom: 2rem;">
    <div class="admin-card-header">
      <h4 style="font-weight: 700; color: #065f46;">
        Import Summary: <?= $importResults['imported'] ?> New Created • <?= $importResults['updated'] ?> Updated
      </h4>
    </div>
    <div class="admin-card-body" style="padding: 1rem;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Status</th>
            <th>Venue</th>
            <th>City</th>
            <th>Date</th>
            <th>Base Price</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($importResults['rows'] as $r): ?>
            <tr>
              <td>
                <span class="badge <?= $r['status'] === 'Imported' ? 'badge-success' : 'badge-info' ?>"><?= $r['status'] ?></span>
              </td>
              <td><strong><?= e($r['venue']) ?></strong></td>
              <td><?= e($r['city']) ?></td>
              <td><?= e($r['date']) ?></td>
              <td><?= e($r['price']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
  <!-- Upload Card -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h4 style="font-size: 1rem; font-weight: 700;">Upload or Paste Listing Data</h4>
    </div>
    <div class="admin-card-body">
      <form method="POST" action="import.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />

        <!-- File Upload -->
        <div style="margin-bottom: 1.5rem;">
          <label style="display: block; font-size: 0.88rem; font-weight: 600; margin-bottom: 0.4rem;">Upload CSV File:</label>
          <input type="file" name="csv_file" accept=".csv, text/csv, .txt" 
                 style="width: 100%; padding: 0.65rem; border: 1.5px dashed #d1d5db; border-radius: 8px;" />
          <p style="font-size: 0.78rem; color: #6b7280; margin-top: 0.35rem;">
            Supports Excel CSV exports (.csv) formatted with standard exhibition columns.
          </p>
        </div>

        <div style="text-align: center; margin: 1rem 0; font-size: 0.85rem; color: #888;">— OR PASTE CSV TEXT DIRECTLY —</div>

        <!-- Text Paste -->
        <div style="margin-bottom: 1.5rem;">
          <label style="display: block; font-size: 0.88rem; font-weight: 600; margin-bottom: 0.4rem;">Paste CSV Content:</label>
          <textarea name="csv_text" rows="8" placeholder="2026-10-13,Lucerna Towers,Noida,3k employees,Corporate,5000,3500,4000,5000,4500,10k onwards,20+,Middle Class,10-8 PM,Outdoors,First come first serve,300 RS"
                    style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px; font-family: monospace; font-size: 0.82rem;"></textarea>
        </div>

        <button type="submit" class="admin-btn admin-btn-gold" style="padding: 0.75rem 1.75rem;">
          <span>🚀 Process &amp; Import Listings</span>
        </button>
      </form>
    </div>
  </div>

  <!-- Format Guide Card -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h4 style="font-size: 1rem; font-weight: 700;">Column Format Reference</h4>
    </div>
    <div class="admin-card-body" style="font-size: 0.82rem; color: #4b5563; line-height: 1.6;">
      <p style="margin-bottom: 0.75rem;">The importer automatically parses the standard Expo Tree Excel layout:</p>
      <ol style="margin-left: 1.25rem; margin-bottom: 1rem;">
        <li><strong>DATE</strong> (e.g. 2026-10-13 or 18-19-20 Oct)</li>
        <li><strong>VENUE</strong> (e.g. Lucerna Towers)</li>
        <li><strong>CITY</strong> (e.g. Noida, Gurugram)</li>
        <li><strong>FOOTFALL</strong> (e.g. 3k employees)</li>
        <li><strong>EVENT TYPE</strong> (Corporate, Society, Mall)</li>
        <li><strong>Shopping Canopy Price</strong></li>
        <li><strong>Shopping 1 Table Price</strong></li>
        <li><strong>Shopping 2 Tables Price</strong></li>
        <li><strong>Food Canopy Price</strong></li>
        <li><strong>Food 2 Tables Price</strong></li>
        <li><strong>Promotional Rate</strong></li>
        <li><strong>Total Stalls Count</strong></li>
        <li><strong>GENTRY</strong> (Middle Class, Premium)</li>
        <li><strong>TIMINGS</strong> (e.g. 10-8 PM)</li>
        <li><strong>LOCATION</strong> (Indoors, Outdoors)</li>
        <li><strong>Layout / Fcfs</strong></li>
        <li><strong>Fan Charge</strong> (e.g. 300 RS)</li>
      </ol>
      <div style="background: #f9fafb; padding: 0.75rem; border-radius: 6px; border: 1px solid #e5e7eb;">
        ✓ Duplicate prevention: existing events on the same date and venue are synchronized without creating duplicate entries.
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
