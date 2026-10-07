<?php
/**
 * Event Organizer Submissions Review Portal
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$adminTitle = 'Organizer Submissions';
$activeMenu = 'submissions';

// Fetch Submissions
$statusFilter = sanitize($_GET['status'] ?? 'pending');

$sql = "SELECT * FROM events WHERE source = 'organizer_submission'";
$params = [];

if ($statusFilter !== 'all') {
    $sql .= " AND status = :st";
    $params[':st'] = $statusFilter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$submissions = $stmt->fetchAll();

// Count badges
$pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE source = 'organizer_submission' AND status = 'pending'")->fetchColumn();
$approvedCount = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE source = 'organizer_submission' AND status IN ('published', 'approved')")->fetchColumn();
$rejectedCount = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE source = 'organizer_submission' AND status = 'rejected'")->fetchColumn();

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827;">
      Organizer Submissions (<?= count($submissions) ?>)
    </h3>
    <p style="font-size: 0.85rem; color: #6b7280;">
      Review and verify exhibition listings submitted by RWAs, society committees, and event managers.
    </p>
  </div>
</div>

<!-- Status Tabs -->
<div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
  <a href="submissions.php?status=pending" class="admin-btn <?= $statusFilter === 'pending' ? 'admin-btn-gold' : 'admin-btn-outline' ?>">
    <span>⏳ Pending Verification (<?= $pendingCount ?>)</span>
  </a>
  <a href="submissions.php?status=published" class="admin-btn <?= $statusFilter === 'published' ? 'admin-btn-gold' : 'admin-btn-outline' ?>">
    <span>✓ Approved &amp; Published (<?= $approvedCount ?>)</span>
  </a>
  <a href="submissions.php?status=rejected" class="admin-btn <?= $statusFilter === 'rejected' ? 'admin-btn-gold' : 'admin-btn-outline' ?>">
    <span>✗ Rejected (<?= $rejectedCount ?>)</span>
  </a>
  <a href="submissions.php?status=all" class="admin-btn <?= $statusFilter === 'all' ? 'admin-btn-gold' : 'admin-btn-outline' ?>">
    <span>All Submissions</span>
  </a>
</div>

<!-- Submissions Table Card -->
<div class="admin-card">
  <div style="overflow-x: auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Exhibition Details</th>
          <th>Location &amp; Dates</th>
          <th>Stalls &amp; Pricing</th>
          <th>Organizer Contact</th>
          <th>Status</th>
          <th style="text-align: right;">Approval Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($submissions)): ?>
          <tr>
            <td colspan="7" style="text-align: center; color: #888; padding: 3rem;">
              <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📋</div>
              <p>No <?= $statusFilter !== 'all' ? e($statusFilter) : '' ?> organizer submissions found.</p>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($submissions as $sub): ?>
            <tr>
              <td>#<?= $sub['id'] ?></td>
              <td>
                <div style="font-weight: 700; color: #111827; font-size: 0.95rem;"><?= e($sub['title']) ?></div>
                <div style="font-size: 0.78rem; color: #6b7280;"><?= e($sub['event_type']) ?> • <?= e($sub['category']) ?></div>
                <div style="font-size: 0.72rem; color: #9ca3af; margin-top: 0.2rem;">Submitted: <?= date('d M Y, h:i A', strtotime($sub['created_at'])) ?></div>
              </td>
              <td>
                <div style="font-weight: 600; font-size: 0.85rem; color: #111827;">📍 <?= e($sub['venue']) ?></div>
                <div style="font-size: 0.78rem; color: #4b5563;"><?= e($sub['city']) ?> (<?= e($sub['location_type']) ?>)</div>
                <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.2rem;">📅 <?= e($sub['date_display'] ?? formatDateRange($sub['start_date'], $sub['end_date'])) ?></div>
              </td>
              <td>
                <div style="font-size: 0.82rem;"><strong><?= $sub['total_stalls'] ?></strong> Total Stalls</div>
                <div style="font-size: 0.75rem; color: #6b7280;">
                  From: <strong>₹<?= number_format($sub['daily_stall_price'], 0) ?>/day</strong>
                </div>
              </td>
              <td>
                <div style="font-weight: 600; color: #111827; font-size: 0.85rem;"><?= e($sub['organizer_name']) ?></div>
                <?php if (!empty($sub['organizer_company'])): ?>
                  <div style="font-size: 0.75rem; color: #4b5563;"><?= e($sub['organizer_company']) ?></div>
                <?php endif; ?>
                <div style="font-size: 0.78rem; margin-top: 0.25rem;">
                  <a href="tel:<?= e($sub['organizer_phone']) ?>" style="color: var(--admin-burgundy); font-weight: 600; text-decoration: none;">📞 <?= e($sub['organizer_phone']) ?></a>
                </div>
                <div style="font-size: 0.75rem;">
                  <a href="https://wa.me/<?= e($sub['organizer_whatsapp']) ?>" target="_blank" style="color: #25d366; text-decoration: none; font-weight: 600;">💬 WhatsApp</a>
                </div>
              </td>
              <td>
                <?php if ($sub['status'] === 'pending'): ?>
                  <span class="badge badge-warning">⏳ Pending Approval</span>
                <?php elseif ($sub['status'] === 'published' || $sub['status'] === 'active'): ?>
                  <span class="badge badge-success">✓ Live Published</span>
                <?php elseif ($sub['status'] === 'rejected'): ?>
                  <span class="badge badge-danger">✗ Rejected</span>
                <?php else: ?>
                  <span class="badge badge-info"><?= ucfirst($sub['status']) ?></span>
                <?php endif; ?>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <?php if ($sub['status'] === 'pending'): ?>
                  <a href="<?= BASE_URL ?>/admin/event-actions.php?action=approve&id=<?= $sub['id'] ?>&csrf_token=<?= csrf_token() ?>&return_to=submissions.php" 
                     class="admin-btn admin-btn-gold admin-btn-sm" title="Approve and publish live">
                    ✓ Approve
                  </a>
                  <a href="<?= BASE_URL ?>/admin/event-actions.php?action=reject&id=<?= $sub['id'] ?>&csrf_token=<?= csrf_token() ?>&return_to=submissions.php" 
                     onclick="return confirm('Are you sure you want to reject this submission?');"
                     class="admin-btn admin-btn-outline admin-btn-sm" style="color: #dc2626;" title="Reject submission">
                    ✗ Reject
                  </a>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/admin/event-edit.php?id=<?= $sub['id'] ?>" class="admin-btn admin-btn-outline admin-btn-sm" title="Edit Details">
                  ✏️ Edit
                </a>

                <a href="<?= BASE_URL ?>/admin/event-actions.php?action=delete&id=<?= $sub['id'] ?>&csrf_token=<?= csrf_token() ?>&return_to=submissions.php" 
                   onclick="return confirm('Permanently delete this submission?');"
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
