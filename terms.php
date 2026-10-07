<?php
/**
 * Terms & Conditions
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Terms & Conditions | Expo Tree Exhibitions';
$pageDesc = 'Terms and Conditions for stall bookings and exhibition attendance.';
$currentPage = 'terms';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Legal Body -->
  <section class="section" style="background: var(--bg-ivory); min-height: 70vh;">
    <div class="container" style="max-width: 850px;">
      <div style="background: #ffffff; border: 1.5px solid var(--border-gold); border-radius: 24px; padding: 3rem; box-shadow: var(--shadow-card);">
        <h1 style="font-family: var(--font-cinzel); font-size: 2.2rem; color: var(--burgundy-950); margin-bottom: 1.5rem;">Terms &amp; Conditions</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.5rem;">
          Welcome to <strong>Expo Tree Exhibitions</strong>. By booking a stall or visiting our exhibitions, you acknowledge and agree to the following terms and operational guidelines:
        </p>

        <h3 style="color: var(--burgundy-900); font-size: 1.2rem; margin-top: 1.5rem; margin-bottom: 0.5rem;">1. Stall Allotment &amp; Booking Advances</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1rem;">
          Stall allocations (Corner, Island, Canopy, Table) are confirmed only upon receipt of the agreed booking advance. Spaces are allocated based on category balance and floor plan availability.
        </p>

        <h3 style="color: var(--burgundy-900); font-size: 1.2rem; margin-top: 1.5rem; margin-bottom: 0.5rem;">2. Visitor Entry &amp; Lucky Draw Rules</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1rem;">
          Entry to our exhibitions is free. Lucky draw vouchers are subject to event-day presence and verification. Organizers reserve the right to verify registration authenticity.
        </p>

        <h3 style="color: var(--burgundy-900); font-size: 1.2rem; margin-top: 1.5rem; margin-bottom: 0.5rem;">3. Venue Safety &amp; Inquiries</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.5rem;">
          All exhibitors and visitors are expected to maintain orderly conduct in accordance with venue mall regulations. For any disputes or questions, reach our hotline at <a href="tel:<?= ADMIN_PHONE ?>" style="color: var(--burgundy-700); font-weight: 700;">+91 <?= ADMIN_PHONE ?></a>.
        </p>

        <a href="index.php" class="btn btn-burgundy">
          <span>← Return to Home</span>
        </a>
      </div>
    </div>
  </section>

  

<?php require_once __DIR__ . '/includes/footer.php'; ?>
