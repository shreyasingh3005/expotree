<?php
/**
 * Common Header Component
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Fallback metadata if not set by individual page
$pageTitle = $pageTitle ?? SITE_NAME . ' | Premier Lifestyle & Festive Exhibitions Delhi NCR';
$pageDesc = $pageDesc ?? 'Delhi NCR premier lifestyle and festive exhibitions organizer. Book stalls across Gurugram, Noida, Delhi, Faridabad, Ghaziabad. Hotline: 9811175057';
$currentPage = $currentPage ?? basename($_SERVER['SCRIPT_NAME'], '.php');
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <!-- SEO Meta Tags -->
  <title><?= e($pageTitle) ?></title>
  <meta name="title" content="<?= e($pageTitle) ?>" />
  <meta name="description" content="<?= e($pageDesc) ?>" />
  <meta name="keywords" content="Expo Tree Exhibitions, book exhibition stall Delhi NCR, lifestyle exhibition Gurugram, festive exhibition Noida, stall booking 9811175057, jewellery exhibition, handicrafts mela, list exhibition" />
  <meta name="author" content="Expo Tree Exhibitions" />

  <!-- Open Graph / Social -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?= e(BASE_URL . '/' . basename($_SERVER['SCRIPT_NAME'])) ?>" />
  <meta property="og:title" content="<?= e($pageTitle) ?>" />
  <meta property="og:description" content="<?= e($pageDesc) ?>" />
  <meta property="og:image" content="<?= BASE_URL ?>/images/logo.svg" />

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/images/favicon.svg" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,400;1,600&display=swap" rel="stylesheet">

  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css" />

  <!-- Global Base URL definition for JS -->
  <script>
    window.EXPO_CONFIG = {
      baseUrl: '<?= BASE_URL ?>',
      adminPhone: '<?= ADMIN_PHONE ?>',
      adminWhatsApp: '<?= ADMIN_WHATSAPP ?>'
    };
  </script>
</head>
<body class="<?= !empty($bodyClass) ? e($bodyClass) : '' ?>">

  <!-- ==========================================================================
       TOP ANNOUNCEMENT BAR & AUDIENCE SWITCHER
       ========================================================================== -->
  <aside class="top-announcement-bar" aria-label="Announcement & Hotlines">
    <div class="top-announcement-inner">
      <div class="top-highlights">
        <div class="top-highlight-item">
          <span>✨</span>
          <span>Delhi NCR’s Premier Lifestyle &amp; Festive Exhibition Partner</span>
        </div>
        <div class="top-highlight-item">
          <span>📍</span>
          <span>Gurugram • Noida • Delhi • Faridabad • Ghaziabad</span>
        </div>
      </div>

      <div class="top-actions">
        <!-- Dual Audience Toggle -->
        <div class="audience-mode-toggle" title="Switch Experience">
          <a href="<?= BASE_URL ?>/upcoming-exhibitions.php" class="mode-btn <?= in_array($currentPage, ['upcoming-exhibitions', 'event', 'book-a-stall']) ? 'active' : '' ?>">🎪 For Exhibitors</a>
          <a href="<?= BASE_URL ?>/free-shopper-pass.php" class="mode-btn <?= $currentPage === 'free-shopper-pass' ? 'active' : '' ?>">🛍️ For Shoppers</a>
        </div>

        <a href="tel:<?= ADMIN_PHONE ?>" class="top-phone-link" title="Call Organizer Hotline">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <span><?= ADMIN_PHONE ?></span>
        </a>
      </div>
    </div>
  </aside>

  <!-- ==========================================================================
       MAIN NAVIGATION BAR
       ========================================================================== -->
  <header class="main-header" id="site-header">
    <nav class="navbar" aria-label="Main Navigation">
      <a href="<?= BASE_URL ?>/index.php" class="brand-logo" aria-label="Expo Tree Exhibitions Home">
        <img src="<?= BASE_URL ?>/images/logo.svg" alt="Expo Tree Exhibitions Logo" width="165" height="42" />
      </a>

      <!-- Desktop Nav Menu -->
      <ul class="nav-menu" id="primary-nav-menu">
        <li><a href="<?= BASE_URL ?>/index.php" class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>">Home</a></li>
        <li><a href="<?= BASE_URL ?>/upcoming-exhibitions.php" class="nav-link <?= in_array($currentPage, ['upcoming-exhibitions', 'events', 'event']) ? 'active' : '' ?>">Exhibitions</a></li>
        <li><a href="<?= BASE_URL ?>/categories.php" class="nav-link <?= $currentPage === 'categories' ? 'active' : '' ?>">Categories</a></li>
        <li>
          <a href="<?= BASE_URL ?>/list-your-event.php" class="nav-link nav-link-highlight <?= $currentPage === 'list-your-event' ? 'active' : '' ?>">
            <span>List Your Event</span>
            <span class="nav-badge-fast">Free</span>
          </a>
        </li>
        <li><a href="<?= BASE_URL ?>/free-shopper-pass.php" class="nav-link <?= $currentPage === 'free-shopper-pass' ? 'active' : '' ?>">Shopper Pass</a></li>
        <li><a href="<?= BASE_URL ?>/gallery.php" class="nav-link <?= $currentPage === 'gallery' ? 'active' : '' ?>">Gallery</a></li>
        <li><a href="<?= BASE_URL ?>/about-us.php" class="nav-link <?= $currentPage === 'about-us' ? 'active' : '' ?>">About Us</a></li>
        <li><a href="<?= BASE_URL ?>/faq.php" class="nav-link <?= $currentPage === 'faq' ? 'active' : '' ?>">FAQ</a></li>
        <li><a href="<?= BASE_URL ?>/contact.php" class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a></li>
      </ul>

      <div class="nav-cta-group">
        <a href="<?= BASE_URL ?>/book-a-stall.php" class="btn btn-gold nav-desktop-cta">
          <span>Book a Stall</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>

        <button class="mobile-menu-toggle" id="mobile-toggle-btn" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="mobile-nav-drawer">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
      </div>
    </nav>
  </header>

  <!-- Mobile Slide Drawer Overlay & Container (Guaranteed No Blackout Screen) -->
  <div class="mobile-nav-backdrop" id="mobile-nav-backdrop"></div>
  <aside class="mobile-nav-drawer" id="mobile-nav-drawer" aria-label="Mobile Navigation Menu">
    <div class="mobile-drawer-header">
      <div class="drawer-brand">
        <img src="<?= BASE_URL ?>/images/logo.svg" alt="Expo Tree Logo" height="32" />
      </div>
      <button class="drawer-close-btn" id="drawer-close-btn" aria-label="Close menu">&times;</button>
    </div>

    <div class="mobile-drawer-body">
      <ul class="mobile-drawer-menu">
        <li><a href="<?= BASE_URL ?>/index.php" class="drawer-link <?= $currentPage === 'index' ? 'active' : '' ?>"><span>🏠 Home</span></a></li>
        <li><a href="<?= BASE_URL ?>/upcoming-exhibitions.php" class="drawer-link <?= in_array($currentPage, ['upcoming-exhibitions', 'events', 'event']) ? 'active' : '' ?>"><span>🎪 Upcoming Exhibitions</span><span class="drawer-tag">Active</span></a></li>
        <li><a href="<?= BASE_URL ?>/book-a-stall.php" class="drawer-link <?= $currentPage === 'book-a-stall' ? 'active' : '' ?>"><span>🎟️ Book a Stall</span><span class="drawer-tag gold">Hot</span></a></li>
        <li><a href="<?= BASE_URL ?>/list-your-event.php" class="drawer-link <?= $currentPage === 'list-your-event' ? 'active' : '' ?>"><span>✦ List Your Event</span><span class="drawer-tag fast">Instant Live</span></a></li>
        <li><a href="<?= BASE_URL ?>/free-shopper-pass.php" class="drawer-link <?= $currentPage === 'free-shopper-pass' ? 'active' : '' ?>"><span>🛍️ Free Shopper Pass</span><span class="drawer-tag">VIP</span></a></li>
        <li><a href="<?= BASE_URL ?>/categories.php" class="drawer-link <?= $currentPage === 'categories' ? 'active' : '' ?>"><span>✨ 16 Categories We Host</span></a></li>
        <li><a href="<?= BASE_URL ?>/gallery.php" class="drawer-link <?= $currentPage === 'gallery' ? 'active' : '' ?>"><span>📸 Photo Gallery</span></a></li>
        <li><a href="<?= BASE_URL ?>/about-us.php" class="drawer-link <?= $currentPage === 'about-us' ? 'active' : '' ?>"><span>👑 About Expo Tree</span></a></li>
        <li><a href="<?= BASE_URL ?>/faq.php" class="drawer-link <?= $currentPage === 'faq' ? 'active' : '' ?>"><span>❓ Frequently Asked Questions</span></a></li>
        <li><a href="<?= BASE_URL ?>/contact.php" class="drawer-link <?= $currentPage === 'contact' ? 'active' : '' ?>"><span>📞 Contact Us</span></a></li>
        <li><a href="<?= BASE_URL ?>/admin/index.php" class="drawer-link" style="color: var(--gold-400); border-top: 1px dashed rgba(212,175,55,0.3); margin-top: 0.5rem;"><span>🔐 Admin Portal</span></a></li>
      </ul>

      <div class="drawer-contact-card">
        <div class="drawer-contact-title">Exhibition Hotline</div>
        <a href="tel:<?= ADMIN_PHONE ?>" class="drawer-phone-btn">📞 <?= ADMIN_PHONE ?></a>
        <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>" target="_blank" rel="noopener noreferrer" class="drawer-wa-btn">💬 Chat on WhatsApp</a>
      </div>
    </div>
  </aside>

  <!-- Flash Notification Display (if any) -->
  <?php $flash = getFlash(); ?>
  <?php if ($flash): ?>
    <div class="site-flash-alert alert-<?= e($flash['type']) ?>" role="alert">
      <div class="container site-flash-inner">
        <span><?= $flash['message'] ?></span>
        <button type="button" class="flash-close-btn" onclick="this.parentElement.parentElement.remove();">&times;</button>
      </div>
    </div>
  <?php endif; ?>

  <main id="main-content">
