<?php
/**
 * Custom 404 Page
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Page Not Found (404) | Expo Tree Exhibitions';
$pageDesc = 'The requested exhibition page was not found.';
$currentPage = '404';

http_response_code(404);
require_once __DIR__ . '/includes/header.php';
?>

<div style="min-height: 70vh; display: flex; align-items: center; justify-content: center; text-align: center; background: radial-gradient(circle at 50% 30%, #25060b 0%, #150306 60%, #0a0103 100%); color: #ffffff; padding: 4rem 1.5rem;">
  <div style="max-width: 620px; background: rgba(255, 255, 255, 0.04); border: 2px solid var(--gold-500); border-radius: 24px; padding: 3.5rem 2.5rem; backdrop-filter: blur(12px); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6);">
    <div style="font-family: var(--font-cinzel); font-size: 5.5rem; font-weight: 900; color: var(--gold-400); line-height: 1; margin-bottom: 0.75rem; text-shadow: 0 0 25px rgba(212, 175, 55, 0.4);">
      404
    </div>
    <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>
    <h2 style="font-family: var(--font-cinzel); font-size: 1.85rem; color: #ffffff; margin-bottom: 1rem;">
      Exhibition Page Not Found
    </h2>
    <p style="color: rgba(255, 255, 255, 0.85); font-size: 1rem; margin-bottom: 2rem; line-height: 1.6;">
      The exhibition you're looking for might have concluded, been rescheduled, or the link may have expired. Explore our live upcoming calendar!
    </p>
    <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
      <a href="<?= BASE_URL ?>/index.php" class="btn btn-gold">
        <span>🏠 Return Home</span>
      </a>
      <a href="<?= BASE_URL ?>/upcoming-exhibitions.php" class="btn btn-outline-white">
        <span>🎪 Upcoming Exhibitions</span>
      </a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
