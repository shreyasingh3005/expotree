<?php
/**
 * Privacy Policy
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Privacy Policy | Expo Tree Exhibitions';
$pageDesc = 'Privacy Policy for Expo Tree Exhibitions visitors and exhibitors.';
$currentPage = 'privacy-policy';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Legal Body -->
  <section class="section" style="background: var(--bg-ivory); min-height: 70vh;">
    <div class="container" style="max-width: 850px;">
      <div style="background: #ffffff; border: 1.5px solid var(--border-gold); border-radius: 24px; padding: 3rem; box-shadow: var(--shadow-card);">
        <h1 style="font-family: var(--font-cinzel); font-size: 2.2rem; color: var(--burgundy-950); margin-bottom: 1.5rem;">Privacy Policy</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.5rem;">
          Last Updated: 2026. <strong>Expo Tree Exhibitions</strong> is committed to honoring and safeguarding the privacy of our visitors, exhibitors, and partners.
        </p>

        <h3 style="color: var(--burgundy-900); font-size: 1.2rem; margin-top: 1.5rem; margin-bottom: 0.5rem;">1. Information We Collect</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1rem;">
          We collect personal identification information including your Name, Mobile / WhatsApp Number, City, and Brand Name when you voluntarily register for a Free Shopper Pass or request a stall booking quotation.
        </p>

        <h3 style="color: var(--burgundy-900); font-size: 1.2rem; margin-top: 1.5rem; margin-bottom: 0.5rem;">2. Use of Information</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1rem;">
          Your data is solely used to process stall allotments, deliver event reminders, send digital VIP passes, and register your participation in the ₹5,000 Festive Lucky Draw.
        </p>

        <h3 style="color: var(--burgundy-900); font-size: 1.2rem; margin-top: 1.5rem; margin-bottom: 0.5rem;">3. Data Protection &amp; Contact</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.5rem;">
          We never sell, rent, or lease our attendee or exhibitor contact information to third parties. For any privacy requests, please contact our helpline at <a href="tel:<?= ADMIN_PHONE ?>" style="color: var(--burgundy-700); font-weight: 700;">+91 <?= ADMIN_PHONE ?></a>.
        </p>

        <a href="index.php" class="btn btn-burgundy">
          <span>← Return to Home</span>
        </a>
      </div>
    </div>
  </section>

  

<?php require_once __DIR__ . '/includes/footer.php'; ?>
