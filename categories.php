<?php
/**
 * 16 Exhibition Categories We Host
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = '16 Categories We Host | Expo Tree Exhibitions Delhi NCR';
$pageDesc = 'Discover 16 curated lifestyle exhibition categories: Jewellery, Apparel, Footwear, Home Decor, Handcrafted Soaps, Gourmet Food & more across Delhi NCR.';
$currentPage = 'categories';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section page-hero-section">
  <div class="hero-bg-photo" aria-hidden="true" style="opacity: 0.16;"></div>
  <div class="container" style="position: relative; z-index: 2;">
    <span class="section-badge badge-gold">✦ Product Showcase Directory</span>
    <h1 class="section-title title-white">16 Curated Exhibition Categories</h1>
    <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>
    <p class="section-subtitle subtitle-white" style="max-width: 800px; margin: 0 auto;">
      From heirloom silver jewellery and festive silks to artisanal soaps and mystic tarot cards, explore the full spectrum of products featured at Expo Tree Exhibitions.
    </p>
  </div>
</section>

<!-- Categories Comprehensive Grid -->
<section class="section categories-section">
  <div class="container">
    <div class="categories-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
      
      <!-- 1. Jewellery & Accessories -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=600&q=80" alt="Jewellery &amp; Accessories" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="6 3 18 3 22 9 12 22 2 9"/><line x1="12" y1="22" x2="12" y2="9"/><line x1="2" y1="9" x2="22" y2="9"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Jewellery &amp; Accessories</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Kundan, Polki, 92.5 Hallmarked Sterling Silver, temple jewellery, semi-precious stones, and contemporary fashion accessories.
        </p>
        <div class="category-badge-pill">
          Prime Footfall • Corner Canopy Recommended
        </div>
        <a href="book-a-stall.php?category=Jewellery%20%26%20Accessories" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Jewellery →
        </a>
      </div>

      <!-- 2. Handbags -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=600&q=80" alt="Handbags &amp; Clutches" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Handbags &amp; Clutches</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Embroidered bridal potlis, handcrafted box clutches, genuine leather totes, vegan sling bags, and travel duffels.
        </p>
        <div class="category-badge-pill">
          High Impulse Category • Main Walkway Placement
        </div>
        <a href="book-a-stall.php?category=Handbags" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Handbags →
        </a>
      </div>

      <!-- 3. Apparel & Footwear -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=600&q=80" alt="Apparel &amp; Footwear" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.38 3.46L16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Apparel &amp; Footwear</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Chanderi, Banarasi suits, Lucknowi chikankari, designer sarees, hand-embroidered Punjabi juttis, and festive footwear.
        </p>
        <div class="category-badge-pill">
          High Sales Volume • Center Island Booth Preferred
        </div>
        <a href="book-a-stall.php?category=Apparel%20%26%20Footwear" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Apparel →
        </a>
      </div>

      <!-- 4. Gifting & Home Décor -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=600&q=80" alt="Gifting &amp; Home Décor" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Gifting &amp; Home Décor</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Handcrafted brass diyas, urlis, corporate festive gift boxes, ceramic vases, wall accents, and celebratory torans.
        </p>
        <div class="category-badge-pill">
          Festive Corporate &amp; Home Gifting Demand
        </div>
        <a href="book-a-stall.php?category=Gifting%20%26%20Home%20D%C3%A9cor" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Decor →
        </a>
      </div>

      <!-- 5. Skincare & Perfumes -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1547887537-6158d64c35b3?auto=format&fit=crop&w=600&q=80" alt="Skincare &amp; Perfumes" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Skincare &amp; Perfumes</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Ayurvedic cold-pressed oils, handmade face serums, traditional non-alcoholic ittars, body butters, and luxury perfumes.
        </p>
        <div class="category-badge-pill">
          High Repeat Patronage • Boutique Setup
        </div>
        <a href="book-a-stall.php?category=Skincare%20%26%20Perfumes" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Skincare →
        </a>
      </div>

      <!-- 6. Crochet -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1615486511484-92e172cc4fe0?auto=format&fit=crop&w=600&q=80" alt="Crochet Creations" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M8 12a4 4 0 1 0 8 0 4 4 0 1 0-8 0"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Crochet Creations</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Artisanal knitted tote bags, everlasting crochet bouquets, hair accessories, cute coasters, and custom plushies.
        </p>
        <div class="category-badge-pill">
          Artisan Handcrafted • Dedicated Table Space
        </div>
        <a href="book-a-stall.php?category=Crochet%20%26%20Handmade" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Crochet →
        </a>
      </div>

      <!-- 7. Candles & Soaps -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1603006905003-be475563bc59?auto=format&fit=crop&w=600&q=80" alt="Candles &amp; Soaps" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21h6v-9H9v9zm3-18a3 3 0 0 0-3 3c0 2 3 5 3 5s3-3 3-5a3 3 0 0 0-3-3z"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Candles &amp; Soaps</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Pure soy wax scented candles, wax melts, botanical melt-and-pour soaps, and festive dessert candles.
        </p>
        <div class="category-badge-pill">
          Festive Gifting • Rapid Turnover
        </div>
        <a href="book-a-stall.php?category=Candles%20%26%20Soaps" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Candles →
        </a>
      </div>

      <!-- 8. Wooden Toys -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1596461404969-9ae70f2830c1?auto=format&fit=crop&w=600&q=80" alt="Wooden Toys" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Wooden Toys</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Eco-friendly neem wood toys, Montessori learning blocks, handcrafted board games, and traditional Indian spinning tops.
        </p>
        <div class="category-badge-pill">
          Family &amp; Children Footfall Attraction
        </div>
        <a href="book-a-stall.php?category=Wooden%20Toys" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Wooden Toys →
        </a>
      </div>

      <!-- 9. Home Furnishings -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1584100936595-c0654b55a2e2?auto=format&fit=crop&w=600&q=80" alt="Home Furnishings" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8v9"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Home Furnishings</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Jaipuri block print bedsheets, embroidered cushion covers, dhurries, table runners, and festive throws.
        </p>
        <div class="category-badge-pill">
          High Average Order Value • 2-Side Open Space
        </div>
        <a href="book-a-stall.php?category=Home%20Furnishings" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Furnishings →
        </a>
      </div>

      <!-- 10. Bakery & Dryfruits -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=600&q=80" alt="Bakery &amp; Dryfruits" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2a5 5 0 0 0-5 5c0 3 5 7 5 7s5-4 5-7a5 5 0 0 0-5-5z"/><path d="M5 14h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2z"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Bakery &amp; Dryfruits</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Gourmet dates, Afghan dry fruits, artisan Belgian chocolates, festive tea cakes, and roasted nut hampers.
        </p>
        <div class="category-badge-pill">
          F&amp;B Tasting &amp; Gift Hampers
        </div>
        <a href="book-a-stall.php?category=Bakery%20%26%20Dryfruits" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Bakery →
        </a>
      </div>

      <!-- 11. Food Stalls -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&q=80" alt="Food &amp; Beverage Stalls" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Food &amp; Beverage Stalls</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Delhi street chaat, gourmet momos, cold-pressed mocktails, artisanal coffee, and international dessert bites.
        </p>
        <div class="category-badge-pill">
          Food Court &amp; Beverage Pavilion
        </div>
        <a href="book-a-stall.php?category=Food%20Stalls" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Food Stall →
        </a>
      </div>

      <!-- 12. Handmade Products -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=600&q=80" alt="Handmade Crafts" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Handmade Crafts</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Ceramic pottery, resin art trays, macramé wall hangings, brass wind chimes, and papercraft stationery.
        </p>
        <div class="category-badge-pill">
          Artisan Handmade Boutique Space
        </div>
        <a href="book-a-stall.php?category=Handmade%20Products" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Crafts →
        </a>
      </div>

      <!-- 13. Kidswear -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?auto=format&fit=crop&w=600&q=80" alt="Kidswear &amp; Accessories" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 19V6M5 12l7-7 7 7"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Kidswear &amp; Accessories</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Festive kurtas and lehengas for toddlers, organic cotton daily wear, handcrafted hair clips, and footwear.
        </p>
        <div class="category-badge-pill">
          Kids &amp; Toddler Festive Collection
        </div>
        <a href="book-a-stall.php?category=Kidswear" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stall in Kidswear →
        </a>
      </div>

      <!-- 14. Tarot Card Reading -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1633511090164-b43840ea1607?auto=format&fit=crop&w=600&q=80" alt="Tarot Card Reading" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="2" width="16" height="20" rx="2"/><circle cx="12" cy="12" r="4"/><line x1="12" y1="6" x2="12" y2="8"/><line x1="12" y1="16" x2="12" y2="18"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Tarot Card Reading</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Experienced intuitive tarot card readers, angel card divination, astrology charts, and palmistry sessions.
        </p>
        <div class="category-badge-pill">
          Specialty Consultation Space
        </div>
        <a href="book-a-stall.php?category=Tarot%20Card%20Reading" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Tarot Space →
        </a>
      </div>

      <!-- 15. Healing Products -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1600857544200-b2f666a9a2ec?auto=format&fit=crop&w=600&q=80" alt="Healing &amp; Crystals" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Healing &amp; Crystals</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Natural amethyst, rose quartz clusters, orgonite pyramids, Tibetan singing bowls, sage smudges, and reiki tools.
        </p>
        <div class="category-badge-pill">
          Holistic Wellness &amp; Lifestyle Space
        </div>
        <a href="book-a-stall.php?category=Healing%20Products" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Healing Stall →
        </a>
      </div>

      <!-- 16. Toys & Stationery -->
      <div class="category-card" style="text-align: left; align-items: flex-start; padding: 1.35rem;">
        <div class="category-card-thumb-wrap">
          <img src="https://images.unsplash.com/photo-1586075010923-2dd4570fb338?auto=format&fit=crop&w=600&q=80" alt="Toys &amp; Stationery" class="category-card-thumb" loading="lazy" />
          <div class="category-card-thumb-overlay"></div>
          <div class="category-card-icon-floating">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><circle cx="11" cy="11" r="2"/></svg>
          </div>
        </div>
        <h3 class="category-name" style="font-size: 1.25rem;">Toys &amp; Stationery</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.5;">
          Luxury planners, wax seal kits, kawaii stationery, creative puzzles, and educational STEM kits.
        </p>
        <div class="category-badge-pill">
          Stationery &amp; Corporate Gift Sets
        </div>
        <a href="book-a-stall.php?category=Toys%20%26%20Stationery" class="btn btn-burgundy" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;">
          Book Stationery Stall →
        </a>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
