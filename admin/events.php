<?php
/**
 * Admin Events Listing & Management
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$adminTitle = 'Exhibition Events Management';
$activeMenu = 'events';

// Filters
$cityFilter = sanitize($_GET['city'] ?? 'all');
$statusFilter = sanitize($_GET['status'] ?? 'all');
$search = sanitize($_GET['q'] ?? '');

$sql = "SELECT * FROM events WHERE 1=1";
$params = [];

if ($cityFilter !== 'all' && !empty($cityFilter)) {
    $sql .= " AND LOWER(city) LIKE :city";
    $params[':city'] = '%' . strtolower($cityFilter) . '%';
}
if ($statusFilter !== 'all' && !empty($statusFilter)) {
    $sql .= " AND status = :status";
    $params[':status'] = $statusFilter;
}
if (!empty($search)) {
    $sql .= " AND (title LIKE :q OR venue LIKE :q OR city LIKE :q OR event_type LIKE :q)";
    $params[':q'] = "%$search%";
}

$sql .= " ORDER BY start_date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Action Bar -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827;">All Exhibitions (<?= count($events) ?>)</h3>
    <p style="font-size: 0.85rem; color: #6b7280;">Manage event details, stall capacity, pricing &amp; live publishing status.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/event-add.php" class="admin-btn admin-btn-gold">
    <span>➕ Add New Exhibition</span>
  </a>
</div>

<!-- Filter Bar Card -->
<div class="admin-card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
  <form method="GET" action="events.php" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 0.75rem; align-items: center;">
    <div>
      <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search venue, title, or city..." 
             style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    </div>

    <div>
      <select name="city" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem; background: #fff;">
        <option value="all" <?= $cityFilter === 'all' ? 'selected' : '' ?>>All Cities</option>
        <option value="gurugram" <?= $cityFilter === 'gurugram' ? 'selected' : '' ?>>Gurugram</option>
        <option value="noida" <?= $cityFilter === 'noida' ? 'selected' : '' ?>>Noida</option>
        <option value="delhi" <?= $cityFilter === 'delhi' ? 'selected' : '' ?>>Delhi</option>
        <option value="faridabad" <?= $cityFilter === 'faridabad' ? 'selected' : '' ?>>Faridabad</option>
        <option value="ghaziabad" <?= $cityFilter === 'ghaziabad' ? 'selected' : '' ?>>Ghaziabad</option>
        <option value="greater noida" <?= $cityFilter === 'greater noida' ? 'selected' : '' ?>>Greater Noida</option>
      </select>
    </div>

    <div>
      <select name="status" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem; background: #fff;">
        <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
        <option value="published" <?= $statusFilter === 'published' ? 'selected' : '' ?>>Published</option>
        <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending Review</option>
        <option value="draft" <?= $statusFilter === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="unpublished" <?= $statusFilter === 'unpublished' ? 'selected' : '' ?>>Unpublished</option>
        <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
    </div>

    <div style="display: flex; gap: 0.5rem;">
      <button type="submit" class="admin-btn admin-btn-burgundy">Filter</button>
      <a href="events.php" class="admin-btn admin-btn-outline" title="Clear Filters">Reset</a>
    </div>
  </form>
</div>

<!-- Events Table Card -->
<div class="admin-card">
  <div style="overflow-x: auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Exhibition &amp; Venue</th>
          <th>City &amp; Type</th>
          <th>Dates &amp; Timings</th>
          <th>Stalls Capacity</th>
          <th>Pricing Matrix</th>
          <th>Status</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($events)): ?>
          <tr><td colspan="8" style="text-align: center; color: #888; padding: 2.5rem;">No exhibitions match your search criteria.</td></tr>
        <?php else: ?>
          <?php foreach ($events as $ev): ?>
            <tr>
              <td>#<?= $ev['id'] ?></td>
              <td>
                <div style="font-weight: 700; color: #111827; font-size: 0.95rem;"><?= e($ev['title']) ?></div>
                <div style="font-size: 0.8rem; color: #4b5563;">📍 <?= e($ev['venue']) ?></div>
                <div style="font-size: 0.75rem; color: #9ca3af;">👥 <?= e($ev['footfall']) ?> • <?= e($ev['gentry']) ?></div>
              </td>
              <td>
                <span class="badge badge-info"><?= e($ev['city']) ?></span>
                <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.2rem;"><?= e($ev['event_type']) ?></div>
              </td>
              <td>
                <div style="font-weight: 600; font-size: 0.85rem; color: #111827;"><?= e($ev['date_display'] ?? formatDateRange($ev['start_date'], $ev['end_date'])) ?></div>
                <div style="font-size: 0.75rem; color: #6b7280;">⏰ <?= e($ev['timings']) ?></div>
              </td>
              <td>
                <div style="font-size: 0.82rem; margin-bottom: 0.25rem;">
                  <strong><?= $ev['available_stalls'] ?></strong> avail / <strong><?= $ev['total_stalls'] ?></strong> total
                </div>
                <div style="width: 100px; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;">
                  <?php $pct = $ev['total_stalls'] > 0 ? ($ev['booked_stalls'] / $ev['total_stalls']) * 100 : 0; ?>
                  <div style="width: <?= min(100, $pct) ?>%; height: 100%; background: #D4AF37;"></div>
                </div>
                <div style="font-size: 0.7rem; color: #888; margin-top: 0.15rem;"><?= $ev['booked_stalls'] ?> booked</div>
              </td>
              <td>
                <div style="font-size: 0.78rem;">
                  <?php if (!empty($ev['price_shopping_canopy'])): ?>Canopy: <strong>₹<?= number_format($ev['price_shopping_canopy'], 0) ?></strong><br><?php endif; ?>
                  <?php if (!empty($ev['price_shopping_table1'])): ?>1 Table: <strong>₹<?= number_format($ev['price_shopping_table1'], 0) ?></strong><br><?php endif; ?>
                  Base: <strong>₹<?= number_format($ev['daily_stall_price'], 0) ?></strong>
                </div>
              </td>
              <td>
                <?php
                $st = $ev['status'];
                $badgeClass = 'badge-info';
                $badgeText = ucfirst($st);
                if (in_array($st, ['published', 'active'])) {
                    $badgeClass = 'badge-success';
                    $badgeText = ($st === 'published') ? 'Published' : 'Active';
                } elseif ($st === 'pending') {
                    $badgeClass = 'badge-warning';
                    $badgeText = 'Pending Review';
                } elseif ($st === 'draft') {
                    $badgeClass = 'badge-info';
                    $badgeText = 'Draft';
                } elseif (in_array($st, ['unpublished', 'inactive'])) {
                    $badgeClass = 'badge-danger';
                    $badgeText = 'Unpublished';
                } elseif ($st === 'rejected') {
                    $badgeClass = 'badge-danger';
                    $badgeText = 'Rejected';
                }
                ?>
                <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <?php if ($ev['status'] === 'pending'): ?>
                  <a href="<?= BASE_URL ?>/admin/event-actions.php?action=approve&id=<?= $ev['id'] ?>&csrf_token=<?= csrf_token() ?>" class="admin-btn admin-btn-gold admin-btn-sm" title="Approve &amp; Publish">
                    ✓ Approve
                  </a>
                <?php elseif (in_array($ev['status'], ['published', 'active'])): ?>
                  <a href="<?= BASE_URL ?>/admin/event-actions.php?action=unpublish&id=<?= $ev['id'] ?>&csrf_token=<?= csrf_token() ?>" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #d97706;" title="Unpublish">
                    Unpublish
                  </a>
                <?php else: ?>
                  <a href="<?= BASE_URL ?>/admin/event-actions.php?action=publish&id=<?= $ev['id'] ?>&csrf_token=<?= csrf_token() ?>" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #059669;" title="Publish Live">
                    Publish
                  </a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/event.php?id=<?= $ev['id'] ?>" target="_blank" class="admin-btn admin-btn-outline admin-btn-sm" title="View live page">
                  👁️ Live
                </a>
                <a href="<?= BASE_URL ?>/admin/event-edit.php?id=<?= $ev['id'] ?>" class="admin-btn admin-btn-outline admin-btn-sm" title="Edit">
                  ✏️ Edit
                </a>
                <a href="<?= BASE_URL ?>/admin/event-actions.php?action=delete&id=<?= $ev['id'] ?>&csrf_token=<?= csrf_token() ?>" 
                   onclick="return confirm('Are you sure you want to delete this exhibition: <?= addslashes($ev['title']) ?>?');" 
                   class="admin-btn admin-btn-danger admin-btn-sm" title="Delete">
                  🗑️
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
