<?php
/**
 * Configuration Settings
 * Expo Tree Exhibitions
 */

// Error reporting for production-readiness
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Optional local overrides (if present)
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// Environment & Database Credentials
$serverHost = $_SERVER['HTTP_HOST'] ?? '';
$isHostinger = (
    stripos($serverHost, 'hostingersite.com') !== false ||
    strpos(__DIR__, 'u424679052') !== false ||
    file_exists('/home/u424679052') ||
    (!empty($serverHost) && stripos($serverHost, 'localhost') === false && stripos($serverHost, '127.0.0.1') === false)
);

if ($isHostinger) {
    if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    if (!defined('DB_PORT')) define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));
    if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'u424679052_expotree123');
    if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false && getenv('DB_PASS') !== '' ? getenv('DB_PASS') : 'yY7S@C=#');
    if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'u424679052_expotree');
} else {
    if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
    if (!defined('DB_PORT')) define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));
    if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
    if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
    if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'expotree_db');
}

// Brand Contact Info
define('SITE_NAME', 'Expo Tree Exhibitions');
define('SITE_TAGLINE', 'Shop • Explore • Support • Indulge — Your One Stop Destination for Fashion, Lifestyle & More');
define('ADMIN_PHONE', '9811175057');
define('ADMIN_WHATSAPP', '9811175057');
define('ADMIN_EMAIL', 'expotreeexhibitions@gmail.com');
define('INSTAGRAM_HANDLE', 'expo_tree_exhibitions');
define('INSTAGRAM_FOLLOWERS', '88.9K+');

// Dynamic Base URL detection
if (php_sapi_name() === 'cli') {
    define('BASE_URL', 'http://localhost/expotree');
} else {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

    // If running inside /admin or /api, strip it for root base
    $rootDir = preg_replace('/(\/admin|\/api|\/scratch).*$/', '', $scriptDir);
    $rootDir = rtrim($rootDir, '/');

    define('BASE_URL', $protocol . $host . $rootDir);
}
define('ADMIN_URL', BASE_URL . '/admin');

// Session Start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
