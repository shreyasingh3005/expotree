<?php
/**
 * Hostinger One-Click Database Deployment & Migration Runner
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$sqlFile = __DIR__ . '/hostinger_u424679052_expotree.sql';
$message = '';
$messageType = '';
$isSetupNeeded = false;

// Check existing tables
$existingTables = [];
try {
    $existingTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $message = "Database query error: " . $e->getMessage();
    $messageType = "error";
}

if (empty($existingTables) || !in_array('events', $existingTables)) {
    $isSetupNeeded = true;
}

// Handle Run Setup Action
if (isset($_POST['run_setup']) || isset($_GET['auto_run'])) {
    if (!file_exists($sqlFile)) {
        $message = "SQL Dump file not found at: " . htmlspecialchars($sqlFile);
        $messageType = "error";
    } else {
        try {
            $sqlContent = file_get_contents($sqlFile);
            
            // Execute statements
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
            $pdo->exec($sqlContent);
            
            // Refresh table list
            $existingTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            $eventCount = in_array('events', $existingTables) ? $pdo->query("SELECT COUNT(*) FROM `events`")->fetchColumn() : 0;
            $adminCount = in_array('admin_users', $existingTables) ? $pdo->query("SELECT COUNT(*) FROM `admin_users`")->fetchColumn() : 0;
            
            $message = "Database successfully initialized! Created " . count($existingTables) . " tables with $eventCount events and $adminCount admin user.";
            $messageType = "success";
            $isSetupNeeded = false;
        } catch (Exception $ex) {
            $message = "SQL Execution Error: " . $ex->getMessage();
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hostinger Database Setup | Expo Tree Exhibitions</title>
  <style>
    :root {
      --bg: #0e0204;
      --card: #1c0509;
      --border: #4a1520;
      --gold: #d4af37;
      --text: #f5f0eb;
      --text-muted: #a68b91;
      --success-bg: #0f3d1f;
      --success-text: #6ee7b7;
      --error-bg: #4c1117;
      --error-text: #fca5a5;
    }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background: var(--bg);
      color: var(--text);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      padding: 1.5rem;
      box-sizing: border-box;
    }
    .setup-card {
      background: var(--card);
      border: 1.5px solid var(--border);
      border-radius: 16px;
      padding: 2.5rem;
      max-width: 650px;
      width: 100%;
      box-shadow: 0 20px 50px rgba(0,0,0,0.6);
    }
    h1 {
      font-size: 1.6rem;
      color: var(--gold);
      margin-top: 0;
      margin-bottom: 0.5rem;
    }
    p {
      color: var(--text-muted);
      line-height: 1.6;
      font-size: 0.95rem;
    }
    .alert {
      padding: 1rem 1.25rem;
      border-radius: 10px;
      margin: 1.5rem 0;
      font-size: 0.95rem;
      line-height: 1.5;
    }
    .alert-success { background: var(--success-bg); color: var(--success-text); border: 1px solid #10b981; }
    .alert-error { background: var(--error-bg); color: var(--error-text); border: 1px solid #ef4444; }
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin: 1.5rem 0;
      font-size: 0.88rem;
    }
    .info-table td {
      padding: 0.65rem 0.75rem;
      border-bottom: 1px solid var(--border);
    }
    .info-table td:first-child {
      color: var(--text-muted);
      width: 38%;
    }
    .info-table td:last-child {
      color: #ffffff;
      font-family: monospace;
      font-weight: 600;
    }
    .btn {
      display: inline-block;
      background: var(--gold);
      color: #1a0408;
      font-weight: 700;
      padding: 0.85rem 1.75rem;
      border-radius: 8px;
      text-decoration: none;
      border: none;
      cursor: pointer;
      font-size: 0.95rem;
      transition: all 0.2s ease;
    }
    .btn:hover {
      background: #f1cf59;
      transform: translateY(-1px);
    }
    .btn-secondary {
      background: transparent;
      color: var(--gold);
      border: 1px solid var(--gold);
      margin-left: 0.75rem;
    }
    .btn-secondary:hover {
      background: rgba(212, 175, 55, 0.1);
      color: #f1cf59;
    }
    .status-badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.8rem;
      font-weight: 700;
    }
    .status-badge.live { background: #064e3b; color: #a7f3d0; }
    .status-badge.warn { background: #78350f; color: #fde68a; }
  </style>
</head>
<body>
  <div class="setup-card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
      <h1>Expo Tree Hostinger DB Deployer</h1>
      <span class="status-badge <?= $isSetupNeeded ? 'warn' : 'live' ?>">
        <?= $isSetupNeeded ? 'Setup Pending' : 'Database Ready' ?>
      </span>
    </div>
    <p>This automated utility manages the database on Hostinger (<code>u424679052_expotree</code>) for Expo Tree Exhibitions.</p>

    <?php if ($message): ?>
      <div class="alert alert-<?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <table class="info-table">
      <tr>
        <td>Connected DB Host</td>
        <td><?= htmlspecialchars(DB_HOST) ?></td>
      </tr>
      <tr>
        <td>Target Database</td>
        <td><?= htmlspecialchars(DB_NAME) ?></td>
      </tr>
      <tr>
        <td>Database User</td>
        <td><?= htmlspecialchars(DB_USER) ?></td>
      </tr>
      <tr>
        <td>Existing Tables</td>
        <td><?= count($existingTables) > 0 ? htmlspecialchars(implode(', ', $existingTables)) : 'None (Empty Database)' ?></td>
      </tr>
      <tr>
        <td>SQL Source File</td>
        <td><?= file_exists($sqlFile) ? '✓ Found (' . round(filesize($sqlFile)/1024, 1) . ' KB)' : '✗ Missing' ?></td>
      </tr>
    </table>

    <div style="margin-top: 2rem;">
      <form method="POST" style="display: inline-block;">
        <button type="submit" name="run_setup" value="1" class="btn" onclick="return confirm('Initialize database now? This will create tables and import all 23 live events.')">
          <?= $isSetupNeeded ? '🚀 Initialize Database Now' : '🔄 Re-import / Update Database' ?>
        </button>
      </form>
      <a href="../" class="btn btn-secondary">Visit Website</a>
      <a href="../admin/" class="btn btn-secondary">Admin Login</a>
    </div>

    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); font-size: 0.82rem; color: var(--text-muted);">
      <strong>Default Admin Credentials:</strong><br>
      Username: <code>admin</code> or <code>admin@expotreeexhibitions.com</code><br>
      Password: <code>Admin@ExpoTree2026</code>
    </div>
  </div>
</body>
</html>
