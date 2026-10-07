<?php
/**
 * Upcoming Exhibitions & Festive Melas Calendar
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Upcoming Exhibitions 2026 | Delhi NCR Lifestyle & Festive Melas';
$pageDesc = 'Explore upcoming lifestyle exhibitions in Gurugram, Noida, Delhi, Faridabad & Ghaziabad. Book high-footfall shopping & food stalls or get free shopper pass.';
$currentPage = 'upcoming-exhibitions';

// Filters
$selectedCity = sanitize($_GET['city'] ?? 'all');
$selectedType = sanitize($_GET['type'] ?? 'all');
$selectedCategory = sanitize($_GET['category'] ?? 'all');
$selectedStallType = sanitize($_GET['stall_type'] ?? 'all');
$selectedAvailability = sanitize($_GET['availability'] ?? 'all');
$selectedDate = sanitize($_GET['date'] ?? 'upcoming');
$sortBy = sanitize($_GET['sort'] ?? 'date_asc');
$searchKeyword = sanitize($_GET['q'] ?? '');

// Base Query - only published/active exhibitions appear publicly
$sql = "SELECT * FROM events WHERE status IN ('published', 'active')";
$params = [];

// City Filter
if ($selectedCity !== 'all' && !empty($selectedCity)) {
    $sql .= " AND LOWER(city) LIKE :city";
    $params[':city'] = '%' . strtolower($selectedCity) . '%';
}

// Event Type Filter
if ($selectedType !== 'all' && !empty($selectedType)) {
    $sql .= " AND LOWER(event_type) LIKE :type";
    $params[':type'] = '%' . strtolower($selectedType) . '%';
}

// Category Filter
if ($selectedCategory !== 'all' && !empty($selectedCategory)) {
    $sql .= " AND LOWER(category) LIKE :cat";
    $params[':cat'] = '%' . strtolower($selectedCategory) . '%';
}

// Stall Type Filter
if ($selectedStallType === 'canopy') {
    $sql .= " AND (price_shopping_canopy > 0 OR price_food_canopy > 0)";
} elseif ($selectedStallType === 'table') {
    $sql .= " AND (price_shopping_table1 > 0 OR price_shopping_table2 > 0)";
} elseif ($selectedStallType === 'food') {
    $sql .= " AND (price_food_canopy > 0 OR price_food_table2 > 0)";
}

// Availability Filter
if ($selectedAvailability === 'available') {
    $sql .= " AND available_stalls > 0";
}

// Date Filter
$today = date('Y-m-d');
if ($selectedDate === 'upcoming') {
    $sql .= " AND end_date >= :today";
    $params[':today'] = $today;
} elseif ($selectedDate === 'past') {
    $sql .= " AND end_date < :today";
    $params[':today'] = $today;
}

// Sorting
if ($sortBy === 'price_asc') {
    $sql .= " ORDER BY daily_stall_price ASC";
} elseif ($sortBy === 'price_desc') {
    $sql .= " ORDER BY daily_stall_price DESC";
} elseif ($sortBy === 'date_desc') {
    $sql .= " ORDER BY start_date DESC";
} elseif ($sortBy === 'stalls_avail') {
    $sql .= " ORDER BY available_stalls DESC";
} else {
    // default date_asc
    $sql .= " ORDER BY start_date ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

// If search keyword provided, filter
if (!empty($searchKeyword)) {
    $searchKeywordLower = strtolower($searchKeyword);
    $events = array_filter($events, function($ev) use ($searchKeywordLower) {
        return strpos(strtolower($ev['title']), $searchKeywordLower) !== false
            || strpos(strtolower($ev['venue']), $searchKeywordLower) !== false
            || strpos(strtolower($ev['city']), $searchKeywordLower) !== false
            || strpos(strtolower($ev['event_type']), $searchKeywordLower) !== false
            || strpos(strtolower($ev['category'] ?? ''), $searchKeywordLower) !== false;
    });
}

// Stats count
$totalActiveCount = $pdo->query("SELECT COUNT(*) FROM events WHERE status IN ('published', 'active') AND end_date >= '$today'")->fetchColumn();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero Header -->
<section class="section page-hero-section">
  <div class="hero-bg-photo" aria-hidden="true" style="opacity: 0.18;"></div>
  <div class="container" style="position: relative; z-index: 2;">
    <span class="section-badge badge-gold">✦ 2026 Festive &amp; Corporate Melas Calendar</span>
    <h1 class="section-title title-white">Upcoming Exhibitions &amp; Lifestyle Melas</h1>
    <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>
    <p class="section-subtitle subtitle-white" style="max-width: 780px; margin: 0 auto;">
      Discover curated high-footfall shopping festivals across Gurugram tech parks, Noida corporate hubs, South Delhi malls, and premium gated societies. Stalls available on first-come basis!
    </p>

    <!-- Quick Stats Summary -->
    <div style="display: flex; justify-content: center; gap: 2rem; margin-top: 1.75rem; flex-wrap: wrap;">
      <div style="background: rgba(255,255,255,0.06); padding: 0.6rem 1.25rem; border-radius: 30px; border: 1px solid rgba(212,175,55,0.3); font-size: 0.9rem;">
        <span style="color: var(--gold-300); font-weight: 700;"><?= $totalActiveCount ?></span> Upcoming Scheduled Exhibitions
      </div>
      <div style="background: rgba(255,255,255,0.06); padding: 0.6rem 1.25rem; border-radius: 30px; border: 1px solid rgba(212,175,55,0.3); font-size: 0.9rem;">
        <span>🎪</span> 100% Verified Footfall &amp; Society Clearances
      </div>
    </div>
  </div>
</section>

<!-- Filter & Search Controls Section -->
<section class="section" style="padding-top: 2.5rem; padding-bottom: 5rem;">
  <div class="container">

    <!-- Filters Bar Card -->
    <div class="filters-panel" style="background: #ffffff; border: 1.5px solid var(--border-gold); border-radius: 18px; padding: 1.5rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-card);">
      
      <!-- City Filter Tabs -->
      <div style="margin-bottom: 1.25rem;">
        <div style="font-size: 0.85rem; font-weight: 700; color: var(--burgundy-900); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.6rem;">
          Select City / Region:
        </div>
        <div class="filter-tabs-wrapper" style="margin-bottom: 0;">
          <?php
          $cities = [
              'all' => 'All Cities (Delhi NCR)',
              'gurugram' => 'Gurugram',
              'noida' => 'Noida',
              'delhi' => 'Delhi',
              'faridabad' => 'Faridabad',
              'ghaziabad' => 'Ghaziabad',
              'greater noida' => 'Greater Noida'
          ];
          foreach ($cities as $ckey => $cname):
              $isActive = (strtolower($selectedCity) === strtolower($ckey)) || ($ckey === 'all' && empty($selectedCity));
          ?>
            <a href="?city=<?= urlencode($ckey) ?>&type=<?= urlencode($selectedType) ?>&date=<?= urlencode($selectedDate) ?><?= !empty($searchKeyword) ? '&q=' . urlencode($searchKeyword) : '' ?>" 
               class="filter-btn <?= $isActive ? 'active' : '' ?>">
              <?= $cname ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Secondary Filters & Search Bar Form -->
      <form method="GET" action="upcoming-exhibitions.php" style="border-top: 1px dashed rgba(212,175,55,0.35); padding-top: 1.25rem;">
        <input type="hidden" name="city" value="<?= e($selectedCity) ?>" />

        <!-- Row 1: Search & Core Filters -->
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem;">
          <!-- Keyword Search -->
          <div style="position: relative;">
            <input type="text" name="q" value="<?= e($searchKeyword) ?>" placeholder="Search venue, tech park, society or category..." 
                   style="width: 100%; padding: 0.7rem 1rem 0.7rem 2.5rem; border: 1.5px solid #e2d1d4; border-radius: 10px; font-size: 0.92rem; outline: none;" />
            <span style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #888;">🔍</span>
          </div>

          <!-- Event Type Dropdown -->
          <div>
            <select name="type" style="width: 100%; padding: 0.7rem 0.85rem; border: 1.5px solid #e2d1d4; border-radius: 10px; font-size: 0.92rem; background: #ffffff;">
              <option value="all" <?= $selectedType === 'all' ? 'selected' : '' ?>>All Event Types</option>
              <option value="Corporate" <?= stripos($selectedType, 'Corporate') !== false ? 'selected' : '' ?>>Corporate Tech Parks</option>
              <option value="Society" <?= stripos($selectedType, 'Society') !== false ? 'selected' : '' ?>>Premium Societies</option>
              <option value="Mall" <?= stripos($selectedType, 'Mall') !== false ? 'selected' : '' ?>>Mall Atriums</option>
              <option value="Exhibition" <?= stripos($selectedType, 'Exhibition') !== false ? 'selected' : '' ?>>Festive Melas</option>
            </select>
          </div>

          <!-- Category Filter -->
          <div>
            <select name="category" style="width: 100%; padding: 0.7rem 0.85rem; border: 1.5px solid #e2d1d4; border-radius: 10px; font-size: 0.92rem; background: #ffffff;">
              <option value="all" <?= $selectedCategory === 'all' ? 'selected' : '' ?>>All Categories</option>
              <option value="Lifestyle" <?= stripos($selectedCategory, 'Lifestyle') !== false ? 'selected' : '' ?>>Lifestyle &amp; Festive</option>
              <option value="Fashion" <?= stripos($selectedCategory, 'Fashion') !== false ? 'selected' : '' ?>>Fashion &amp; Jewellery</option>
              <option value="Home Decor" <?= stripos($selectedCategory, 'Decor') !== false ? 'selected' : '' ?>>Home Decor &amp; Crafts</option>
              <option value="Food" <?= stripos($selectedCategory, 'Food') !== false ? 'selected' : '' ?>>Gourmet &amp; Food Fest</option>
            </select>
          </div>
        </div>

        <!-- Row 2: Stall Type, Availability, Sorting & Actions -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1.2fr auto; gap: 0.75rem; align-items: center;">
          <!-- Stall Type -->
          <div>
            <select name="stall_type" style="width: 100%; padding: 0.65rem 0.75rem; border: 1.5px solid #e2d1d4; border-radius: 10px; font-size: 0.88rem; background: #ffffff;">
              <option value="all" <?= $selectedStallType === 'all' ? 'selected' : '' ?>>All Stall Types</option>
              <option value="canopy" <?= $selectedStallType === 'canopy' ? 'selected' : '' ?>>Shopping Canopies (10x10)</option>
              <option value="table" <?= $selectedStallType === 'table' ? 'selected' : '' ?>>Open Tables (6x3)</option>
              <option value="food" <?= $selectedStallType === 'food' ? 'selected' : '' ?>>Food &amp; Beverage Stalls</option>
            </select>
          </div>

          <!-- Availability Filter -->
          <div>
            <select name="availability" style="width: 100%; padding: 0.65rem 0.75rem; border: 1.5px solid #e2d1d4; border-radius: 10px; font-size: 0.88rem; background: #ffffff;">
              <option value="all" <?= $selectedAvailability === 'all' ? 'selected' : '' ?>>All Availability</option>
              <option value="available" <?= $selectedAvailability === 'available' ? 'selected' : '' ?>>Available Stalls Only</option>
            </select>
          </div>

          <!-- Sort By Dropdown -->
          <div>
            <select name="sort" style="width: 100%; padding: 0.65rem 0.75rem; border: 1.5px solid #e2d1d4; border-radius: 10px; font-size: 0.88rem; background: #ffffff;">
              <option value="date_asc" <?= $sortBy === 'date_asc' ? 'selected' : '' ?>>Sort: Date (Earliest First)</option>
              <option value="date_desc" <?= $sortBy === 'date_desc' ? 'selected' : '' ?>>Sort: Date (Latest First)</option>
              <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Sort: Price (Low to High)</option>
              <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Sort: Price (High to Low)</option>
              <option value="stalls_avail" <?= $sortBy === 'stalls_avail' ? 'selected' : '' ?>>Sort: Most Stalls Available</option>
            </select>
          </div>

          <!-- Filter Submit & Reset -->
          <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-burgundy" style="padding: 0.65rem 1.25rem;">
              <span>Apply Filters</span>
            </button>
            <a href="upcoming-exhibitions.php" class="btn" style="padding: 0.65rem 1rem; background: #f3f4f6; color: #4b5563; border: 1px solid #d1d5db;" title="Reset Filters">
              <span>Reset</span>
            </a>
          </div>
        </div>
      </form>
    </div>

    <!-- Results Count -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem;">
      <div style="font-size: 1.05rem; font-weight: 600; color: var(--burgundy-950);">
        Showing <span style="color: var(--burgundy-700); font-weight: 700;"><?= count($events) ?></span> Exhibitions
        <?php if ($selectedCity !== 'all'): ?> in <span style="color: var(--gold-600);"><?= ucwords($selectedCity) ?></span><?php endif; ?>
      </div>
      <div style="font-size: 0.88rem; color: var(--text-muted);">
        📞 Stalls Allocated on First-Come Basis • Call <a href="tel:<?= ADMIN_PHONE ?>" style="color: var(--burgundy-800); font-weight: 700;"><?= ADMIN_PHONE ?></a>
      </div>
    </div>

    <!-- Events Grid Container -->
    <?php if (empty($events)): ?>
      <div style="text-align: center; padding: 4rem 1.5rem; background: #ffffff; border-radius: 18px; border: 1.5px dashed var(--border-gold);">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🎪</div>
        <h3 style="font-family: var(--font-cinzel); color: var(--burgundy-900); font-size: 1.5rem; margin-bottom: 0.5rem;">No Exhibitions Found Matching Your Filters</h3>
        <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 1.5rem;">
          We continuously add new corporate tech park and high-society flea markets. Try selecting "All Cities" or reset your search.
        </p>
        <a href="upcoming-exhibitions.php" class="btn btn-gold">
          <span>View All Upcoming Exhibitions</span>
        </a>
      </div>
    <?php else: ?>
      <div class="events-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
        <?php foreach ($events as $ev): ?>
          <?php
            $startDate = strtotime($ev['start_date']);
            $dayNum = date('d', $startDate);
            $monthShort = date('M', $startDate);
            $yearNum = date('Y', $startDate);
            $eventUrl = BASE_URL . '/event.php?id=' . $ev['id'] . '&slug=' . urlencode($ev['slug']);

            // Image fallback
            $img = !empty($ev['image_url']) ? $ev['image_url'] : 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=700&q=80';

            // Lowest price calculation
            $minPrice = $ev['daily_stall_price'] ?? 4000;
            if (!empty($ev['price_shopping_table1']) && $ev['price_shopping_table1'] > 0) {
                $minPrice = min($minPrice, $ev['price_shopping_table1']);
            }
            if (!empty($ev['price_shopping_canopy']) && $ev['price_shopping_canopy'] > 0) {
                $minPrice = min($minPrice, $ev['price_shopping_canopy']);
            }

            // WhatsApp link prefilled
            $waMsg = "Hello Expo Tree! I am interested in booking a stall for *" . $ev['title'] . "* at *" . $ev['venue'] . ", " . $ev['city'] . "* (" . $ev['date_display'] . "). Please share the available stall options and blueprint layout.";
            $waUrl = 'https://wa.me/' . ADMIN_WHATSAPP . '?text=' . urlencode($waMsg);
          ?>
          <div class="event-card" style="background: #ffffff; border: 1.5px solid var(--border-gold); border-radius: 18px; overflow: hidden; display: flex; flex-direction: column; box-shadow: var(--shadow-card); transition: transform 0.3s ease, box-shadow 0.3s ease;">
            
            <!-- Card Image & Header Badges -->
            <div style="position: relative; height: 210px; overflow: hidden;">
              <img src="<?= e($img) ?>" alt="<?= e($ev['title']) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" loading="lazy" />
              <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0.15) 0%, rgba(26,3,7,0.75) 100%);"></div>

              <!-- Date Badge -->
              <div class="event-date-badge" style="position: absolute; top: 1rem; left: 1rem; background: rgba(35, 4, 10, 0.95); border: 1.5px solid var(--gold-400); border-radius: 10px; padding: 0.4rem 0.75rem; text-align: center; color: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.4);">
                <div style="font-size: 1.3rem; font-weight: 800; font-family: var(--font-cinzel); color: var(--gold-300); line-height: 1;"><?= $dayNum ?></div>
                <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; color: #ffffff;"><?= $monthShort ?></div>
              </div>

              <!-- City & Type Tag -->
              <div style="position: absolute; top: 1rem; right: 1rem; display: flex; gap: 0.4rem; flex-direction: column; align-items: flex-end;">
                <span style="background: var(--gold-500); color: #1a0408; font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.6rem; border-radius: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
                  📍 <?= e($ev['city']) ?>
                </span>
                <span style="background: rgba(0,0,0,0.75); color: #ffffff; font-size: 0.72rem; font-weight: 600; padding: 0.2rem 0.55rem; border-radius: 15px; border: 1px solid rgba(255,255,255,0.3);">
                  <?= e($ev['event_type']) ?>
                </span>
              </div>

              <!-- Location / Indoor-Outdoor Badge -->
              <div style="position: absolute; bottom: 0.85rem; left: 1rem; right: 1rem; display: flex; justify-content: space-between; align-items: center; color: #ffffff;">
                <span style="font-size: 0.78rem; background: rgba(0,0,0,0.65); padding: 0.2rem 0.55rem; border-radius: 6px; backdrop-filter: blur(4px);">
                  🏢 <?= e($ev['location_type'] ?? 'Indoors') ?>
                </span>
                <span style="font-size: 0.78rem; background: rgba(0,0,0,0.65); padding: 0.2rem 0.55rem; border-radius: 6px; backdrop-filter: blur(4px);">
                  👥 <?= e($ev['footfall'] ?? 'High Footfall') ?>
                </span>
              </div>
            </div>

            <!-- Card Body Content -->
            <div style="padding: 1.35rem 1.35rem 1rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <h3 style="font-family: var(--font-cinzel); font-size: 1.18rem; color: var(--burgundy-950); margin-bottom: 0.4rem; line-height: 1.3;">
                  <a href="<?= $eventUrl ?>" style="color: inherit; text-decoration: none;">
                    <?= e($ev['title']) ?>
                  </a>
                </h3>

                <p style="font-size: 0.88rem; color: #5a5a5a; margin-bottom: 0.85rem; display: flex; align-items: flex-start; gap: 0.4rem;">
                  <span>📍</span>
                  <span><strong><?= e($ev['venue']) ?></strong>, <?= e($ev['city']) ?> (<?= e($ev['state']) ?>)</span>
                </p>

                <!-- Key Excel Specifications Grid -->
                <div style="background: #faf7f8; border: 1px solid #ebdada; border-radius: 10px; padding: 0.75rem; margin-bottom: 1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.8rem;">
                  <div>
                    <span style="color: #888;">📅 Dates:</span><br>
                    <strong style="color: var(--burgundy-900);"><?= e($ev['date_display'] ?? formatDateRange($ev['start_date'], $ev['end_date'])) ?></strong>
                  </div>
                  <div>
                    <span style="color: #888;">⏰ Timings:</span><br>
                    <strong style="color: var(--burgundy-900);"><?= e($ev['timings'] ?? '10:00 AM - 8:00 PM') ?></strong>
                  </div>
                  <div>
                    <span style="color: #888;">🌟 Gentry:</span><br>
                    <strong style="color: var(--burgundy-900);"><?= e($ev['gentry'] ?? 'Premium Class') ?></strong>
                  </div>
                  <div>
                    <span style="color: #888;">📐 Layout:</span><br>
                    <strong style="color: var(--burgundy-900);"><?= e($ev['layout_type'] ?? 'FCFS') ?></strong>
                  </div>
                </div>

                <!-- Stalls Availability Pill -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                  <div>
                    <span style="font-size: 0.75rem; color: #777;">Stalls Available:</span>
                    <div>
                      <?php if ($ev['available_stalls'] <= 0): ?>
                        <span style="color: #DC2626; font-weight: 700; font-size: 0.88rem;">❌ Sold Out</span>
                      <?php elseif ($ev['available_stalls'] <= 5): ?>
                        <span style="color: #D97706; font-weight: 700; font-size: 0.88rem;">⚡ Only <?= $ev['available_stalls'] ?> Stalls Left!</span>
                      <?php else: ?>
                        <span style="color: #059669; font-weight: 700; font-size: 0.88rem;">✅ <?= $ev['available_stalls'] ?> of <?= $ev['total_stalls'] ?> Stalls Open</span>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div style="text-align: right;">
                    <span style="font-size: 0.75rem; color: #777;">Starting from:</span>
                    <div style="font-size: 1.15rem; font-weight: 800; color: var(--burgundy-900); font-family: var(--font-cinzel);">
                      <?= formatPrice($minPrice) ?>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Card Action Buttons (No dummy links) -->
              <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 0.6rem; margin-top: 0.5rem; border-top: 1px solid #ebdada; padding-top: 0.85rem;">
                <a href="<?= $eventUrl ?>" class="btn btn-burgundy" style="padding: 0.65rem 0.75rem; font-size: 0.86rem; text-align: center; justify-content: center;">
                  <span>View &amp; Book</span>
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="<?= $waUrl ?>" target="_blank" rel="noopener noreferrer" class="btn" style="background: #25D366; color: #ffffff; padding: 0.65rem 0.5rem; font-size: 0.84rem; text-align: center; justify-content: center; font-weight: 600;" title="Instant WhatsApp Blueprint & Queries">
                  <span>💬 WhatsApp</span>
                </a>
              </div>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Organizers Callout -->
    <div style="margin-top: 4.5rem; background: radial-gradient(circle at 50% 50%, #30060e 0%, #170205 100%); border: 1.5px solid var(--border-gold); border-radius: 20px; padding: 2.75rem 2rem; text-align: center; color: #ffffff; box-shadow: var(--shadow-card);">
      <span class="section-badge badge-gold" style="margin-bottom: 0.75rem;">✦ Are you an RWA, Mall or Corporate Facility Manager?</span>
      <h2 style="font-family: var(--font-cinzel); font-size: 1.85rem; color: var(--gold-300); margin-bottom: 0.75rem;">Host an Exhibition at Your Venue</h2>
      <p style="color: #e2c2c8; max-width: 680px; margin: 0 auto 1.75rem; font-size: 0.95rem;">
        List your society flea market, corporate carnival, or mall atrium event on Expo Tree. Our network of 5,000+ verified exhibitors will turn your premises into a festive shopping landmark!
      </p>
      <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
        <a href="<?= BASE_URL ?>/list-your-event.php" class="btn btn-gold">
          <span>✦ List Your Exhibition</span>
        </a>
        <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>?text=<?= urlencode('Hello Expo Tree! We want to organize an exhibition in our society / mall / corporate park.') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-white">
          <span>💬 Partner on WhatsApp</span>
        </a>
      </div>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
