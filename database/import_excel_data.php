<?php
/**
 * Excel / CSV Data Importer
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

echo "=== Processing Excel Listing Import ===\n";

$csvData = <<<'EOD'
2026-10-13,Lucerna Towers ,Noida ,3k employees ,Corporate ,5000,3500,4000,5000,4500,10k onwards ,20+,Middle Class ,10-8 PM,Outdoors ,First come first serve ,300 RS 
2026-10-13,Smartworks ,Noida ,3k employees ,Corporate ,NA ,6000,7000,NA,NA,10K onwards ,10,Middle Class ,10-8 PM ,Indoors ,First come first serve ,-
2026-10-14,Smartworks ,Noida ,3k employees ,Corporate ,NA ,6000,7000,NA,NA,10K onwards ,10,Middle Class ,10-8 PM ,Indoors ,First come first serve ,-
2026-10-17,Bestech Park View Spa Next ,Gurugram ,570 families ,Society ,4000,3000,3500,6000,5000,10k onwards ,30+,Middle Class ,4-10 PM ,Outdoors ,First come first serve ,300 RS 
2026-10-17,M3M Skyheights ,Gurugram ,1000 families ,Society ,8000,5000,6000,7000,5000,10k onwards ,20+,Premium Class,4-10 PM ,Outdoors ,First come first serve ,300 RS 
18-19-20 October ,Ireo Victory Valley,Gurugram ,800 families ,Society ,6000,4000,5000,7000,6000,10k onwards ,25+,Premium Class,4-10 PM ,Outdoors ,First come first serve ,300 RS 
19-20 Oct ,Ireo city central ,Gurugram ,1000+,Mall,6000,NA,NA,6000,NA,10K onwards ,10,Premium Class ,10-10 PM ,outdoors ,We have layout here ,-
2026-10-27,Stellar 1425 ,Noida ,5k employees ,Corporate ,NA ,5000,6000,NA,NA,10K onwards ,10+,Middle Class ,10-8 PM ,Indoors ,First come first serve ,-
27-28 Oct ,Stellar It Park ,Noida ,12000 employees ,Corporate ,6000 per day ,NA,NA,5000 per day ,4000 per day ,20k onwards ,20+,Middle Class ,10-8 PM ,Outdoors ,First come first serve ,300 RS 
27-28 Oct ,Ireo city central ,Gurugram ,1000+,Mall,6000,NA,NA,6000,NA,10K onwards ,10,Premium Class ,10-10 PM ,outdoors ,We have layout here ,-
2026-10-28,Stellar 1423,Noida ,5k employees ,Corporate ,NA ,5000,6000,NA,NA,10K onwards ,10+,Middle Class ,10-8 PM ,Indoors ,First come first serve ,-
2026-10-30,M3M Skyheights ,Gurugram ,1000 families ,Society ,8000,5000,6000,7000,5000,10k onwards ,20+,Premium Class,4-10 PM ,Outdoors ,First come first serve ,300 RS 
2026-11-01,The Nile ,Gurugram ,450 families ,Society ,6000,4000,5000,7000,5000,10k onwards ,20+,Middle Class ,4-10 PM ,Outdoors ,First come first serve ,-
2026-11-03,Stellar 1425 ,Noida ,5k employees ,Corporate ,NA ,5000,6000,NA,NA,10K onwards ,10+,Middle Class ,10-8 PM ,Indoors ,First come first serve ,-
2026-11-04,Stellar 1423,Noida ,5k employees ,Corporate ,NA ,5000,6000,NA,NA,10K onwards ,10+,Middle Class ,10-8 PM ,Indoors ,First come first serve ,-
2026-11-04,Urbtech Trade Centre ,Noida ,3000 employees ,Corporate ,5000,NA,NA,5000,4500,10k onwards ,20+,Middle Class ,10-8 PM,Outdoors ,First come first serve ,
2026-11-04,Central Park Flower Valley ,Gurugram ,2000 families ,Society ,10000,NA ,NA ,NA,NA ,10k onwards ,30+,Premium Class,4-10 PM ,Outdoors ,First come first serve ,-
3-4 November ,Lucerna Towers ,Noida ,15000 employees ,Corporate ,5000,NA,NA,5000,4500,10k onwards ,20+,Middle Class ,10-8 PM,Outdoors ,First come first serve ,
EOD;

$lines = explode("\n", trim($csvData));
$importedCount = 0;
$skippedCount = 0;

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;
    $cols = array_map('trim', explode(',', $line));
    if (count($cols) < 5) continue;

    $dateRaw = $cols[0];
    $venue = $cols[1];
    $city = $cols[2];
    $footfall = $cols[3];
    $eventType = $cols[4];
    $priceShopCanopyRaw = $cols[5] ?? 'NA';
    $priceShopT1Raw = $cols[6] ?? 'NA';
    $priceShopT2Raw = $cols[7] ?? 'NA';
    $priceFoodCanopyRaw = $cols[8] ?? 'NA';
    $priceFoodT2Raw = $cols[9] ?? 'NA';
    $pricePromoRaw = $cols[10] ?? '10k onwards';
    $totalStallsRaw = $cols[11] ?? '20';
    $gentry = $cols[12] ?? 'Middle Class';
    $timings = $cols[13] ?? '10-8 PM';
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
    } elseif (stripos($dateRaw, '18-19-20 October') !== false || stripos($dateRaw, '18-19-20 Oct') !== false) {
        $startDate = '2026-10-18';
        $endDate = '2026-10-20';
        $dateDisplay = '18-19-20 Oct 2026';
    } elseif (stripos($dateRaw, '19-20 Oct') !== false) {
        $startDate = '2026-10-19';
        $endDate = '2026-10-20';
        $dateDisplay = '19-20 Oct 2026';
    } elseif (stripos($dateRaw, '27-28 Oct') !== false) {
        $startDate = '2026-10-27';
        $endDate = '2026-10-28';
        $dateDisplay = '27-28 Oct 2026';
    } elseif (stripos($dateRaw, '3-4 November') !== false || stripos($dateRaw, '3-4 Nov') !== false) {
        $startDate = '2026-11-03';
        $endDate = '2026-11-04';
        $dateDisplay = '03-04 Nov 2026';
    } else {
        // Fallback
        $startDate = '2026-10-15';
        $endDate = '2026-10-15';
    }

    // Parse pricing numbers
    $cleanNum = function($v) {
        if (strtoupper($v) === 'NA' || empty($v) || $v === '-') return null;
        $n = preg_replace('/[^0-9.]/', '', $v);
        return is_numeric($n) ? (float)$n : null;
    };

    $priceShopCanopy = $cleanNum($priceShopCanopyRaw);
    $priceShopT1 = $cleanNum($priceShopT1Raw);
    $priceShopT2 = $cleanNum($priceShopT2Raw);
    $priceFoodCanopy = $cleanNum($priceFoodCanopyRaw);
    $priceFoodT2 = $cleanNum($priceFoodT2Raw);
    $pricePromo = trim($pricePromoRaw);
    $totalStalls = max(10, (int)preg_replace('/[^0-9]/', '', $totalStallsRaw));

    // Determine baseline daily price
    $dailyPrice = 5000;
    if ($priceShopCanopy) $dailyPrice = $priceShopCanopy;
    elseif ($priceShopT1) $dailyPrice = $priceShopT1;

    // Check if event already exists with same venue and start_date
    $chkStmt = $pdo->prepare("SELECT id FROM events WHERE venue LIKE ? AND start_date = ?");
    $chkStmt->execute(['%' . $venue . '%', $startDate]);
    $existingId = $chkStmt->fetchColumn();

    if ($existingId) {
        // Update any missing pricing details on existing record
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
        $upd->execute([$priceShopCanopy, $priceShopT1, $priceShopT2, $priceFoodCanopy, $priceFoodT2, $pricePromo, $existingId]);

        // Sync stalls
        sync_event_stalls($pdo, $existingId);
        $skippedCount++;
        echo "[-] Event already in DB: {$venue} on {$startDate} (#{$existingId}) -> Stalls & pricing synchronized.\n";
    } else {
        // Generate Title and Slug
        $title = "{$venue} {$eventType} Lifestyle Exhibition";
        $slug = slugify("{$title}-{$startDate}");

        // High quality photo based on event type
        if (stripos($eventType, 'Corporate') !== false) {
            $img = 'https://images.unsplash.com/photo-1526178613552-2b45c6c302f0?auto=format&fit=crop&w=800&q=80';
        } elseif (stripos($eventType, 'Society') !== false) {
            $img = 'https://images.unsplash.com/photo-1577900232427-18219b9166a0?auto=format&fit=crop&w=800&q=80';
        } else {
            $img = 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=800&q=80';
        }

        $insSql = "INSERT INTO events (
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
        )";

        $desc = "Curated {$eventType} exhibition hosted at {$venue}, {$city}. Direct exposure to {$footfall} affluent attendees with dedicated setups for Jewellery, Apparel, Home Decor, Bakery & Food counters.";

        $insStmt = $pdo->prepare($insSql);
        $insStmt->execute([
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
            ':ps_canopy' => $priceShopCanopy,
            ':ps_t1' => $priceShopT1,
            ':ps_t2' => $priceShopT2,
            ':pf_canopy' => $priceFoodCanopy,
            ':pf_t2' => $priceFoodT2,
            ':p_promo' => $pricePromo,
            ':daily_price' => $dailyPrice,
            ':tot_stalls' => $totalStalls,
            ':avail_stalls' => $totalStalls,
            ':desc' => $desc,
            ':img' => $img
        ]);

        $newId = $pdo->lastInsertId();
        sync_event_stalls($pdo, $newId);
        $importedCount++;
        echo "[+] Successfully imported: {$title} (#{$newId}) for {$startDate}\n";
    }
}

// Also ensure all existing events have synchronized stalls
$allEventIds = $pdo->query("SELECT id FROM events")->fetchAll(PDO::FETCH_COLUMN);
foreach ($allEventIds as $eid) {
    sync_event_stalls($pdo, $eid);
}

echo "=== Excel Import Summary ===\n";
echo "Imported: {$importedCount} new events\n";
echo "Synchronized/Existing: {$skippedCount} events\n";
echo "Total events in DB: " . count($allEventIds) . "\n";
$totalStallsInDb = $pdo->query("SELECT COUNT(*) FROM event_stalls")->fetchColumn();
echo "Total Granular Stalls Configured in DB: {$totalStallsInDb}\n";
