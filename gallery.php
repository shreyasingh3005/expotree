<?php
/**
 * Real Photo Gallery & Lightbox
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Photo Gallery | Expo Tree Exhibitions Delhi NCR';
$pageDesc = 'Explore real exhibition photos of bustling canopies, shopper footfall, jewellery collections, and festive melas across Delhi NCR.';
$currentPage = 'gallery';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section page-hero-section">
  <div class="hero-bg-photo" aria-hidden="true" style="opacity: 0.16;"></div>
  <div class="container" style="position: relative; z-index: 2;">
    <span class="section-badge badge-gold">✦ Visual Archives</span>
    <h1 class="section-title title-white">Real Photo Gallery</h1>
    <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>
    <p class="section-subtitle subtitle-white" style="max-width: 800px; margin: 0 auto;">
      Take a visual tour through our bustling exhibition canopies, crowd enthusiasm, and designer collections across Ambience Mall, DLF Mall of India, and South Delhi pavilions.
    </p>
  </div>
</section>

<!-- Gallery Grid with Lightbox -->
<section class="section gallery-section">
  <div class="container">
    <div class="gallery-filter-tabs">
      <button class="gallery-tab-btn active" data-gallery-cat="all">All Photos</button>
      <button class="gallery-tab-btn" data-gallery-cat="stalls">Stalls &amp; Canopies</button>
      <button class="gallery-tab-btn" data-gallery-cat="crowd">Shoppers &amp; Crowd</button>
      <button class="gallery-tab-btn" data-gallery-cat="products">Designer Products</button>
    </div>

    <div id="gallery-masonry" class="gallery-masonry">
      <!-- Rendered by js/main.js -->
    </div>

    <!-- Exhibitor Callout Banner -->
    <div style="margin-top: 4.5rem; background: var(--burgundy-900); border: 1.5px solid var(--border-gold); border-radius: 20px; padding: 3rem 2rem; text-align: center; color: #ffffff; box-shadow: var(--shadow-card);">
      <h3 style="font-family: var(--font-cinzel); font-size: 2rem; color: var(--gold-300); margin-bottom: 0.8rem;">
        Want Your Brand in Our Next Photo Spotlight?
      </h3>
      <p style="max-width: 650px; margin: 0 auto 1.75rem; color: rgba(255,255,255,0.85); font-size: 1.05rem;">
        Secure your stall at our next exhibition and gain massive visibility across our <?= INSTAGRAM_FOLLOWERS ?> Instagram community.
      </p>
      <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
        <a href="book-a-stall.php" class="btn btn-gold" style="font-size: 1rem; padding: 0.9rem 2rem;">
          <span>🎪 Book Your Stall Now</span>
        </a>
        <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp" style="font-size: 1rem; padding: 0.9rem 2rem;">
          <span>💬 Chat on WhatsApp (<?= ADMIN_PHONE ?>)</span>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Fullscreen Lightbox Modal -->
<div id="lightbox-modal" class="lightbox-modal" role="dialog" aria-modal="true">
  <div class="lightbox-content">
    <button id="lightbox-close" class="lightbox-close" aria-label="Close Lightbox">&times;</button>
    <button id="lightbox-prev" class="lightbox-nav-btn lightbox-prev" aria-label="Previous Image">&#10094;</button>
    <img id="lightbox-image" src="" alt="" />
    <button id="lightbox-next" class="lightbox-nav-btn lightbox-next" aria-label="Next Image">&#10095;</button>
    <div id="lightbox-caption-text" class="lightbox-caption"></div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
