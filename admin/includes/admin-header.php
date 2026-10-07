<?php
/**
 * Admin Layout Header
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/auth-check.php';

$adminTitle = $adminTitle ?? 'Admin Portal | Expo Tree Exhibitions';
$activeMenu = $activeMenu ?? 'dashboard';

$pendingSubCount = 0;
$pendingBookingsCount = 0;
if (isset($pdo)) {
    try {
        $pendingSubCount = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE status = 'pending'")->fetchColumn();
        $pendingBookingsCount = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($adminTitle) ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/images/favicon.svg" />
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --admin-bg: #f8fafc;
      --admin-sidebar-bg: #141118;
      --admin-card-bg: #ffffff;
      --admin-gold: #c59b27;
      --admin-gold-hover: #b0881e;
      --admin-burgundy: #2c060e;
      --admin-border: #e2e8f0;
      --admin-text-dark: #0f172a;
      --admin-text-muted: #64748b;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--admin-bg);
      color: var(--admin-text-dark);
      min-height: 100vh;
      display: flex;
    }
    
    /* Sidebar */
    .admin-sidebar {
      width: 260px;
      background: #141118;
      border-right: 1px solid #272230;
      color: #f8fafc;
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 100;
      transition: transform 0.3s ease;
    }
    .admin-brand {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid #272230;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .admin-brand img { height: 32px; width: auto; }
    .admin-menu {
      list-style: none;
      padding: 1rem 0.75rem;
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
      overflow-y: auto;
    }
    .admin-menu a {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.7rem 0.95rem;
      border-radius: 8px;
      color: #94a3b8;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.2s;
    }
    .admin-menu a:hover {
      background: rgba(255, 255, 255, 0.05);
      color: #ffffff;
    }
    .admin-menu a.active {
      background: rgba(197, 155, 39, 0.15);
      color: #ffffff;
      border-left: 3px solid var(--admin-gold);
      font-weight: 600;
    }
    .admin-menu .menu-badge {
      margin-left: auto;
      background: var(--admin-gold);
      color: #1a080c;
      font-size: 0.72rem;
      font-weight: 700;
      padding: 0.15rem 0.5rem;
      border-radius: 9999px;
    }
    .admin-user-pill {
      padding: 1rem 1.25rem;
      border-top: 1px solid #272230;
      background: rgba(0,0,0,0.3);
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 0.85rem;
    }
    
    /* Main Content Area */
    .admin-main {
      flex: 1;
      margin-left: 260px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .admin-topbar {
      background: #ffffff;
      border-bottom: 1px solid var(--admin-border);
      padding: 0.85rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 90;
      box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .admin-content {
      padding: 2rem;
      flex: 1;
    }
    
    /* Common UI Elements */
    .admin-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.55rem 1.15rem;
      border-radius: 8px;
      font-size: 0.88rem;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.2s;
    }
    .admin-btn-gold {
      background: var(--admin-gold);
      color: #1a080c;
      border-color: var(--admin-gold);
    }
    .admin-btn-gold:hover {
      background: var(--admin-gold-hover);
    }
    .admin-btn-burgundy {
      background: var(--admin-burgundy);
      color: #ffffff;
      border-color: var(--admin-burgundy);
    }
    .admin-btn-burgundy:hover {
      background: #3d0914;
    }
    .admin-btn-outline {
      background: #ffffff;
      border: 1px solid #cbd5e1;
      color: #334155;
    }
    .admin-btn-outline:hover {
      background: #f8fafc;
      border-color: #94a3b8;
    }
    .admin-btn-danger {
      background: #e11d48;
      color: #ffffff;
      border-color: #e11d48;
    }
    .admin-btn-danger:hover {
      background: #be123c;
    }
    .admin-btn-sm {
      padding: 0.35rem 0.65rem;
      font-size: 0.8rem;
      border-radius: 6px;
    }
    
    /* Cards & Stats */
    .admin-card {
      background: #ffffff;
      border-radius: 12px;
      border: 1px solid var(--admin-border);
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
      margin-bottom: 2rem;
      overflow: hidden;
    }
    .admin-card-header {
      padding: 1.15rem 1.5rem;
      border-bottom: 1px solid var(--admin-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 0.75rem;
      background: #ffffff;
    }
    .admin-card-body {
      padding: 1.5rem;
    }
    
    /* Tables */
    .admin-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.88rem;
      text-align: left;
    }
    .admin-table th {
      background: #f8fafc;
      padding: 0.75rem 1rem;
      font-weight: 600;
      color: #475569;
      border-bottom: 1px solid var(--admin-border);
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.04em;
    }
    .admin-table td {
      padding: 0.85rem 1rem;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
      color: #1e293b;
    }
    .admin-table tr:hover td {
      background: #f8fafc;
    }
    
    /* Badges */
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      padding: 0.2rem 0.6rem;
      border-radius: 9999px;
      font-size: 0.74rem;
      font-weight: 600;
      border: 1px solid transparent;
    }
    .badge-success { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
    .badge-warning { background: #fffbeb; color: #92400e; border-color: #fde68a; }
    .badge-danger { background: #fff1f2; color: #9f1239; border-color: #fecdd3; }
    .badge-info { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }
    
    /* Mobile responsive */
    .mobile-admin-toggle { display: none; }
    @media (max-width: 992px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }
      .admin-sidebar.open {
        transform: translateX(0);
      }
      .admin-main {
        margin-left: 0;
      }
      .mobile-admin-toggle {
        display: inline-flex;
        align-items: center;
        background: none;
        border: none;
        font-size: 1.4rem;
        cursor: pointer;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-brand">
      <img src="<?= BASE_URL ?>/images/logo.svg" alt="Expo Tree" />
      <div>
        <div style="font-family: 'Cinzel', serif; font-size: 0.85rem; color: var(--admin-gold); font-weight: 700;">EXPO TREE</div>
        <div style="font-size: 0.68rem; color: #a37f86;">Admin Portal v2.0</div>
      </div>
    </div>

    <ul class="admin-menu">
      <li>
        <a href="<?= BASE_URL ?>/admin/index.php" class="<?= $activeMenu === 'dashboard' ? 'active' : '' ?>">
          <span>📊</span>
          <span>Dashboard</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/events.php" class="<?= in_array($activeMenu, ['events', 'event-edit']) ? 'active' : '' ?>">
          <span>🎪</span>
          <span>All Events</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/event-add.php" class="<?= $activeMenu === 'event-add' ? 'active' : '' ?>">
          <span>➕</span>
          <span>Add New Event</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/submissions.php" class="<?= $activeMenu === 'submissions' ? 'active' : '' ?>">
          <span>📋</span>
          <span>Submissions</span>
          <?php if ($pendingSubCount > 0): ?>
            <span class="menu-badge" style="background: #f59e0b; color: #1a0408; font-weight: 800;"><?= $pendingSubCount ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/bookings.php" class="<?= $activeMenu === 'bookings' ? 'active' : '' ?>">
          <span>🎟️</span>
          <span>Stall Bookings</span>
          <?php if ($pendingBookingsCount > 0): ?>
            <span class="menu-badge" style="background: #3b82f6; color: #fff; font-weight: 700;"><?= $pendingBookingsCount ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/shopper-passes.php" class="<?= $activeMenu === 'shopper-passes' ? 'active' : '' ?>">
          <span>🛍️</span>
          <span>VIP Shopper Passes</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/import.php" class="<?= $activeMenu === 'import' ? 'active' : '' ?>">
          <span>📥</span>
          <span>Excel / CSV Import</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/settings.php" class="<?= $activeMenu === 'settings' ? 'active' : '' ?>">
          <span>⚙️</span>
          <span>Website Settings</span>
        </a>
      </li>
      
      <li style="margin-top: auto; border-top: 1px dashed var(--admin-border); padding-top: 0.75rem;">
        <a href="<?= BASE_URL ?>/index.php" target="_blank">
          <span>🌐</span>
          <span>View Public Site ↗</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/logout.php" style="color: #fca5a5;">
          <span>🚪</span>
          <span>Logout</span>
        </a>
      </li>
    </ul>

    <div class="admin-user-pill">
      <div>
        <div style="font-weight: 600; color: #fff;"><?= e($_SESSION['admin_name'] ?? 'Administrator') ?></div>
        <div style="font-size: 0.72rem; color: #9ca3af;"><?= e($_SESSION['admin_username'] ?? 'admin') ?></div>
      </div>
      <a href="<?= BASE_URL ?>/admin/logout.php" title="Sign out" style="color: #f87171; text-decoration: none; font-size: 1.1rem;">⏻</a>
    </div>
  </aside>

  <!-- Main Content Wrapper -->
  <div class="admin-main">
    
    <!-- Topbar -->
    <header class="admin-topbar">
      <div style="display: flex; align-items: center; gap: 1rem;">
        <button class="mobile-admin-toggle" onclick="document.getElementById('adminSidebar').classList.toggle('open');">☰</button>
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--admin-burgundy);"><?= e($adminTitle) ?></h2>
      </div>

      <div style="display: flex; align-items: center; gap: 1rem;">
        <a href="<?= BASE_URL ?>/admin/event-add.php" class="admin-btn admin-btn-gold admin-btn-sm">
          <span>+ Add Event</span>
        </a>
        <a href="<?= BASE_URL ?>/index.php" target="_blank" class="admin-btn admin-btn-outline admin-btn-sm">
          <span>Live Site ↗</span>
        </a>
      </div>
    </header>

    <!-- Admin Content Area -->
    <main class="admin-content">

      <!-- Flash messages in admin -->
      <?php $flash = getFlash(); ?>
      <?php if ($flash): ?>
        <div style="margin-bottom: 1.5rem; padding: 0.85rem 1.25rem; border-radius: 8px; font-size: 0.9rem; font-weight: 500; display: flex; justify-content: space-between; align-items: center; <?= $flash['type'] === 'success' ? 'background: #d1fae5; color: #065f46; border: 1px solid #10b981;' : ($flash['type'] === 'danger' ? 'background: #fee2e2; color: #991b1b; border: 1px solid #f87171;' : 'background: #fef3c7; color: #92400e; border: 1px solid #f59e0b;') ?>">
          <span><?= $flash['message'] ?></span>
          <button type="button" onclick="this.parentElement.remove();" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: inherit;">&times;</button>
        </div>
      <?php endif; ?>
