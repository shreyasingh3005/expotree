<?php
/**
 * Admin Dashboard
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$adminTitle = 'Dashboard Overview';
$activeMenu = 'dashboard';

// Fetch Live Metrics
$totalEvents = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$activeEvents = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE status IN ('published', 'active')")->fetchColumn();
$pendingEvents = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE status = 'pending'")->fetchColumn();

$stallsMetrics = $pdo->query("
    SELECT 
        COALESCE(SUM(total_stalls), 0) as total,
        COALESCE(SUM(booked_stalls), 0) as booked,
        COALESCE(SUM(available_stalls), 0) as available
    FROM events
")->fetch();

$totalBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pendingBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$confirmedBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn();
$totalRevenue = (float)$pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE status IN ('confirmed', 'completed')")->fetchColumn();
$shopperPasses = (int)$pdo->query("SELECT COUNT(*) FROM shopper_passes")->fetchColumn();

// Organizer submissions pending review
$pendingSubmissions = $pdo->query("
    SELECT * FROM events WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5
")->fetchAll();

// Recent Bookings (Latest 6)
$recentBookings = $pdo->query("
    SELECT b.*, e.title as event_title, e.city as event_city
    FROM bookings b
    JOIN events e ON b.event_id = e.id
    ORDER BY b.created_at DESC
    LIMIT 6
")->fetchAll();

// Upcoming Events (Next 5)
$upcomingEvents = $pdo->query("
    SELECT * FROM events 
    WHERE status IN ('published', 'active')
    ORDER BY start_date ASC
    LIMIT 5
")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Metric Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
  
  <!-- Total Events -->
  <div class="admin-card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid #D4AF37;">
    <div style="font-size: 0.8rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Total Exhibitions</div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #111827; margin: 0.35rem 0;"><?= $totalEvents ?></div>
    <div style="font-size: 0.78rem; color: #059669; font-weight: 600;">✓ <?= $activeEvents ?> Active • <?= $pendingEvents ?> Pending</div>
  </div>

  <!-- Pending Approvals -->
  <div class="admin-card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid <?= $pendingEvents > 0 ? '#f59e0b' : '#9ca3af' ?>;">
    <div style="font-size: 0.8rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Organizer Submissions</div>
    <div style="font-size: 1.85rem; font-weight: 800; color: <?= $pendingEvents > 0 ? '#d97706' : '#111827' ?>; margin: 0.35rem 0;"><?= $pendingEvents ?></div>
    <div style="font-size: 0.78rem;">
      <?php if ($pendingEvents > 0): ?>
        <a href="<?= BASE_URL ?>/admin/submissions.php" style="color: #d97706; font-weight: 700; text-decoration: none;">⚠️ Review <?= $pendingEvents ?> Pending →</a>
      <?php else: ?>
        <span style="color: #059669; font-weight: 600;">✓ All reviews cleared</span>
      <?php endif; ?>
    </div>
  </div>

  <!-- Total Stalls Capacity -->
  <div class="admin-card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid #3b82f6;">
    <div style="font-size: 0.8rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Stalls Inventory</div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #111827; margin: 0.35rem 0;"><?= $stallsMetrics['total'] ?></div>
    <div style="font-size: 0.78rem; color: #2563eb;">
      <?= $stallsMetrics['booked'] ?> Booked • <strong><?= $stallsMetrics['available'] ?> Available</strong>
    </div>
  </div>

  <!-- Total Exhibitor Bookings -->
  <div class="admin-card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid #10b981;">
    <div style="font-size: 0.8rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Exhibitor Bookings</div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #111827; margin: 0.35rem 0;"><?= $totalBookings ?></div>
    <div style="font-size: 0.78rem; color: #059669; font-weight: 600;">
      ✓ <?= $confirmedBookings ?> Confirmed <?= $pendingBookings > 0 ? "• <span style='color:#d97706;'>{$pendingBookings} Pending</span>" : '' ?>
    </div>
  </div>

  <!-- Total Revenue -->
  <div class="admin-card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid #8b5cf6;">
    <div style="font-size: 0.8rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Booking Value</div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #111827; margin: 0.35rem 0;">₹<?= number_format($totalRevenue, 0) ?></div>
    <div style="font-size: 0.78rem; color: #6b7280;">From <?= $confirmedBookings ?> verified bookings</div>
  </div>

  <!-- VIP Shopper Passes -->
  <div class="admin-card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid #ec4899;">
    <div style="font-size: 0.8rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Shopper VIP Passes</div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #111827; margin: 0.35rem 0;"><?= $shopperPasses ?></div>
    <div style="font-size: 0.78rem; color: #db2777;">Registered attendees</div>
  </div>

</div>

<!-- Pending Submissions Attention Alert -->
<?php if (!empty($pendingSubmissions)): ?>
  <div class="admin-card" style="border: 2px solid #f59e0b; background: #fffbeb; margin-bottom: 2rem; padding: 1.25rem 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 1.75rem;">⚠️</span>
        <div>
          <h4 style="font-size: 1.05rem; font-weight: 700; color: #92400e; margin-bottom: 0.2rem;">
            <?= count($pendingSubmissions) ?> Organizer Submission(s) Awaiting Review &amp; Approval
          </h4>
          <p style="font-size: 0.85rem; color: #b45309;">
            Review society venues, verify layout and pricing before publishing to the public calendar.
          </p>
        </div>
      </div>
      <a href="<?= BASE_URL ?>/admin/submissions.php" class="admin-btn admin-btn-gold">
        <span>📋 Open Review Queue (<?= $pendingEvents ?>) →</span>
      </a>
    </div>
  </div>
<?php endif; ?>

<!-- Quick Action Shortcuts -->
<div style="display: flex; gap: 0.75rem; margin-bottom: 2rem; flex-wrap: wrap;">
  <a href="<?= BASE_URL ?>/admin/event-add.php" class="admin-btn admin-btn-gold">
    <span>➕ Add New Exhibition</span>
  </a>
  <a href="<?= BASE_URL ?>/admin/submissions.php" class="admin-btn admin-btn-outline">
    <span>📋 Organizer Submissions <?= $pendingEvents > 0 ? "({$pendingEvents})" : '' ?></span>
  </a>
  <a href="<?= BASE_URL ?>/admin/events.php" class="admin-btn admin-btn-outline">
    <span>🎪 Manage All Events (<?= $totalEvents ?>)</span>
  </a>
  <a href="<?= BASE_URL ?>/admin/bookings.php" class="admin-btn admin-btn-outline">
    <span>🎟️ View All Bookings (<?= $totalBookings ?>)</span>
  </a>
  <a href="<?= BASE_URL ?>/admin/import.php" class="admin-btn admin-btn-outline">
    <span>📥 Excel / CSV Import</span>
  </a>
  <a href="<?= BASE_URL ?>/admin/settings.php" class="admin-btn admin-btn-outline">
    <span>⚙️ Website Settings</span>
  </a>
</div>

<!-- Two Columns Layout: Recent Bookings & Upcoming Events -->
<div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 1.5rem; align-items: start;">
  
  <!-- Left: Recent Exhibitor Bookings -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h3 style="font-size: 1.05rem; font-weight: 700; color: #111827;">Recent Stall Bookings</h3>
      <a href="<?= BASE_URL ?>/admin/bookings.php" style="font-size: 0.85rem; color: #2563eb; text-decoration: none;">View All →</a>
    </div>
    
    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Booking Ref</th>
            <th>Exhibitor</th>
            <th>Event &amp; Stall</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentBookings)): ?>
            <tr><td colspan="5" style="text-align: center; color: #888; padding: 2rem;">No bookings recorded yet.</td></tr>
          <?php else: ?>
            <?php foreach ($recentBookings as $rb): ?>
              <tr>
                <td>
                  <strong style="font-family: monospace; font-size: 0.82rem; color: var(--admin-burgundy);"><?= e($rb['booking_number']) ?></strong><br>
                  <span style="font-size: 0.72rem; color: #888;"><?= date('d M, h:i A', strtotime($rb['created_at'])) ?></span>
                </td>
                <td>
                  <strong><?= e($rb['customer_name']) ?></strong><br>
                  <span style="font-size: 0.78rem; color: #666;"><?= e($rb['mobile']) ?></span>
                </td>
                <td>
                  <div style="font-size: 0.85rem; font-weight: 600; color: #1f2937;"><?= e($rb['event_title']) ?></div>
                  <span style="font-size: 0.75rem; color: #6b7280;"><?= e($rb['stall_type']) ?> (<?= $rb['stalls_count'] ?> stall)</span>
                </td>
                <td>
                  <strong style="color: #111827;">₹<?= number_format($rb['total_amount'], 0) ?></strong>
                </td>
                <td>
                  <span class="badge <?= $rb['status'] === 'confirmed' ? 'badge-success' : ($rb['status'] === 'pending' ? 'badge-warning' : 'badge-danger') ?>">
                    <?= ucfirst($rb['status']) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Right: Upcoming Scheduled Exhibitions -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h3 style="font-size: 1.05rem; font-weight: 700; color: #111827;">Active Exhibitions</h3>
      <a href="<?= BASE_URL ?>/admin/events.php" style="font-size: 0.85rem; color: #2563eb; text-decoration: none;">All Events →</a>
    </div>

    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Event</th>
            <th>Stalls</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($upcomingEvents)): ?>
            <tr><td colspan="3" style="text-align: center; color: #888; padding: 2rem;">No upcoming events.</td></tr>
          <?php else: ?>
            <?php foreach ($upcomingEvents as $uev): ?>
              <tr>
                <td>
                  <div style="font-weight: 600; color: #111827;"><?= e($uev['title']) ?></div>
                  <div style="font-size: 0.78rem; color: #6b7280;">📍 <?= e($uev['venue']) ?>, <?= e($uev['city']) ?></div>
                  <div style="font-size: 0.75rem; color: #9ca3af;">📅 <?= e($uev['date_display'] ?? formatDateRange($uev['start_date'], $uev['end_date'])) ?></div>
                </td>
                <td>
                  <span style="font-size: 0.82rem; font-weight: 700; color: <?= $uev['available_stalls'] > 0 ? '#059669' : '#dc2626' ?>;">
                    <?= $uev['available_stalls'] ?> / <?= $uev['total_stalls'] ?>
                  </span>
                </td>
                <td>
                  <a href="<?= BASE_URL ?>/admin/event-edit.php?id=<?= $uev['id'] ?>" class="admin-btn admin-btn-outline admin-btn-sm" title="Edit">
                    ✏️ Edit
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
