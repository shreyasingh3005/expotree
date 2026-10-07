<?php
/**
 * Expo Tree Exhibitions - Homepage
 * Curates and organizes premium lifestyle & festive exhibitions across Delhi NCR
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Expo Tree Exhibitions | Delhi NCR Premier Lifestyle & Festive Exhibitions';
$pageDesc = 'Shop, Explore, Support, Indulge. Delhi NCR premier lifestyle & festive exhibitions organizer across Gurugram, Noida, Delhi. Book stalls or claim free shopper pass. Hotline: 9811175057';
$currentPage = 'index';

// Fetch top upcoming active events from MySQL
$today = date('Y-m-d');
$homeEventsStmt = $pdo->prepare("SELECT * FROM events WHERE status IN ('published', 'active') AND end_date >= ? ORDER BY start_date ASC LIMIT 12");
$homeEventsStmt->execute([$today]);
$homeEvents = $homeEventsStmt->fetchAll();

// Fallback if no future events exist
if (empty($homeEvents)) {
    $homeEventsStmt = $pdo->query("SELECT * FROM events WHERE status IN ('published', 'active') ORDER BY start_date ASC LIMIT 12");
    $homeEvents = $homeEventsStmt->fetchAll();
}

// Prepare JSON payload for frontend script
$expoEventsForJs = array_map(function($ev) {
    $start = strtotime($ev['start_date']);
    $end = strtotime($ev['end_date']);
    $dayText = date('d', $start) . ($ev['end_date'] !== $ev['start_date'] ? '-' . date('d', $end) : '');
    $monthText = date('M Y', $start);
    $minPrice = $ev['daily_stall_price'] ?? 4000;
    if (!empty($ev['price_shopping_table1'])) $minPrice = min($minPrice, $ev['price_shopping_table1']);
    if (!empty($ev['price_shopping_canopy'])) $minPrice = min($minPrice, $ev['price_shopping_canopy']);

    $availStalls = (int)($ev['available_stalls'] ?? 0);
    $totStalls = (int)($ev['total_stalls'] ?? 20);

    return [
        'id' => (string)$ev['id'],
        'slug' => $ev['slug'] ?? '',
        'title' => $ev['title'],
        'date' => [
            'day' => $dayText,
            'month' => $monthText
        ],
        'date_display' => !empty($ev['date_display']) ? $ev['date_display'] : ($dayText . ' ' . $monthText),
        'startDate' => $ev['start_date'],
        'city' => $ev['city'],
        'venue' => $ev['venue'],
        'category' => !empty($ev['category']) ? $ev['category'] : 'Lifestyle & Festive Mela',
        'timing' => !empty($ev['timings']) ? $ev['timings'] : '11:00 AM – 9:00 PM',
        'expectedFootfall' => !empty($ev['footfall']) ? $ev['footfall'] : '20,000+ Footfall',
        'image' => !empty($ev['image_url']) ? $ev['image_url'] : 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=800&q=80',
        'tagline' => !empty($ev['category']) ? $ev['category'] : 'Lifestyle & Festive Mela',
        'minPrice' => formatPrice($minPrice),
        'rawMinPrice' => (float)$minPrice,
        'availableStalls' => $availStalls,
        'totalStalls' => $totStalls,
        'highlights' => [
            ($availStalls > 0 ? $availStalls . ' Stalls Open' : 'Filling Fast'),
            'Central AC Pavilion',
            'Free Shopper Entry',
            '88.9K Insta Promotion'
        ],
        'detail_url' => BASE_URL . '/event.php?id=' . $ev['id'] . '&slug=' . urlencode($ev['slug'] ?? ''),
        'book_url' => BASE_URL . '/book-a-stall.php?event_id=' . $ev['id']
    ];
}, $homeEvents);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Pass Dynamic Database Events to JavaScript -->
<script>
  window.EXPO_EVENTS = <?= json_encode($expoEventsForJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>

<main id="main-content">

  <!-- ==========================================================================
       HERO SECTION — CLEAN, ELEGANT & USER-FRIENDLY
       ========================================================================== -->
  <section id="home" class="hero-section" style="padding: 4.5rem 1.5rem 3.5rem;">
    <!-- Backdrop & Mandala -->
    <div class="hero-bg-photo" aria-hidden="true"></div>
    <div class="hero-mandala-bg" aria-hidden="true"></div>

    <div class="hero-content">
      <!-- Live Pulse Badge -->
      <div class="hero-top-badge">
        <span class="live-pulse-dot"></span>
        <span>Delhi NCR's Premier Exhibition &amp; Stall Booking Platform</span>
      </div>

      <h1 class="hero-title" style="margin-bottom: 0.85rem;">
        Festive Melas &amp; Lifestyle Exhibitions
      </h1>

      <p class="hero-subtitle" style="font-size: 1.25rem; margin-bottom: 1rem;">
        Shop Handcrafted Wonders • Book Prime Footfall Stalls
      </p>

      <p class="hero-description" style="max-width: 720px; font-size: 1.05rem; margin-bottom: 2.25rem;">
        Curating high-energy festive exhibitions across Gurugram, Noida, and Delhi. Connecting 100+ boutique designers, handcrafted artisans, and home-grown brands with thousands of eager shoppers.
      </p>

      <!-- Clear Dual CTA Buttons -->
      <div class="hero-cta-wrapper" style="margin-bottom: 2.5rem;">
        <a href="#upcoming" class="btn btn-gold" style="font-size: 1.05rem; padding: 0.95rem 2.2rem;">
          <span>🎪 Explore Upcoming Melas</span>
        </a>

        <a href="#booking-studio" class="btn btn-outline-gold" style="font-size: 1.05rem; padding: 0.95rem 2.2rem;">
          <span>🎟️ Book a Stall (Exhibitors)</span>
        </a>

        <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>?text=<?= urlencode('Hello Expo Tree! I want to inquire about upcoming exhibitions and stall bookings.') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp" style="font-size: 1.05rem; padding: 0.95rem 1.8rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
          <span>WhatsApp Hotline</span>
        </a>
      </div>

      <!-- 4 Key Trust Pillars Ribbon -->
      <div class="hero-shopper-perks-bar">
        <div class="hero-perk-item">
          <div class="hero-perk-icon">🎟️</div>
          <div class="hero-perk-text">
            <h4>100% Free Entry</h4>
            <p>Free VIP passes for shoppers</p>
          </div>
        </div>

        <div class="hero-perk-item">
          <div class="hero-perk-icon">🏬</div>
          <div class="hero-perk-text">
            <h4>Luxury Venues</h4>
            <p>Air-conditioned malls &amp; clubs</p>
          </div>
        </div>

        <div class="hero-perk-item">
          <div class="hero-perk-icon">🛍️</div>
          <div class="hero-perk-text">
            <h4>100+ Handpicked Brands</h4>
            <p>Festive collections &amp; discounts</p>
          </div>
        </div>

        <div class="hero-perk-item">
          <div class="hero-perk-icon">👥</div>
          <div class="hero-perk-text">
            <h4>88.9K+ Community</h4>
            <p>@expo_tree_exhibitions</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       AUDIENCE QUICK-CHOICE STRIP ("Choose Your Experience")
       ========================================================================== -->
  <section class="audience-choice-section" aria-label="Audience Quick Access">
    <div class="container">
      <div class="audience-choice-grid">
        <!-- Shopper Card -->
        <div class="audience-card audience-card-shopper">
          <div class="audience-card-top">
            <span class="audience-badge audience-badge-gold">🛍️ For Shoppers &amp; Visitors</span>
            <h3>Looking to Visit Our Exhibitions?</h3>
            <p>Enjoy a vibrant weekend of festive shopping with family &amp; friends. Discover unique handcrafted jewellery, festive couture, fragrant candles, home decor, and artisanal bakery not found in regular stores!</p>
            <ul class="audience-perks-list">
              <li>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span><strong>100% Free VIP Entry</strong> — No entry tickets needed</span>
              </li>
              <li>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span><strong>₹5,000 Lucky Draw Entry</strong> on every registered pass</span>
              </li>
              <li>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Central air-conditioned mall venues with ample parking</span>
              </li>
            </ul>
          </div>
          <div class="audience-btn-group">
            <a href="#shopper-pass" class="btn btn-gold">
              <span>🎟️ Claim Free VIP Pass</span>
            </a>
            <a href="#upcoming" class="btn btn-outline-gold">
              <span>📅 Browse Melas</span>
            </a>
          </div>
        </div>

        <!-- Exhibitor Card -->
        <div class="audience-card audience-card-exhibitor">
          <div class="audience-card-top">
            <span class="audience-badge audience-badge-green">🎪 For Brands &amp; Artisans</span>
            <h3>Looking to Book an Exhibition Stall?</h3>
            <p>Showcase your products directly to 20,000+ affluent shoppers in Delhi NCR's most prestigious malls. Prime 2-side open corner canopies and boutique table setups available.</p>
            <ul class="audience-perks-list">
              <li>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span><strong>Guaranteed Footfall</strong> in top Gurugram &amp; Noida malls</span>
              </li>
              <li>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span><strong>Social Media Spotlight</strong> on 88.9K+ Instagram page</span>
              </li>
              <li>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Draped tables, chairs, LED spotlights &amp; 15A power included</span>
              </li>
            </ul>
          </div>
          <div class="audience-btn-group">
            <a href="#booking-studio" class="btn btn-gold">
              <span>🎪 Quick Stall Enquiry</span>
            </a>
            <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>?text=<?= urlencode('Hello Expo Tree! I am an exhibitor and want to book a stall for upcoming exhibition.') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp">
              <span>💬 WhatsApp Quote</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       ROTATING UPCOMING EXHIBITIONS TICKER STRIP
       ========================================================================== -->
  <div class="upcoming-ticker-strip" aria-label="Upcoming Events Ticker">
    <div class="ticker-badge-fixed">
      <span>⚡ Upcoming Melas</span>
    </div>
    <div class="ticker-track">
      <?php foreach ($homeEvents as $hev): ?>
        <div class="ticker-item">
          <span class="ticker-item-date"><?= date('d M', strtotime($hev['start_date'])) ?></span>
          <span class="ticker-item-venue"><?= e($hev['title']) ?> • <?= e($hev['venue']) ?></span>
          <span class="ticker-item-city">[<?= e($hev['city']) ?>]</span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ==========================================================================
       UPCOMING EXHIBITIONS CALENDAR & FILTERABLE GRID
       ========================================================================== -->
  <section id="upcoming" class="section upcoming-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge badge-burgundy">Upcoming Exhibitions</span>
        <h2 class="section-title title-burgundy">Explore Festive Melas &amp; Popups</h2>
        <div class="festive-divider">
          <span class="festive-divider-icon">✦</span>
        </div>
        <p class="section-subtitle">
          Plan your weekend visit or reserve your brand stall across Delhi NCR's top venues. Filter by city below:
        </p>
      </div>

      <!-- Search & Filters Toolbar -->
      <div class="home-filters-toolbar" style="max-width: 900px; margin: 0 auto 1.75rem;">
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem;">
          <div style="flex: 1; min-width: 260px; position: relative;">
            <input type="text" id="home-event-search" placeholder="🔍 Search by exhibition name, mall or landmark..." style="width: 100%; padding: 0.75rem 1.25rem; border: 1.5px solid var(--border-gold); border-radius: 30px; font-size: 0.95rem; outline: none; box-shadow: 0 2px 6px rgba(0,0,0,0.04); background: #ffffff;">
          </div>
          <div style="min-width: 200px;">
            <select id="home-event-sort" style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid var(--border-gold); border-radius: 30px; font-size: 0.92rem; background: #fff; cursor: pointer; border: 1.5px solid var(--border-gold);">
              <option value="date_asc">📅 Date: Earliest First</option>
              <option value="price_asc">💰 Price: Low to High</option>
              <option value="price_desc">💎 Price: High to Low</option>
              <option value="stalls">🎪 Most Stalls Open</option>
            </select>
          </div>
        </div>

        <!-- City Filter Tabs -->
        <div class="filter-tabs-wrapper" style="justify-content: center;">
          <button class="filter-btn active" data-city="all">All Cities</button>
          <button class="filter-btn" data-city="gurugram">Gurugram</button>
          <button class="filter-btn" data-city="noida">Noida</button>
          <button class="filter-btn" data-city="delhi">Delhi</button>
          <button class="filter-btn" data-city="faridabad">Faridabad</button>
          <button class="filter-btn" data-city="ghaziabad">Ghaziabad</button>
          <button class="filter-btn" data-city="greater noida">Greater Noida</button>
        </div>
      </div>

      <!-- Grid Container (Handled by JS via window.EXPO_EVENTS for seamless city filtering) -->
      <div id="events-grid-container" class="events-grid">
        <!-- Rendered dynamically by main.js with high fidelity -->
      </div>

      <div style="text-align: center; margin-top: 2.75rem;">
        <a href="<?= BASE_URL ?>/upcoming-exhibitions.php" class="btn btn-gold" style="font-size: 1rem; padding: 0.9rem 2.2rem;">
          <span>✦ View All 20+ Scheduled Exhibitions</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       SHOPPER ATTRACTION & FREE VIP PASS GENERATOR
       ========================================================================== -->
  <section id="shopper-pass" class="section shopper-attraction-section">
    <div class="container">
      <div class="shopper-grid">
        <!-- Left: Perks Information -->
        <div class="shopper-info">
          <span class="section-badge badge-gold">For Shoppers &amp; Visitors</span>
          <h3>Visit Delhi NCR's Most Loved Festive Exhibitions — Free Entry!</h3>
          <p>
            Looking for unique jewellery, festive fashion, handcrafted candles, or organic skincare you won't find in ordinary malls? Step into Expo Tree Exhibitions! Enjoy an unforgettable shopping weekend with family &amp; friends.
          </p>

          <div class="shopper-perks-list">
            <div class="perk-card">
              <div class="perk-card-icon">🎟️</div>
              <div>
                <div class="perk-card-title">100% Free Entry For All</div>
                <div class="perk-card-desc">No tickets needed. Download your VIP digital pass for priority entry.</div>
              </div>
            </div>

            <div class="perk-card">
              <div class="perk-card-icon">🎁</div>
              <div>
                <div class="perk-card-title">₹5,000 Lucky Draw Entry</div>
                <div class="perk-card-desc">Every registered pass enters our hourly shopping voucher lucky draw!</div>
              </div>
            </div>

            <div class="perk-card">
              <div class="perk-card-icon">✨</div>
              <div>
                <div class="perk-card-title">100+ Homegrown Designers</div>
                <div class="perk-card-desc">Exclusive collections directly from independent creators &amp; boutiques.</div>
              </div>
            </div>

            <div class="perk-card">
              <div class="perk-card-icon">🏬</div>
              <div>
                <div class="perk-card-title">Luxury Venues &amp; Parking</div>
                <div class="perk-card-desc">Fully air-conditioned, metro-connected with ample mall parking.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Interactive Digital VIP Pass Generator -->
        <div class="pass-generator-wrapper">
          <div class="pass-generator-card">
            <div class="pass-header-stamp">
              <div class="stamp-title">VIP ENTRY PASS</div>
              <div class="stamp-tag">Free Pass + Lucky Draw</div>
            </div>

            <form id="shopper-pass-form">
              <div class="form-group">
                <label for="shopper-name">Your Full Name *</label>
                <input type="text" id="shopper-name" placeholder="e.g. Priya Sharma" required />
              </div>

              <div class="form-group">
                <label for="shopper-phone">WhatsApp Number (For Pass &amp; Entry Updates) *</label>
                <input type="tel" id="shopper-phone" placeholder="e.g. 98111XXXXX" pattern="[0-9]{10}" required />
              </div>

              <div class="form-group">
                <label for="shopper-city">Your City *</label>
                <select id="shopper-city">
                  <option value="Gurugram">Gurugram</option>
                  <option value="Noida">Noida</option>
                  <option value="Delhi">Delhi (South/North/East/West)</option>
                  <option value="Faridabad">Faridabad</option>
                  <option value="Ghaziabad">Ghaziabad</option>
                  <option value="Greater Noida">Greater Noida</option>
                </select>
              </div>

              <div class="form-group">
                <label for="shopper-event-select">Exhibition You Wish to Visit *</label>
                <select id="shopper-event-select">
                  <?php foreach ($homeEvents as $hev): ?>
                    <option value="<?= $hev['id'] ?>"><?= e($hev['title']) ?> (<?= e($hev['city']) ?>)</option>
                  <?php endforeach; ?>
                </select>
              </div>

              <button type="submit" class="btn btn-gold" style="width: 100%; padding: 1rem; font-size: 1.05rem; font-weight: 700; margin-top: 0.5rem;">
                <span>🎉 Claim Free VIP Pass &amp; Lucky Draw Entry</span>
              </button>

              <p class="pass-disclaimer">
                🔒 100% Free • No spam • Instant confirmation on WhatsApp
              </p>
            </form>

            <!-- Generated VIP Pass (Shown on Submit) -->
            <div id="generated-vip-pass" class="digital-vip-pass">
              <img src="images/mandala.svg" class="pass-watermark" alt="" />
              <div class="stamp-tag" style="display: inline-block; margin-bottom: 0.75rem;">OFFICIAL VIP VISITOR PASS</div>
              <div class="pass-holder-name" id="pass-holder-name-display">Priya Sharma</div>
              <div class="pass-id-badge" id="pass-id-number">PASS ID: EXPO-VIP-8842</div>
              
              <div class="pass-qr-box">
                <svg viewBox="0 0 100 100" width="100%" height="100%">
                  <rect width="100" height="100" fill="#ffffff" />
                  <rect x="10" y="10" width="25" height="25" fill="#3D0810" />
                  <rect x="15" y="15" width="15" height="15" fill="#ffffff" />
                  <rect x="18" y="18" width="9" height="9" fill="#3D0810" />
                  <rect x="65" y="10" width="25" height="25" fill="#3D0810" />
                  <rect x="70" y="15" width="15" height="15" fill="#ffffff" />
                  <rect x="73" y="18" width="9" height="9" fill="#3D0810" />
                  <rect x="10" y="65" width="25" height="25" fill="#3D0810" />
                  <rect x="15" y="70" width="15" height="15" fill="#ffffff" />
                  <rect x="18" y="73" width="9" height="9" fill="#3D0810" />
                  <rect x="42" y="15" width="15" height="8" fill="#3D0810" />
                  <rect x="42" y="30" width="8" height="15" fill="#3D0810" />
                  <rect x="42" y="52" width="16" height="16" fill="#3D0810" />
                  <rect x="65" y="45" width="10" height="20" fill="#3D0810" />
                  <rect x="80" y="70" width="10" height="10" fill="#3D0810" />
                </svg>
              </div>

              <p id="pass-event-name-display" style="font-size: 0.88rem; color: var(--gold-200); margin-bottom: 1rem; font-weight: 600;">
                Grand Diwali Carnival — Ambience Mall, Gurugram
              </p>

              <p style="font-size: 0.78rem; color: rgba(255, 255, 255, 0.8); margin-bottom: 1.25rem;">
                Show this pass on arrival to claim priority entry and participate in the <strong>₹5,000 Festive Lucky Draw</strong>!
              </p>

              <button id="save-pass-whatsapp-btn" class="btn btn-whatsapp" style="width: 100%; padding: 0.85rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                <span>Save Pass &amp; Get Reminders on WhatsApp</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       INTERACTIVE STALL BOOKING STUDIO (FOR EXHIBITORS)
       ========================================================================== -->
  <section id="booking-studio" class="section booking-studio-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge badge-gold">For Exhibitors &amp; Brands</span>
        <h2 class="section-title title-white">Interactive Stall Booking Studio</h2>
        <div class="festive-divider">
          <span class="festive-divider-icon">✦</span>
        </div>
        <p class="section-subtitle subtitle-white">
          Choose your preferred exhibition, stall configuration, and amenities. Instantly generate your customized booking enquiry to <strong><?= ADMIN_PHONE ?></strong>!
        </p>
      </div>

      <div class="booking-studio-layout">
        <!-- Left: Wizard Panel -->
        <div class="wizard-panel">
          <!-- Step 1: Exhibition Selection -->
          <div class="wizard-group">
            <div class="wizard-step-title">
              <span class="step-number-circle">1</span>
              <span>Select Target Exhibition &amp; City</span>
            </div>
            <label class="wizard-label" for="wizard-exhibition-select">Upcoming Exhibition:</label>
            <select id="wizard-exhibition-select" class="wizard-select">
              <?php foreach ($homeEvents as $hev): ?>
                <option value="<?= $hev['id'] ?>"><?= e($hev['title']) ?> (<?= e($hev['city']) ?>) — <?= date('d M', strtotime($hev['start_date'])) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Step 2: Brand Category & Name -->
          <div class="wizard-group">
            <div class="wizard-step-title">
              <span class="step-number-circle">2</span>
              <span>Your Brand &amp; Product Category</span>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
              <div>
                <label class="wizard-label" for="wizard-brand-name">Brand / Business Name:</label>
                <input type="text" id="wizard-brand-name" class="wizard-input" placeholder="e.g. Aura Jewels" />
              </div>
              <div>
                <label class="wizard-label" for="wizard-category-select">Select Category:</label>
                <select id="wizard-category-select" class="wizard-select">
                  <option value="Jewellery &amp; Accessories">Jewellery &amp; Accessories</option>
                  <option value="Handbags">Handbags</option>
                  <option value="Apparel &amp; Footwear">Apparel &amp; Footwear</option>
                  <option value="Gifting &amp; Home Décor">Gifting &amp; Home Décor</option>
                  <option value="Skincare &amp; Perfumes">Skincare &amp; Perfumes</option>
                  <option value="Crochet &amp; Handmade">Crochet &amp; Handmade</option>
                  <option value="Candles &amp; Soaps">Candles &amp; Soaps</option>
                  <option value="Wooden Toys">Wooden Toys</option>
                  <option value="Home Furnishings">Home Furnishings</option>
                  <option value="Bakery &amp; Dryfruits">Bakery &amp; Dryfruits</option>
                  <option value="Food Stalls">Food Stalls</option>
                  <option value="Handmade Products">Handmade Products</option>
                  <option value="Kidswear">Kidswear</option>
                  <option value="Tarot Card Reading">Tarot Card Reading</option>
                  <option value="Healing Products">Healing Products</option>
                  <option value="Toys &amp; Stationery">Toys &amp; Stationery</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Step 3: Stall Type Selection -->
          <div class="wizard-group">
            <div class="wizard-step-title">
              <span class="step-number-circle">3</span>
              <span>Choose Your Stall Configuration</span>
            </div>
            <div class="stall-options-grid">
              <div class="stall-type-card selected" data-stall="Prime Corner Canopy">
                <div class="stall-type-header">
                  <div class="stall-name">Prime Corner Canopy</div>
                  <div class="stall-badge">Most Popular</div>
                </div>
                <div class="stall-desc">2-Side Open corner canopy in highest footfall junction. Maximum visitor visibility and natural walk-in crowd.</div>
              </div>

              <div class="stall-type-card" data-stall="Center Island Booth">
                <div class="stall-type-header">
                  <div class="stall-name">Center Island Booth</div>
                  <div class="stall-badge">Hero Placement</div>
                </div>
                <div class="stall-desc">4-Side Open premium centerpiece booth in central atrium. 360-degree brand exposure and high-ticket customer attraction.</div>
              </div>

              <div class="stall-type-card" data-stall="Standard Canopy Stall">
                <div class="stall-type-header">
                  <div class="stall-name">Standard Canopy Stall</div>
                  <div class="stall-badge">High Traffic</div>
                </div>
                <div class="stall-desc">3x3m covered stall in main shopping corridor with complete fabric draping, 2 chairs, 1 draped table &amp; lights.</div>
              </div>

              <div class="stall-type-card" data-stall="Boutique Table Space">
                <div class="stall-type-header">
                  <div class="stall-name">Boutique Table Space</div>
                  <div class="stall-badge">Handmade / Startup</div>
                </div>
                <div class="stall-desc">Compact dedicated table setup ideal for handmade soaps, crochet, scented candles, tarot card readers &amp; healing crystals.</div>
              </div>
            </div>
          </div>

          <!-- Step 4: Amenities & Add-ons -->
          <div class="wizard-group">
            <div class="wizard-step-title">
              <span class="step-number-circle">4</span>
              <span>Optional Amenities &amp; Services</span>
            </div>
            <div class="addons-grid">
              <label class="addon-label-box">
                <input type="checkbox" class="addon-checkbox" data-name="15A Power Backup" checked />
                <span>15A High-Power Electrical Point</span>
              </label>
              <label class="addon-label-box">
                <input type="checkbox" class="addon-checkbox" data-name="Extra Display Table &amp; Spotlights" />
                <span>Extra Draped Table + 2 Spotlights</span>
              </label>
              <label class="addon-label-box">
                <input type="checkbox" class="addon-checkbox" data-name="Instagram Promotion (88.9K Page)" checked />
                <span>Feature on @expo_tree (88.9K)</span>
              </label>
              <label class="addon-label-box">
                <input type="checkbox" class="addon-checkbox" data-name="Fascia Brand Nameboard" checked />
                <span>Custom Printed Fascia Nameboard</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Right: Live Interactive Summary Card & WhatsApp CTA -->
        <div class="booking-summary-card">
          <div class="summary-crest">
            <img src="images/logo.svg" alt="Expo Tree Emblem" />
            <div class="summary-title">STALL BOOKING SUMMARY</div>
          </div>

          <div class="summary-rows">
            <div class="summary-row">
              <span class="summary-label">Exhibition:</span>
              <span class="summary-value" id="summary-event-name">Upcoming Exhibition</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Category:</span>
              <span class="summary-value" id="summary-category-name">Jewellery &amp; Accessories</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Stall Type:</span>
              <span class="summary-value" id="summary-stall-type">Prime Corner Canopy</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Amenities:</span>
              <span class="summary-value" id="summary-addons-text">15A Power, Instagram Feature, Fascia Board</span>
            </div>
          </div>

          <ul class="summary-perks">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Guaranteed Prime Footfall Placement</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Tables, Chairs, Fabric Draping &amp; Lighting Included</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Promotion on 88.9K+ Follower Instagram Page</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Full Power Backup &amp; Venue Security 24/7</span>
            </li>
          </ul>

          <!-- 1-Click WhatsApp Booking Action -->
          <a href="#" id="whatsapp-booking-btn" class="btn btn-whatsapp instant-whatsapp-cta-btn">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
            <span>Instant WhatsApp Enquiry (<?= ADMIN_PHONE ?>)</span>
          </a>

          <div class="booking-hotline-note">
            Prefer speaking directly? Call our Booking Team: <a href="tel:<?= ADMIN_PHONE ?>"><?= ADMIN_PHONE ?></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       CURATED CATEGORIES (TOP 8 POPULAR CATEGORIES)
       ========================================================================== -->
  <section id="categories" class="section categories-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge badge-burgundy">Curated Collections</span>
        <h2 class="section-title title-burgundy">Popular Categories We Host</h2>
        <div class="festive-divider">
          <span class="festive-divider-icon">✦</span>
        </div>
        <p class="section-subtitle">
          From regal handcrafted jewellery to artisanal bakery, explore the diverse spectrum of home-grown boutique categories.
        </p>
      </div>

      <div class="clean-categories-grid">
        <!-- 1. Jewellery -->
        <a href="<?= BASE_URL ?>/categories.php?cat=jewellery" class="clean-category-card">
          <div class="clean-cat-icon">💎</div>
          <div class="clean-cat-name">Jewellery &amp; Kundan</div>
          <div class="clean-cat-desc">Silver, Kundan &amp; Western Pieces</div>
        </a>

        <!-- 2. Festive Couture -->
        <a href="<?= BASE_URL ?>/categories.php?cat=apparel" class="clean-category-card">
          <div class="clean-cat-icon">👗</div>
          <div class="clean-cat-name">Festive Couture</div>
          <div class="clean-cat-desc">Handcrafted Silks &amp; Ethnic Wear</div>
        </a>

        <!-- 3. Gifting & Home Decor -->
        <a href="<?= BASE_URL ?>/categories.php?cat=decor" class="clean-category-card">
          <div class="clean-cat-icon">🎁</div>
          <div class="clean-cat-name">Gifting &amp; Home Décor</div>
          <div class="clean-cat-desc">Brass Urli, Artifacts &amp; Festive Lights</div>
        </a>

        <!-- 4. Handbags -->
        <a href="<?= BASE_URL ?>/categories.php?cat=handbags" class="clean-category-card">
          <div class="clean-cat-icon">👜</div>
          <div class="clean-cat-name">Handbags &amp; Clutches</div>
          <div class="clean-cat-desc">Potlis, Totes &amp; Embroidered Clutches</div>
        </a>

        <!-- 5. Skincare -->
        <a href="<?= BASE_URL ?>/categories.php?cat=skincare" class="clean-category-card">
          <div class="clean-cat-icon">🌿</div>
          <div class="clean-cat-name">Skincare &amp; Perfumes</div>
          <div class="clean-cat-desc">Organic Ittar, Serums &amp; Botanicals</div>
        </a>

        <!-- 6. Candles & Soaps -->
        <a href="<?= BASE_URL ?>/categories.php?cat=candles" class="clean-category-card">
          <div class="clean-cat-icon">🕯️</div>
          <div class="clean-cat-name">Candles &amp; Artisan Soaps</div>
          <div class="clean-cat-desc">Soy Wax, Aromas &amp; Handmade Soaps</div>
        </a>

        <!-- 7. Bakery & Gourmet -->
        <a href="<?= BASE_URL ?>/categories.php?cat=bakery" class="clean-category-card">
          <div class="clean-cat-icon">🍪</div>
          <div class="clean-cat-name">Bakery &amp; Gourmet Food</div>
          <div class="clean-cat-desc">Artisan Chocolates, Dryfruits &amp; Hampers</div>
        </a>

        <!-- 8. Tarot & Healing -->
        <a href="<?= BASE_URL ?>/categories.php?cat=healing" class="clean-category-card">
          <div class="clean-cat-icon">🔮</div>
          <div class="clean-cat-name">Tarot &amp; Energy Healing</div>
          <div class="clean-cat-desc">Crystals, Singing Bowls &amp; Readings</div>
        </a>
      </div>

      <div style="text-align: center; margin-top: 2.5rem;">
        <a href="<?= BASE_URL ?>/categories.php" class="btn btn-outline-gold" style="font-size: 0.95rem; padding: 0.8rem 2rem;">
          <span>✦ Explore All 16 Categories</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       WHY CHOOSE EXPO TREE (4 TRUST PILLARS)
       ========================================================================== -->
  <section class="section" style="background: #fffcf7; border-top: 1px solid rgba(212, 175, 55, 0.25); border-bottom: 1px solid rgba(212, 175, 55, 0.25);">
    <div class="container">
      <div class="section-header">
        <span class="section-badge badge-gold">Why Expo Tree</span>
        <h2 class="section-title title-burgundy">Delhi NCR's Most Trusted Exhibition Organizer</h2>
        <div class="festive-divider">
          <span class="festive-divider-icon">✦</span>
        </div>
        <p class="section-subtitle">
          Over 180+ successful exhibitions organized across Gurugram, Noida, and Delhi with proven footfall and exceptional sales conversions.
        </p>
      </div>

      <div class="trust-pillars-grid">
        <!-- 1. Prime Footfall -->
        <div class="trust-pillar-card">
          <div class="trust-pillar-icon">👥</div>
          <h4>Guaranteed High Footfall</h4>
          <p>Consistently attracting 15,000 to 35,000+ targeted, high-spending shoppers every weekend.</p>
        </div>

        <!-- 2. Luxury Venues -->
        <div class="trust-pillar-card">
          <div class="trust-pillar-icon">🏬</div>
          <h4>Prestigious AC Venues</h4>
          <p>Hosted in prime luxury malls like Ambience Mall, DLF Mall of India, and upscale club atriums.</p>
        </div>

        <!-- 3. Social Media Spotlight -->
        <div class="trust-pillar-card">
          <div class="trust-pillar-icon">📢</div>
          <h4>88.9K+ Instagram Reach</h4>
          <p>Every confirmed exhibitor receives dedicated reels, story spotlights, and promotion to our active community.</p>
        </div>

        <!-- 4. Turnkey Setup -->
        <div class="trust-pillar-card">
          <div class="trust-pillar-icon">🛡️</div>
          <h4>Hassle-Free Stall Setup</h4>
          <p>Complete tables, fabric draping, cushioned chairs, LED spotlights, 15A power, and 24/7 security provided.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       REAL PHOTO GALLERY PREVIEW
       ========================================================================== -->
  <section id="gallery" class="section gallery-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge badge-burgundy">Exhibition Glimpses</span>
        <h2 class="section-title title-burgundy">Real Exhibition Photos</h2>
        <div class="festive-divider">
          <span class="festive-divider-icon">✦</span>
        </div>
        <p class="section-subtitle">
          Catch real glimpses of our curated designer canopies, eager shoppers, and festive atmosphere.
        </p>
      </div>

      <div class="hero-showcase-grid" style="margin-top: 1rem;">
        <!-- Photo 1 -->
        <div class="hero-showcase-card">
          <img src="https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=800&q=80" alt="Curated Stalls at Mall" class="hero-showcase-img" loading="lazy" />
          <div class="hero-showcase-overlay">
            <span class="hero-showcase-badge">🎪 Curated Stalls</span>
            <div class="hero-showcase-heading">Premium Mall Popups</div>
            <div class="hero-showcase-desc">Ambience &amp; DLF Mall Atriums</div>
          </div>
        </div>

        <!-- Photo 2 -->
        <div class="hero-showcase-card">
          <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=800&q=80" alt="Luxury Jewellery" class="hero-showcase-img" loading="lazy" />
          <div class="hero-showcase-overlay">
            <span class="hero-showcase-badge">💎 Luxury Jewellery</span>
            <div class="hero-showcase-heading">Kundan &amp; Fine Silver</div>
            <div class="hero-showcase-desc">Heirloom bridal collections</div>
          </div>
        </div>

        <!-- Photo 3 -->
        <div class="hero-showcase-card">
          <img src="https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=800&q=80" alt="Festive Couture" class="hero-showcase-img" loading="lazy" />
          <div class="hero-showcase-overlay">
            <span class="hero-showcase-badge">👗 Festive Couture</span>
            <div class="hero-showcase-heading">Designer Trunk Shows</div>
            <div class="hero-showcase-desc">Handcrafted silks &amp; ethnic wear</div>
          </div>
        </div>

        <!-- Photo 4 -->
        <div class="hero-showcase-card">
          <img src="https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?auto=format&fit=crop&w=800&q=80" alt="Shoppers Browsing Stalls" class="hero-showcase-img" loading="lazy" />
          <div class="hero-showcase-overlay">
            <span class="hero-showcase-badge">🛍️ 25,000+ Crowd</span>
            <div class="hero-showcase-heading">Eager Shoppers</div>
            <div class="hero-showcase-desc">High footfall weekend crowd</div>
          </div>
        </div>
      </div>

      <div style="text-align: center; margin-top: 2.5rem;">
        <a href="<?= BASE_URL ?>/gallery.php" class="btn btn-burgundy" style="font-size: 0.95rem; padding: 0.8rem 2rem;">
          <span>📸 View Full Photo Gallery</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       COMMUNITY TESTIMONIALS
       ========================================================================== -->
  <section class="section testimonials-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge badge-gold">Community Voices</span>
        <h2 class="section-title title-burgundy">What Our Exhibitors &amp; Shoppers Say</h2>
        <div class="festive-divider">
          <span class="festive-divider-icon">✦</span>
        </div>
        <p class="section-subtitle">
          Real feedback from our stall partners and weekend shoppers across Delhi NCR.
        </p>
      </div>

      <div class="testimonials-grid">
        <!-- Review 1 -->
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <p class="testimonial-quote">
            "Booking a stall with Expo Tree Exhibitions was a game-changer for our jewellery brand! The footfall at Ambience Mall Gurugram was incredible, and we sold out nearly 70% of our stock on day one."
          </p>
          <div class="testimonial-author">
            <div class="author-avatar-placeholder">RJ</div>
            <div class="author-info">
              <h5>Ritu Jain</h5>
              <p>Founder, Silver Sparkles Jewellery</p>
            </div>
          </div>
          <div class="placeholder-notice-pill">Verified Exhibitor Experience</div>
        </div>

        <!-- Review 2 -->
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <p class="testimonial-quote">
            "The best weekend shopping experience in Noida! Free entry, superb AC environment, and so many unique handcrafted home decor items that you cannot find online. My family thoroughly enjoyed."
          </p>
          <div class="testimonial-author">
            <div class="author-avatar-placeholder">AS</div>
            <div class="author-info">
              <h5>Ananya Sharma</h5>
              <p>Shopper &amp; Lifestyle Enthusiast, Noida</p>
            </div>
          </div>
          <div class="placeholder-notice-pill">Verified Shopper Experience</div>
        </div>

        <!-- Review 3 -->
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <p class="testimonial-quote">
            "Expo Tree's team is extremely professional with stall management, electrical power, and on-ground assistance. Their Instagram promotions brought targeted clients straight to our canopy."
          </p>
          <div class="testimonial-author">
            <div class="author-avatar-placeholder">MK</div>
            <div class="author-info">
              <h5>Mohit Kapoor</h5>
              <p>Director, Handcrafted Aromas &amp; Candles</p>
            </div>
          </div>
          <div class="placeholder-notice-pill">Verified Exhibitor Experience</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       FREQUENTLY ASKED QUESTIONS (FAQ)
       ========================================================================== -->
  <section id="faq" class="section faq-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge badge-burgundy">Have Questions?</span>
        <h2 class="section-title title-burgundy">Frequently Asked Questions</h2>
        <div class="festive-divider">
          <span class="festive-divider-icon">✦</span>
        </div>
        <p class="section-subtitle">
          Quick answers about booking stalls as an exhibitor or visiting as a shopper.
        </p>
      </div>

      <!-- FAQ Tabs -->
      <div class="faq-tabs">
        <button class="faq-tab-btn active" data-faq-tab="exhibitors">🎪 For Exhibitors (Stalls)</button>
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
              Our standard canopy stalls are typically 3x3 meters (or 2x2m depending on the mall layout). Each stall includes draped tables, 2 cushioned chairs, standard LED spotlights, power points, a custom brand name fascia board, and continuous on-ground security.
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-question">
              <span>How can I book a stall and lock our space?</span>
              <span class="faq-icon">▼</span>
            </button>
            <div class="faq-answer">
              You can easily use our Stall Booking Studio above or contact our primary hotline directly at <strong><?= ADMIN_PHONE ?></strong> via WhatsApp or Call. Our team will share the floor layout blueprint and booking account details. Prime corner and island stalls are allocated on a first-come, first-served basis.
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-question">
              <span>Do you promote exhibitors on your Instagram page?</span>
              <span class="faq-icon">▼</span>
            </button>
            <div class="faq-answer">
              Yes! Every confirmed exhibitor is featured on our official Instagram page <strong>@expo_tree_exhibitions</strong> (88.9K+ engaged followers) via creative reels, stories, and stall spotlight posts to drive pre-event awareness.
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
              Yes! Entry is 100% free for all shoppers, families, and children. You can generate your digital VIP Pass above to skip regular queues and enter our ₹5,000 festive shopping voucher lucky draw.
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-question">
              <span>What are the exhibition timings and parking arrangements?</span>
              <span class="faq-icon">▼</span>
            </button>
            <div class="faq-answer">
              Our exhibitions typically operate from 11:00 AM to 10:00 PM on Saturdays and Sundays. Since all our venues are prestigious malls and convention centers, ample multi-level parking and valet services are readily available.
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-question">
              <span>Can I pay with UPI / Cards at the stalls?</span>
              <span class="faq-icon">▼</span>
            </button>
            <div class="faq-answer">
              All our exhibitors accept UPI (Google Pay, PhonePe, Paytm), credit/debit cards, and cash. High-speed connectivity is maintained throughout the exhibition premises.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       CONTACT & INSTAGRAM SHOWCASE
       ========================================================================== -->
  <section id="contact" class="section contact-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge badge-gold">Connect With Us</span>
        <h2 class="section-title title-white">Let’s Connect &amp; Collaborate</h2>
        <div class="festive-divider">
          <span class="festive-divider-icon">✦</span>
        </div>
        <p class="section-subtitle subtitle-white">
          Have a question about booking a stall, partnering with our exhibitions, or attending our next event? We are just a message away!
        </p>
      </div>

      <div class="contact-grid">
        <!-- Left: Direct Contact Information -->
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
              <p style="font-size: 0.85rem; color: var(--gold-300);"><?= INSTAGRAM_FOLLOWERS ?> Active Followers</p>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon-bubble">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div class="contact-details">
              <h5>Coverage Operational Hubs</h5>
              <p>Gurugram • Noida • South &amp; Central Delhi • Faridabad • Ghaziabad • Greater Noida</p>
            </div>
          </div>

          <div style="margin-top: 2rem;">
            <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>?text=<?= urlencode('Hello Expo Tree! I want to enquire about stall booking.') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-gold" style="width: 100%;">
              <span>💬 Quick Stall Enquiry on WhatsApp</span>
            </a>
          </div>
        </div>

        <!-- Right: Instagram Showcase Card (88.9K followers) -->
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

          <a href="https://www.instagram.com/<?= INSTAGRAM_HANDLE ?>/" target="_blank" rel="noopener noreferrer" class="btn btn-outline-gold" style="width: 100%;">
            <span>Follow @<?= INSTAGRAM_HANDLE ?></span>
          </a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
