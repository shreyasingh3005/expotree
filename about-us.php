<?php
/**
 * About Us Page
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'About Us | Expo Tree Exhibitions Delhi NCR';
$pageDesc = 'Learn about Expo Tree Exhibitions: Delhi NCR premier lifestyle & festive exhibition partner connecting homegrown brands with shoppers across Gurugram, Noida, Delhi.';
$currentPage = 'about-us';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Header -->
<section class="section page-hero-section">
  <div class="hero-bg-photo" aria-hidden="true" style="opacity: 0.16;"></div>
  <div class="container">
    <span class="section-badge badge-gold">Our Heritage &amp; Mission</span>
    <h1 class="section-title title-white">About Expo Tree Exhibitions</h1>
    <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>
    <p class="section-subtitle subtitle-white" style="max-width: 800px; margin: 0 auto;">
      "Shop &bull; Explore &bull; Support &bull; Indulge" &mdash; Empowering independent homegrown entrepreneurs and curating festive wonder for millions of Delhi NCR shoppers.
    </p>
  </div>
</section>

<!-- Narrative Section -->
<section class="section about-section" style="background: #ffffff; color: var(--text-dark);">
  <div class="container">
    <div class="about-grid">
      <div class="about-visual">
        <div class="about-card-banner" style="border: 2px solid var(--border-gold);">
          <img src="https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=1000&q=80" alt="Expo Tree Atmosphere" />
          <div class="about-floating-badge" style="background: var(--burgundy-900); color: #ffffff;">
            <div class="badge-circle-icon">🌳</div>
            <div class="badge-content">
              <h5>Expo Tree</h5>
              <p>180+ Exhibitions Curated</p>
            </div>
          </div>
        </div>
      </div>

      <div class="about-text">
        <span class="section-badge badge-burgundy">Who We Are</span>
        <h2 style="font-family: var(--font-cinzel); font-size: 2.2rem; color: var(--burgundy-950); margin-bottom: 1.25rem;">
          Delhi NCR's Leading Exhibition &amp; Stall Partner
        </h2>
        <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.8; margin-bottom: 1.25rem;">
          Expo Tree Exhibitions curates and organizes premium lifestyle, fashion, and festive exhibitions across Delhi NCR. We bridge the gap between creative small businesses, independent designers, and artisanal producers with eager, high purchasing-power shoppers.
        </p>
        <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.8; margin-bottom: 1.5rem;">
          By partnering with top-tier commercial malls (such as Ambience Mall Gurugram, DLF Mall of India Noida), prestigious corporate centers, and luxury residential societies, we ensure every event delivers an electric ambiance, seamless logistics, and exceptional sales returns for our exhibitors.
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
          <div style="border-left: 3px solid var(--gold-500); padding-left: 1rem;">
            <h4 style="font-size: 1.1rem; color: var(--burgundy-900); font-weight: 700;">Dual Commitment</h4>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">Providing exhibitors with predictable footfall while offering shoppers a fresh, curated weekend destination.</p>
          </div>
          <div style="border-left: 3px solid var(--gold-500); padding-left: 1rem;">
            <h4 style="font-size: 1.1rem; color: var(--burgundy-900); font-weight: 700;">Zero Entry Barrier</h4>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">100% free entry for shoppers, families, and children to cultivate massive, enthusiastic crowds.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Impact Numbers Section -->
<section class="stats-section" style="border-top: 1px solid var(--border-gold);">
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-number" data-target="88.9" data-suffix="K+">88.9K+</div>
      <div class="stat-label">Instagram Community</div>
      <div class="stat-subtext">@expo_tree_exhibitions</div>
    </div>

    <div class="stat-card">
      <div class="stat-number" data-target="180" data-suffix="+">180+</div>
      <div class="stat-label">Mega Exhibitions</div>
      <div class="stat-subtext">Organized Across NCR</div>
    </div>

    <div class="stat-card">
      <div class="stat-number" data-target="12000" data-suffix="+">12,000+</div>
      <div class="stat-label">Stalls Empowered</div>
      <div class="stat-subtext">Homegrown Brands</div>
    </div>

    <div class="stat-card">
      <div class="stat-number" data-target="6" data-suffix=" Hubs">6 Hubs</div>
      <div class="stat-label">Cities Covered</div>
      <div class="stat-subtext">Gurgaon, Noida, Delhi &amp; more</div>
    </div>

    <div class="stat-card">
      <div class="stat-number" data-target="10" data-suffix=" Lakh+">10 Lakh+</div>
      <div class="stat-label">Shopper Footfall</div>
      <div class="stat-subtext">Vibrant Weekend Crowds</div>
    </div>
  </div>
</section>

<!-- Coverage Cities Hub -->
<section class="section" style="background: var(--bg-ivory);">
  <div class="container">
    <div class="section-header">
      <span class="section-badge badge-emerald">Operational Territory</span>
      <h2 class="section-title title-burgundy">Coverage Across Delhi NCR</h2>
      <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>
      <p class="section-subtitle">
        We operate across the most prominent and high-spending corridors of the National Capital Region:
      </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
      <div style="background: #ffffff; border: 1px solid var(--border-gold); border-radius: 16px; padding: 1.5rem;">
        <h4 style="font-size: 1.15rem; color: var(--burgundy-950); margin-bottom: 0.4rem;">📍 Gurugram</h4>
        <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">Ambience Mall, DLF CyberHub, Golf Course Road clubs, and premium gated condominiums.</p>
      </div>

      <div style="background: #ffffff; border: 1px solid var(--border-gold); border-radius: 16px; padding: 1.5rem;">
        <h4 style="font-size: 1.15rem; color: var(--burgundy-950); margin-bottom: 0.4rem;">📍 Noida</h4>
        <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">DLF Mall of India, Sector 18 Commercial District, and Sector 137 / Expressway residential hubs.</p>
      </div>

      <div style="background: #ffffff; border: 1px solid var(--border-gold); border-radius: 16px; padding: 1.5rem;">
        <h4 style="font-size: 1.15rem; color: var(--burgundy-950); margin-bottom: 0.4rem;">📍 Delhi</h4>
        <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">Vasant Kunj luxury malls, Saket commercial atriums, Rajouri Garden, and Civil Lines pavilions.</p>
      </div>

      <div style="background: #ffffff; border: 1px solid var(--border-gold); border-radius: 16px; padding: 1.5rem;">
        <h4 style="font-size: 1.15rem; color: var(--burgundy-950); margin-bottom: 0.4rem;">📍 Faridabad</h4>
        <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">Radisson Blu Grand Ballroom, Surajkund artisan corridor, and Sector 15 / 16 commercial centers.</p>
      </div>

      <div style="background: #ffffff; border: 1px solid var(--border-gold); border-radius: 16px; padding: 1.5rem;">
        <h4 style="font-size: 1.15rem; color: var(--burgundy-950); margin-bottom: 0.4rem;">📍 Ghaziabad</h4>
        <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">Mahagun Metro Mall Atrium, Indirapuram Habitat Center, and Vasundhara shopping hubs.</p>
      </div>

      <div style="background: #ffffff; border: 1px solid var(--border-gold); border-radius: 16px; padding: 1.5rem;">
        <h4 style="font-size: 1.15rem; color: var(--burgundy-950); margin-bottom: 0.4rem;">📍 Greater Noida</h4>
        <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">India Expo Centre &amp; Mart vicinity, Pari Chowk retail centers, and Gaur City atriums.</p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
