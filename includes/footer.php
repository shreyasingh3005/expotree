<?php
/**
 * Common Footer Component
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Fetch a recent booking for live social proof toast
try {
    $recentStmt = $pdo->query("
        SELECT b.customer_name, b.stall_type, e.title as event_title, e.city as event_city 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        WHERE b.status IN ('confirmed', 'pending')
        ORDER BY b.created_at DESC 
        LIMIT 1
    ");
    $recentBooking = $recentStmt->fetch();
} catch (Exception $e) {
    $recentBooking = null;
}
?>
  </main><!-- /#main-content -->

  <!-- ==========================================================================
       MAIN FOOTER
       ========================================================================== -->
  <footer class="main-footer" id="site-footer">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="<?= BASE_URL ?>/index.php" style="display:inline-block; margin-bottom: 1rem;">
          <img src="<?= BASE_URL ?>/images/logo.svg" alt="Expo Tree Exhibitions" width="180" />
        </a>
        <p class="footer-desc">
          Connecting homegrown businesses, boutique designers, and artisanal creators with eager shoppers across Delhi NCR through curated lifestyle &amp; festive exhibitions.
        </p>
        <div style="margin-top: 1.25rem; display: flex; gap: 0.75rem; align-items: center;">
          <a href="https://www.instagram.com/<?= INSTAGRAM_HANDLE ?>/" target="_blank" rel="noopener noreferrer" style="color: var(--gold-300); font-size: 0.88rem; text-decoration: none; display: flex; align-items: center; gap: 0.4rem; background: rgba(212,175,55,0.12); padding: 0.4rem 0.8rem; border-radius: 20px; border: 1px solid rgba(212,175,55,0.3);">
            <span>📸 Instagram</span>
            <strong>@<?= INSTAGRAM_HANDLE ?></strong>
            <span style="font-size: 0.75rem; background: var(--gold-500); color: #1a0408; padding: 0.1rem 0.4rem; border-radius: 10px; font-weight: 700;"><?= INSTAGRAM_FOLLOWERS ?></span>
          </a>
        </div>
      </div>

      <div>
        <div class="footer-heading">Quick Links</div>
        <ul class="footer-links">
          <li><a href="<?= BASE_URL ?>/index.php">Home</a></li>
          <li><a href="<?= BASE_URL ?>/upcoming-exhibitions.php">Upcoming Exhibitions</a></li>
          <li><a href="<?= BASE_URL ?>/categories.php">16 Categories We Host</a></li>
          <li><a href="<?= BASE_URL ?>/book-a-stall.php">Book a Stall (Exhibitors)</a></li>
          <li><a href="<?= BASE_URL ?>/list-your-event.php">List Your Event (Organizers)</a></li>
          <li><a href="<?= BASE_URL ?>/free-shopper-pass.php">Free Shopper VIP Pass</a></li>
          <li><a href="<?= BASE_URL ?>/gallery.php">Real Photo Gallery</a></li>
          <li><a href="<?= BASE_URL ?>/about-us.php">About Us</a></li>
          <li><a href="<?= BASE_URL ?>/faq.php">Frequently Asked Questions</a></li>
          <li><a href="<?= BASE_URL ?>/contact.php">Contact Us</a></li>
          <li><a href="<?= BASE_URL ?>/admin/login.php">Organizer &amp; Admin Login</a></li>
        </ul>
      </div>

      <div>
        <div class="footer-heading">Coverage Hubs</div>
        <ul class="footer-links">
          <li><a href="<?= BASE_URL ?>/upcoming-exhibitions.php?city=gurugram">Gurugram Exhibitions</a></li>
          <li><a href="<?= BASE_URL ?>/upcoming-exhibitions.php?city=noida">Noida Exhibitions</a></li>
          <li><a href="<?= BASE_URL ?>/upcoming-exhibitions.php?city=delhi">Delhi Exhibitions</a></li>
          <li><a href="<?= BASE_URL ?>/upcoming-exhibitions.php?city=faridabad">Faridabad Exhibitions</a></li>
          <li><a href="<?= BASE_URL ?>/upcoming-exhibitions.php?city=ghaziabad">Ghaziabad Exhibitions</a></li>
          <li><a href="<?= BASE_URL ?>/upcoming-exhibitions.php?city=greater%20noida">Greater Noida Exhibitions</a></li>
        </ul>
      </div>

      <div>
        <div class="footer-heading">Direct Hotline</div>
        <p style="font-size: 0.9rem; margin-bottom: 0.6rem; color: #ffffff;">
          <strong>Phone:</strong> <a href="tel:<?= ADMIN_PHONE ?>" style="color: var(--gold-300); text-decoration: none; font-weight: 600;"><?= ADMIN_PHONE ?></a>
        </p>
        <p style="font-size: 0.9rem; margin-bottom: 0.6rem; color: #ffffff;">
          <strong>WhatsApp:</strong> <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>" target="_blank" rel="noopener noreferrer" style="color: #25D366; text-decoration: none; font-weight: 600;">+91 <?= ADMIN_WHATSAPP ?></a>
        </p>
        <p style="font-size: 0.85rem; color: #e2c0c7; margin-bottom: 1rem;">
          <strong>Email:</strong> <a href="mailto:<?= ADMIN_EMAIL ?>" style="color: inherit; text-decoration: none;"><?= ADMIN_EMAIL ?></a>
        </p>
        <a href="<?= BASE_URL ?>/book-a-stall.php" class="btn btn-gold" style="width: 100%; text-align: center; justify-content: center;">
          <span>🎪 Stall Booking Partner</span>
        </a>
      </div>
    </div>

    <div class="footer-bottom">
      <div>© <?= date('Y') ?> Expo Tree Exhibitions. All Rights Reserved.</div>
      <div class="footer-bottom-links">
        <a href="<?= BASE_URL ?>/privacy-policy.php">Privacy Policy</a>
        <a href="<?= BASE_URL ?>/terms.php">Terms &amp; Conditions</a>
        <a href="<?= BASE_URL ?>/admin/index.php">Admin Portal</a>
      </div>
    </div>
  </footer>

  <!-- ==========================================================================
       LIVE SOCIAL PROOF POPUP NOTIFICATION
       ========================================================================== -->
  <div id="live-social-toast" class="live-social-toast" role="status" aria-live="polite">
    <div class="toast-badge-icon" id="toast-icon">🎉</div>
    <div class="toast-info">
      <div class="toast-msg" id="toast-text">
        <?php if ($recentBooking): ?>
          <strong><?= e($recentBooking['customer_name']) ?></strong> just booked a <em><?= e($recentBooking['stall_type']) ?></em> for <?= e($recentBooking['event_city']) ?>!
        <?php else: ?>
          <strong>Pooja Verma</strong> (Gurugram) just booked a Corner Shopping Stall!
        <?php endif; ?>
      </div>
      <div class="toast-time" id="toast-time">Just now • Verified Booking</div>
    </div>
    <button class="toast-close-btn" id="toast-close-btn" aria-label="Close notification">&times;</button>
  </div>

  <!-- ==========================================================================
       PULSING FLOATING WHATSAPP BUTTON (HOTLINE: 9811175057)
       ========================================================================== -->
  <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>?text=<?= urlencode('Hello Expo Tree! I want to inquire about upcoming exhibitions and stall booking.') ?>" target="_blank" rel="noopener noreferrer" class="floating-whatsapp-btn" aria-label="Chat directly on WhatsApp with Expo Tree Team" title="Chat on WhatsApp">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  </a>

  <!-- ==========================================================================
       MOBILE BOTTOM STICKY ACTION BAR
       ========================================================================== -->
  <div class="mobile-sticky-bar">
    <div class="mobile-bar-actions">
      <a href="tel:<?= ADMIN_PHONE ?>" class="mobile-action-btn call" aria-label="Call Helpline">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <span>Call</span>
      </a>
      <a href="<?= BASE_URL ?>/book-a-stall.php" class="mobile-action-btn book" aria-label="Book Stall">
        <span>🎪 Book Stall</span>
      </a>
      <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>?text=<?= urlencode('Hello Expo Tree! I want to book a stall for upcoming exhibition.') ?>" target="_blank" rel="noopener noreferrer" class="mobile-action-btn whatsapp" aria-label="WhatsApp Us">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
        <span>WhatsApp</span>
      </a>
    </div>
  </div>

  <!-- Scripts -->
  <script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>
