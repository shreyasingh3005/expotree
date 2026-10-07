<?php
/**
 * Admin Bookings Management & CSV Export
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$adminTitle = 'Exhibitor Stall Bookings';
$activeMenu = 'bookings';

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $stmtExp = $pdo->query("
        SELECT b.booking_number, b.customer_name, b.business_name, b.mobile, b.whatsapp, b.email,
               e.title as event_title, e.city as event_city, b.stall_type, b.stalls_count,
               b.start_date, b.end_date, b.days_count, b.price_per_day, b.total_amount,
               b.notes, b.status, b.created_at
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        ORDER BY b.created_at DESC
    ");
    $allBookings = $stmtExp->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=ExpoTree_Bookings_' . date('Ymd_His') . '.csv');

    $out = fopen('php://output', 'w');
    if (!empty($allBookings)) {
        // Headers
        fputcsv($out, array_keys($allBookings[0]));
        foreach ($allBookings as $row) {
            fputcsv($out, $row);
        }
    }
    fclose($out);
    exit;
}

// Handle Status Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $newStatus = sanitize($_POST['new_status'] ?? 'confirmed');

        $bStmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
        $bStmt->execute([$bookingId]);
        $bk = $bStmt->fetch();

        if ($bk) {
            $oldStatus = $bk['status'];
            // If cancelling a non-cancelled booking, restore stalls to event inventory
            if ($oldStatus !== 'cancelled' && $newStatus === 'cancelled') {
                restoreStallBooking($pdo, $bk['event_id'], (int)$bk['stalls_count']);
            }
            // If reactivating a cancelled booking, re-deduct stalls
            elseif ($oldStatus === 'cancelled' && ($newStatus === 'confirmed' || $newStatus === 'pending')) {
                recordStallBooking($pdo, $bk['event_id'], (int)$bk['stalls_count']);
            }

            $upd = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            $upd->execute([$newStatus, $bookingId]);
            setFlash('success', "Booking #{$bk['booking_number']} status updated to {$newStatus}.");
        }
        header('Location: ' . BASE_URL . '/admin/bookings.php');
        exit;
    }
}

// Filters & Query
$statusFilter = sanitize($_GET['status'] ?? 'all');
$search = sanitize($_GET['q'] ?? '');

$sql = "
    SELECT b.*, e.title as event_title, e.venue as event_venue, e.city as event_city
    FROM bookings b
    JOIN events e ON b.event_id = e.id
    WHERE 1=1
";
$params = [];

if ($statusFilter !== 'all' && !empty($statusFilter)) {
    $sql .= " AND b.status = :status";
    $params[':status'] = $statusFilter;
}
if (!empty($search)) {
    $sql .= " AND (b.booking_number LIKE :q OR b.customer_name LIKE :q OR b.business_name LIKE :q OR b.mobile LIKE :q OR e.title LIKE :q)";
    $params[':q'] = "%$search%";
}

$sql .= " ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Title & Action Bar -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Exhibitor Stall Bookings (<?= count($bookings) ?>)</h3>
    <p style="font-size: 0.85rem; color: #6b7280;">Review reservation requests, manage confirmed exhibitors, and export Excel reports.</p>
  </div>
  <a href="bookings.php?export=csv" class="admin-btn admin-btn-gold">
    <span>📥 Export to CSV / Excel</span>
  </a>
</div>

<!-- Filters Card -->
<div class="admin-card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
  <form method="GET" action="bookings.php" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 0.75rem; align-items: center;">
    <div>
      <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search exhibitor name, phone, brand or ref ID..." 
             style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>

    <div>
      <select name="status" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem; background: #fff;">
        <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
        <option value="confirmed" <?= $statusFilter === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
      </select>
    </div>

    <div style="display: flex; gap: 0.5rem;">
      <button type="submit" class="admin-btn admin-btn-burgundy">Filter</button>
      <a href="bookings.php" class="admin-btn admin-btn-outline">Reset</a>
    </div>
  </form>
</div>

<!-- Bookings Table -->
<div class="admin-card">
  <div style="overflow-x: auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Ref / Date</th>
          <th>Exhibitor Details</th>
          <th>Exhibition Event</th>
          <th>Stall Configuration</th>
          <th>Dates &amp; Duration</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($bookings)): ?>
          <tr><td colspan="8" style="text-align: center; color: #888; padding: 2.5rem;">No bookings found.</td></tr>
        <?php else: ?>
          <?php foreach ($bookings as $b): ?>
            <tr>
              <td>
                <strong style="font-family: monospace; font-size: 0.85rem; color: var(--admin-burgundy);"><?= e($b['booking_number']) ?></strong><br>
                <span style="font-size: 0.72rem; color: #888;"><?= date('d M Y, h:i A', strtotime($b['created_at'])) ?></span>
              </td>
              <td>
                <div style="font-weight: 700; color: #111827;"><?= e($b['customer_name']) ?></div>
                <?php if (!empty($b['business_name'])): ?>
                  <div style="font-size: 0.8rem; color: #4b5563;">🏢 <?= e($b['business_name']) ?></div>
                <?php endif; ?>
                <div style="font-size: 0.78rem; color: #2563eb;">
                  <a href="tel:<?= e($b['mobile']) ?>" style="color: inherit; text-decoration: none;">📞 <?= e($b['mobile']) ?></a>
                  <a href="https://wa.me/91<?= e($b['mobile']) ?>" target="_blank" style="margin-left: 0.4rem; color: #16a34a; text-decoration: none; font-weight: 600;">WA</a>
                </div>
              </td>
              <td>
                <div style="font-weight: 600; color: #111827; font-size: 0.88rem;"><?= e($b['event_title']) ?></div>
                <div style="font-size: 0.75rem; color: #6b7280;">📍 <?= e($b['event_venue']) ?>, <?= e($b['event_city']) ?></div>
              </td>
              <td>
                <div style="font-weight: 600; font-size: 0.82rem; color: #1f2937;"><?= e($b['stall_type']) ?></div>
                <div style="font-size: 0.75rem; color: #6b7280;">Category: <?= e($b['category_name']) ?></div>
                <div style="font-size: 0.75rem; color: #059669; font-weight: 700;"><?= $b['stalls_count'] ?> Stall<?= $b['stalls_count'] > 1 ? 's' : '' ?></div>
              </td>
              <td>
                <div style="font-size: 0.82rem; font-weight: 600;"><?= date('d M', strtotime($b['start_date'])) ?> – <?= date('d M Y', strtotime($b['end_date'])) ?></div>
                <div style="font-size: 0.75rem; color: #6b7280;"><?= $b['days_count'] ?> Day(s)</div>
              </td>
              <td>
                <div style="font-size: 0.95rem; font-weight: 800; color: #111827;">₹<?= number_format($b['total_amount'], 0) ?></div>
                <?php if ($b['addons_total'] > 0): ?>
                  <div style="font-size: 0.7rem; color: #888;">+₹<?= number_format($b['addons_total'], 0) ?> addons</div>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge <?= $b['status'] === 'confirmed' ? 'badge-success' : ($b['status'] === 'pending' ? 'badge-warning' : ($b['status'] === 'cancelled' ? 'badge-danger' : 'badge-info')) ?>">
                  <?= ucfirst($b['status']) ?>
                </span>
              </td>
              <td>
                <form method="POST" action="bookings.php" style="display: flex; gap: 0.35rem; align-items: center;">
                  <input type="hidden" name="action" value="update_status" />
                  <input type="hidden" name="booking_id" value="<?= $b['id'] ?>" />
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
                  
                  <select name="new_status" style="padding: 0.3rem 0.4rem; font-size: 0.75rem; border: 1px solid #d1d5db; border-radius: 4px; background: #fff;">
                    <option value="confirmed" <?= $b['status'] === 'confirmed' ? 'selected' : '' ?>>Confirm</option>
                    <option value="pending" <?= $b['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= $b['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= $b['status'] === 'cancelled' ? 'selected' : '' ?>>Cancel</option>
                  </select>
                  <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.25rem 0.45rem; font-size: 0.75rem;">Save</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
