<?php
/**
 * Contact Us Page
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact Us | Expo Tree Exhibitions Helpline ' . ADMIN_PHONE;
$pageDesc = 'Get in touch with Expo Tree Exhibitions team for stall bookings, venue partnerships, or shopper passes. Call or WhatsApp +' . ADMIN_PHONE . ' or visit us in Delhi NCR.';
$currentPage = 'contact';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Header -->
<section class="section page-hero-section">
  <div class="hero-bg-photo" aria-hidden="true" style="opacity: 0.16;"></div>
  <div class="container" style="position: relative; z-index: 2;">
    <span class="section-badge badge-gold">✦ Get in Touch</span>
    <h1 class="section-title title-white">Contact Expo Tree Exhibitions</h1>
    <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>
    <p class="section-subtitle subtitle-white" style="max-width: 800px; margin: 0 auto;">
      Whether you are an exhibitor looking to book a prime stall, a mall partner wanting to host our next exhibition, or a visitor with an inquiry, we are here to assist you.
    </p>
  </div>
</section>

<!-- Contact Details & Form Section -->
<section id="contact" class="section contact-section">
  <div class="container">
    <div class="contact-grid">
      
      <!-- Left: Contact Details Card -->
      <div class="contact-info-card">
        <div class="contact-item">
          <div class="contact-icon-bubble">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <div class="contact-details">
            <h5>Primary Booking Hotline (Call &amp; WhatsApp)</h5>
            <p><a href="tel:<?= ADMIN_PHONE ?>">+91 <?= ADMIN_PHONE ?></a></p>
            <p><a href="https://wa.me/<?= ADMIN_WHATSAPP ?>" target="_blank" rel="noopener noreferrer" style="color: #25D366; font-weight: 600;">Chat on WhatsApp (Instant Response)</a></p>
          </div>
        </div>

        <div class="contact-item">
          <div class="contact-icon-bubble">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </div>
          <div class="contact-details">
            <h5>Official Instagram Channel</h5>
            <p><a href="https://www.instagram.com/<?= INSTAGRAM_HANDLE ?>/" target="_blank" rel="noopener noreferrer">@<?= INSTAGRAM_HANDLE ?></a></p>
            <p style="font-size: 0.85rem; color: var(--gold-300);"><?= INSTAGRAM_FOLLOWERS ?> Active Followers Community</p>
          </div>
        </div>

        <div class="contact-item">
          <div class="contact-icon-bubble">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </div>
          <div class="contact-details">
            <h5>Operational Hubs</h5>
            <p>Gurugram • Noida • South &amp; Central Delhi • Faridabad • Ghaziabad • Greater Noida</p>
          </div>
        </div>

        <div style="margin-top: 2rem; display: flex; flex-direction: column; gap: 0.8rem;">
          <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>?text=<?= urlencode('Hello Expo Tree! I want to enquire about upcoming exhibitions and stall booking.') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp" style="width: 100%;">
            <span>💬 Instant WhatsApp Chat (<?= ADMIN_PHONE ?>)</span>
          </a>
          <a href="tel:<?= ADMIN_PHONE ?>" class="btn btn-outline-gold" style="width: 100%;">
            <span>📞 Direct Call Hotline</span>
          </a>
        </div>
      </div>

      <!-- Right: Instagram Community Card -->
      <div class="instagram-showcase-card">
        <div class="instagram-handle">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          <span>@<?= INSTAGRAM_HANDLE ?></span>
        </div>
        <div class="instagram-followers-count"><?= INSTAGRAM_FOLLOWERS ?></div>
        <div class="instagram-subtag">Followers on Instagram Community</div>

        <div class="instagram-posts-preview-grid">
          <div class="insta-thumb">
            <img src="https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=400&q=80" alt="Insta Post 1" loading="lazy" />
          </div>
          <div class="insta-thumb">
            <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=400&q=80" alt="Kundan Jewellery Exhibition" loading="lazy" />
          </div>
          <div class="insta-thumb">
            <img src="https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=400&q=80" alt="Bridal Festive Couture Trunk Show" loading="lazy" />
          </div>
        </div>

        <a href="https://www.instagram.com/<?= INSTAGRAM_HANDLE ?>/" target="_blank" rel="noopener noreferrer" class="btn btn-outline-gold" style="width: 100%; margin-bottom: 0.75rem;">
          <span>Follow on Instagram (@<?= INSTAGRAM_HANDLE ?>)</span>
        </a>

        <a href="book-a-stall.php" class="btn btn-gold" style="width: 100%;">
          <span>🎪 Book a Stall for Next Mela</span>
        </a>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
