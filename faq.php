<?php
/**
 * Frequently Asked Questions
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Frequently Asked Questions (FAQ) | Expo Tree Exhibitions';
$pageDesc = 'Answers to all common questions for stall exhibitors and visitors attending Expo Tree Exhibitions in Delhi NCR. Call or WhatsApp +' . ADMIN_PHONE . '.';
$currentPage = 'faq';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Header -->
<section class="section page-hero-section">
  <div class="hero-bg-photo" aria-hidden="true" style="opacity: 0.16;"></div>
  <div class="container" style="position: relative; z-index: 2;">
    <span class="section-badge badge-gold">✦ Knowledge &amp; Support</span>
    <h1 class="section-title title-white">Frequently Asked Questions</h1>
    <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>
    <p class="section-subtitle subtitle-white" style="max-width: 800px; margin: 0 auto;">
      Clear, transparent answers for exhibitors booking stalls and shoppers looking to attend our festive melas across Delhi NCR.
    </p>
  </div>
</section>

<!-- FAQ Interactive Accordion -->
<section id="faq" class="section faq-section">
  <div class="container">
    <div class="faq-tabs">
      <button class="faq-tab-btn active" data-faq-tab="exhibitors">🎪 For Exhibitors (Stall Booking)</button>
      <button class="faq-tab-btn" data-faq-tab="shoppers">🛍️ For Shoppers (Visitors)</button>
    </div>

    <div class="faq-accordion-container">
      <!-- Exhibitors FAQ -->
      <div class="faq-list-tab" data-faq-content="exhibitors">
        <div class="faq-item active">
          <button class="faq-question">
            <span>What are the standard stall sizes and inclusions?</span>
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            Our standard canopies are usually 3x3 meters (or 2x2m depending on the mall venue). Every stall includes premium fabric draping, 2 cushioned chairs, 1 or 2 draped display tables, standard LED focus lights, power points, a printed fascia nameboard, and continuous security.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question">
            <span>How do I book a stall and reserve my spot?</span>
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            You can submit your stall preferences using our online <strong>Book a Stall</strong> studio or directly WhatsApp/call our booking hotline at <strong><?= ADMIN_PHONE ?></strong>. Our team will share the floor layout blueprint, stall numbering, and booking confirmation. Stalls are allotted on a first-come, first-served basis.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question">
            <span>Do you offer marketing support for participating brands?</span>
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            Yes! Every confirmed exhibitor is featured on our official Instagram page (<?= INSTAGRAM_FOLLOWERS ?> engaged followers) through reels and stories before the event. We also run targeted local meta ads and distribute brochures in surrounding luxury societies.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question">
            <span>When can exhibitors set up and dismantle their stalls?</span>
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            Exhibitors receive setup access the evening prior to the exhibition date (usually from 9:00 PM onwards) or early morning by 8:30 AM on opening day. Dismantling begins after 10:00 PM on Sunday evening.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question">
            <span>Are power points and high-wattage connections available?</span>
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            Standard electrical points suitable for lights and POS machines are provided. High-wattage 15A points for baking ovens, deep fryers, or heavy irons can be arranged with advance notice.
          </div>
        </div>
      </div>

      <!-- Shoppers FAQ -->
      <div class="faq-list-tab" data-faq-content="shoppers" style="display: none;">
        <div class="faq-item active">
          <button class="faq-question">
            <span>Is entry free for shoppers and families?</span>
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            Yes, entry is 100% free for everyone! You do not need to buy any tickets. You can generate a free VIP Digital Pass on our website to enjoy priority entrance and participate in our ₹5,000 Festive Lucky Draw.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question">
            <span>What are the exhibition dates and timings?</span>
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            Most of our lifestyle and festive exhibitions run over Saturday and Sunday from 11:00 AM to 10:00 PM. Check our <strong>Upcoming Exhibitions</strong> calendar for exact dates of each mela.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question">
            <span>Is parking available at the venue?</span>
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            Yes! Because our venues are premier shopping malls and star hotels (such as DLF Mall of India, Ambience Mall, Radisson Blu), multi-level covered parking, valet service, and wheelchair-accessible elevators are readily available.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question">
            <span>Can I pay via UPI (Google Pay, PhonePe) or Credit Card?</span>
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            Yes! All participating exhibitors accept UPI, debit/credit cards, and cash. High-speed connectivity is available throughout the mall premises.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question">
            <span>How does the ₹5,000 Lucky Draw work?</span>
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            When you generate your Free VIP Shopper Pass online, your unique Pass ID is automatically added to our lucky draw pool. Winners are announced at the exhibition and receive ₹5,000 shopping vouchers to spend at any stall!
          </div>
        </div>
      </div>
    </div>

    <!-- Still Have Questions Callout -->
    <div style="margin-top: 4rem; text-align: center; background: #ffffff; border: 1.5px solid var(--border-gold); border-radius: 20px; padding: 2.5rem; max-width: 700px; margin-left: auto; margin-right: auto; box-shadow: var(--shadow-card);">
      <h3 style="font-family: var(--font-cinzel); font-size: 1.5rem; color: var(--burgundy-950); margin-bottom: 0.6rem;">Still Have a Question?</h3>
      <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem;">
        Our organizing team is available 7 days a week to answer your stall booking and visitor inquiries.
      </p>
      <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
        <a href="tel:<?= ADMIN_PHONE ?>" class="btn btn-burgundy">
          <span>📞 Call <?= ADMIN_PHONE ?></span>
        </a>
        <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp">
          <span>💬 Chat on WhatsApp</span>
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
