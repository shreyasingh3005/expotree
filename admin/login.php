<?php
/**
 * Administrator Login Portal
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// If already logged in, redirect to dashboard
if (isAdminLoggedIn()) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username/email and password.';
        } else {
            // Check admin_users table
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE (username = :u1 OR email = :u2) AND status = 'active' LIMIT 1");
            $stmt->execute([':u1' => $username, ':u2' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_role'] = $admin['role'];

                setFlash('success', 'Welcome back, ' . $admin['full_name'] . '!');
                header('Location: ' . BASE_URL . '/admin/index.php');
                exit;
            } else {
                $error = 'Invalid credentials. Please verify username and password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login | Expo Tree Exhibitions</title>
  <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/images/favicon.svg" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Outfit', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at 50% 30%, #25060b 0%, #140306 65%, #0a0103 100%);
      color: #ffffff;
      padding: 1.5rem;
    }
    .login-card {
      width: 100%;
      max-width: 440px;
      background: rgba(255, 255, 255, 0.05);
      border: 1.5px solid rgba(197, 155, 39, 0.35);
      border-radius: 20px;
      padding: 2.5rem 2rem;
      backdrop-filter: blur(12px);
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7);
    }
    .login-logo {
      text-align: center;
      margin-bottom: 1.75rem;
    }
    .login-title {
      font-family: 'Cinzel', serif;
      font-size: 1.4rem;
      color: #c59b27;
      text-align: center;
      margin-bottom: 0.25rem;
    }
    .login-sub {
      text-align: center;
      font-size: 0.85rem;
      color: #d1b4b9;
      margin-bottom: 1.75rem;
    }
    .form-group {
      margin-bottom: 1.25rem;
    }
    .form-label {
      display: block;
      font-size: 0.85rem;
      font-weight: 600;
      color: #e5c3c8;
      margin-bottom: 0.4rem;
    }
    .form-input {
      width: 100%;
      padding: 0.8rem 1rem;
      background: rgba(0, 0, 0, 0.4);
      border: 1.5px solid rgba(197, 155, 39, 0.25);
      border-radius: 10px;
      color: #ffffff;
      font-size: 0.95rem;
      outline: none;
      transition: border-color 0.2s;
    }
    .form-input:focus {
      border-color: #c59b27;
    }
    .login-btn {
      width: 100%;
      padding: 0.85rem;
      background: #c59b27;
      color: #160205;
      border: none;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s;
      margin-top: 0.5rem;
    }
    .login-btn:hover {
      background: #b0881e;
    }
    .error-alert {
      background: rgba(220, 38, 38, 0.2);
      border: 1px solid #dc2626;
      color: #fca5a5;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      font-size: 0.88rem;
      margin-bottom: 1.25rem;
      text-align: center;
    }
    .default-creds {
      background: rgba(212, 175, 55, 0.08);
      border: 1px dashed rgba(212, 175, 55, 0.3);
      padding: 0.75rem;
      border-radius: 8px;
      font-size: 0.78rem;
      color: #e2c0c7;
      margin-top: 1.5rem;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <div class="login-logo">
      <img src="<?= BASE_URL ?>/images/logo.svg" alt="Expo Tree Logo" height="38" />
    </div>

    <h1 class="login-title">Administrator Portal</h1>
    <p class="login-sub">Sign in to manage exhibitions, stalls &amp; bookings</p>

    <?php if (!empty($error)): ?>
      <div class="error-alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />

      <div class="form-group">
        <label class="form-label" for="username">Username or Email</label>
        <input type="text" name="username" id="username" class="form-input" placeholder="admin" required autofocus />
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" name="password" id="password" class="form-input" placeholder="••••••••••••" required />
      </div>

      <button type="submit" class="login-btn">Sign In to Dashboard</button>
    </form>

    <div class="default-creds">
      <strong>Default Admin Credentials:</strong><br>
      Username: <code>admin</code> | Password: <code>Admin@ExpoTree2026</code>
    </div>

    <div style="text-align: center; margin-top: 1.25rem;">
      <a href="<?= BASE_URL ?>/index.php" style="color: #D4AF37; font-size: 0.85rem; text-decoration: none;">← Return to Public Website</a>
    </div>
  </div>

</body>
</html>
