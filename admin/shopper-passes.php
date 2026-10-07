<?php
/**
 * Admin VIP Shopper Passes Management & CSV Export
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$adminTitle = 'VIP Shopper Entry Passes';
$activeMenu = 'shopper-passes';

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $stmtExp = $pdo->query("
        SELECT sp.pass_code, sp.name, sp.phone, sp.city,
               COALESCE(e.title, 'All Exhibitions') as target_event,
               sp.created_at
        FROM shopper_passes sp
        LEFT JOIN events e ON sp.event_id = e.id
        ORDER BY sp.created_at DESC
    ");
    $allPasses = $stmtExp->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=ExpoTree_VIP_Shoppers_' . date('Ymd_His') . '.csv');

    $out = fopen('php://output', 'w');
    $headers = ['Pass Code', 'Guest Name', 'Phone Number', 'City', 'Target Exhibition', 'Registration Date'];
    fputcsv($out, $headers);
    foreach ($allPasses as $row) {
        fputcsv($out, $row);
    }
    fclose($out);
    exit;
}

$search = sanitize($_GET['q'] ?? '');

$sql = "
    SELECT sp.*, e.title as event_title, e.city as event_city
    FROM shopper_passes sp
    LEFT JOIN events e ON sp.event_id = e.id
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $sql .= " AND (sp.pass_code LIKE :q OR sp.name LIKE :q OR sp.phone LIKE :q OR sp.city LIKE :q)";
    $params[':q'] = "%$search%";
}

$sql .= " ORDER BY sp.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$passes = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Registered VIP Shopper Passes (<?= count($passes) ?>)</h3>
    <p style="font-size: 0.85rem; color: #6b7280;">View visitor registrations, export attendees for entry scanners and lucky draw entries.</p>
  </div>
  <a href="shopper-passes.php?export=csv" class="admin-btn admin-btn-gold">
    <span>📥 Export to CSV / Excel</span>
  </a>
</div>

<!-- Search Card -->
<div class="admin-card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
  <form method="GET" action="shopper-passes.php" style="display: flex; gap: 0.75rem;">
    <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search guest name, phone, or pass code..." 
           style="flex: 1; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;" />
    <button type="submit" class="admin-btn admin-btn-burgundy">Search</button>
    <?php if (!empty($search)): ?>
      <a href="shopper-passes.php" class="admin-btn admin-btn-outline">Reset</a>
    <?php endif; ?>
  </form>
</div>

<!-- Passes Table -->
<div class="admin-card">
  <div style="overflow-x: auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Pass Code</th>
          <th>Guest Name</th>
          <th>Mobile Phone</th>
          <th>City</th>
          <th>Target Exhibition</th>
          <th>Registered On</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($passes)): ?>
          <tr><td colspan="6" style="text-align: center; color: #888; padding: 2.5rem;">No shopper passes found.</td></tr>
        <?php else: ?>
          <?php foreach ($passes as $p): ?>
            <tr>
              <td>
                <strong style="font-family: monospace; font-size: 0.9rem; color: var(--admin-burgundy); background: #fdf2f4; padding: 0.2rem 0.5rem; border-radius: 4px; border: 1px solid #ebdada;">
                  <?= e($p['pass_code']) ?>
                </strong>
              </td>
              <td><strong><?= e($p['name']) ?></strong></td>
              <td>
                <a href="tel:<?= e($p['phone']) ?>" style="color: inherit; text-decoration: none;">📞 <?= e($p['phone']) ?></a>
              </td>
              <td><?= e($p['city']) ?></td>
              <td>
                <?= !empty($p['event_title']) ? e($p['event_title']) : '<span style="color:#888;">All Exhibitions</span>' ?>
              </td>
              <td><?= date('d M Y, h:i A', strtotime($p['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
