/**
 * Expo Tree Exhibitions — Main Interactive Engine
 * Handles dual audience mode, stall booking studio, free VIP pass generator,
 * live social proof popups, gallery lightbox, FAQ accordion, and scroll effects.
 */

// Global state & config
const EXPO_PHONE = "9811175057";
const WHATSAPP_BASE = `https://wa.me/${EXPO_PHONE}`;

// Exhibition Data
const exhibitionsData = [
  {
    id: "diwali-gurugram",
    title: "Grand Diwali & Festive Shopping Carnival",
    date: { day: "18-19", month: "Oct 2026" },
    city: "Gurugram",
    venue: "Ambience Mall Promenade & Convention Center",
    timing: "11:00 AM – 10:00 PM",
    expectedFootfall: "25,000+ Footfall",
    image: "https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=800&q=80",
    tagline: "Festive Mega Carnival",
    highlights: ["120+ Curated Stalls", "Central AC Pavilion", "Live Music", "Free Entry Pass"]
  },
  {
    id: "karwa-noida",
    title: "Karwa Chauth & Karigari Lifestyle Mela",
    date: { day: "24-25", month: "Oct 2026" },
    city: "Noida",
    venue: "DLF Mall of India / Sector 18 Arena",
    timing: "11:00 AM – 10:00 PM",
    expectedFootfall: "30,000+ Shoppers",
    image: "https://images.unsplash.com/photo-1577900232427-18219b9166a0?auto=format&fit=crop&w=800&q=80",
    tagline: "Bridal & Festive Special",
    highlights: ["Mehendi Artists", "Jewellery & Handbags", "Gifting Counters", "Lucky Draw"]
  },
  {
    id: "autumn-delhi",
    title: "South Delhi Autumn Fashion & Luxury Popup",
    date: { day: "07-08", month: "Nov 2026" },
    city: "Delhi",
    venue: "The Grand Pavilion, Vasant Kunj / Saket Hub",
    timing: "11:30 AM – 9:30 PM",
    expectedFootfall: "18,000+ Affluent Shoppers",
    image: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=800&q=80",
    tagline: "Haute Couture & Handcrafted Luxury",
    highlights: ["Designer Apparel", "Home Décor", "Bakery Tastings", "Tarot Reading"]
  },
  {
    id: "winter-faridabad",
    title: "Royal Winter & Wedding Trunk Show",
    date: { day: "21-22", month: "Nov 2026" },
    city: "Faridabad",
    venue: "Radisson Blu Grand Ballroom & Lawns",
    timing: "11:00 AM – 9:00 PM",
    expectedFootfall: "15,000+ Visitors",
    image: "https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=800&q=80",
    tagline: "Wedding & Festive Gifting",
    highlights: ["Apparel & Footwear", "Wooden Toys", "Skincare & Perfumes", "Food Court"]
  },
  {
    id: "lifestyle-ghaziabad",
    title: "Delhi NCR Artisan & Handicrafts Carnival",
    date: { day: "05-06", month: "Dec 2026" },
    city: "Ghaziabad",
    venue: "Mahagun Metro Mall Atrium, Vaishali",
    timing: "11:00 AM – 10:00 PM",
    expectedFootfall: "20,000+ Footfall",
    image: "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=800&q=80",
    tagline: "Handmade & Home Furnishing",
    highlights: ["Crochet & Candles", "Handmade Soaps", "Home Furnishings", "Live Workshops"]
  },
  {
    id: "newyear-greaternoida",
    title: "Grand Christmas & New Year Lifestyle Expo",
    date: { day: "19-20", month: "Dec 2026" },
    city: "Greater Noida",
    venue: "India Expo Centre & Mart Hub",
    timing: "11:00 AM – 10:00 PM",
    expectedFootfall: "35,000+ Mega Crowd",
    image: "https://images.unsplash.com/photo-1519567241046-7f570eee3ce6?auto=format&fit=crop&w=800&q=80",
    tagline: "Year-End Mega Extravaganza",
    highlights: ["All 16 Categories", "Giant Kid's Play Zone", "Celebrity Guests", "Exclusive Deals"]
  }
];

// Sample Social Proof Notifications
const liveNotifications = [
  {
    name: "Ritu Verma (Aroma Glow Candles)",
    city: "Gurugram",
    action: "booked a Prime Corner Stall",
    event: "Ambience Mall Carnival",
    icon: "🕯️"
  },
  {
    name: "Simran Kaur",
    city: "Noida",
    action: "claimed Free VIP Shopper Pass",
    event: "Karwa Chauth Mela",
    icon: "🛍️"
  },
  {
    name: "The Velvet Trunk (Jewellery)",
    city: "South Delhi",
    action: "booked a Center Island Booth",
    event: "Vasant Kunj Luxury Popup",
    icon: "💎"
  },
  {
    name: "Pooja & Ankit",
    city: "Faridabad",
    action: "entered ₹5,000 Lucky Draw",
    event: "Royal Winter Trunk Show",
    icon: "🎉"
  },
  {
    name: "Pure Loom Handlooms",
    city: "Ghaziabad",
    action: "booked a Standard Canopy",
    event: "Handicrafts Carnival",
    icon: "✨"
  },
  {
    name: "Dr. Meenakshi (Holistic Healing)",
    city: "Greater Noida",
    action: "booked a Boutique Table Space",
    event: "New Year Lifestyle Expo",
    icon: "🌿"
  }
];

// Gallery Images Data (12 Curated Professional Photos)
const galleryData = [
  {
    category: "stalls",
    title: "Festive Canopy & Stall Setups",
    sub: "Ambience Mall, Gurugram",
    src: "https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=1200&q=80"
  },
  {
    category: "crowd",
    title: "Vibrant Shopper Footfall",
    sub: "DLF Mall of India, Noida",
    src: "https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?auto=format&fit=crop&w=1200&q=80"
  },
  {
    category: "products",
    title: "Curated Designer Jewellery Showcase",
    sub: "Handcrafted 92.5 Silver & Kundan",
    src: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=1200&q=80"
  },
  {
    category: "products",
    title: "Bridal Couture & Festive Lehengas",
    sub: "Haute Trunk Show, South Delhi",
    src: "https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1200&q=80"
  },
  {
    category: "stalls",
    title: "Aesthetic Scented Candles & Soaps",
    sub: "Homegrown Artisan Booths",
    src: "https://images.unsplash.com/photo-1603006905003-be475563bc59?auto=format&fit=crop&w=1200&q=80"
  },
  {
    category: "crowd",
    title: "Weekend Festive Shopping Spree",
    sub: "Connecting Brands with 30K+ Shoppers",
    src: "https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?auto=format&fit=crop&w=1200&q=80"
  },
  {
    category: "products",
    title: "Luxury Perfumes & Organic Skincare",
    sub: "Boutique Fragrance Popups",
    src: "https://images.unsplash.com/photo-1547887537-6158d64c35b3?auto=format&fit=crop&w=1200&q=80"
  },
  {
    category: "products",
    title: "Handcrafted Pottery & Home Décor",
    sub: "Artisan Handicrafts Carnival",
    src: "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=1200&q=80"
  },
  {
    category: "stalls",
    title: "Gourmet Bakery & Dryfruit Gifting",
    sub: "Festive Tasting Counters",
    src: "https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=1200&q=80"
  },
  {
    category: "products",
    title: "Mystic Tarot & Healing Crystals",
    sub: "Spiritual Wellness Corner",
    src: "https://images.unsplash.com/photo-1633511090164-b43840ea1607?auto=format&fit=crop&w=1200&q=80"
  },
  {
    category: "stalls",
    title: "Grand Covered Exhibition Pavilion",
    sub: "Central AC & 24/7 Power Backup",
    src: "https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=1200&q=80"
  },
  {
    category: "products",
    title: "Embroidered Potlis & Bridal Clutches",
    sub: "Handcrafted Accessories",
    src: "https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1200&q=80"
  }
];

// Init when DOM ready
document.addEventListener("DOMContentLoaded", () => {
  initNavbar();
  initAudienceToggle();
  initUpcomingEvents();
  initStallBookingStudio();
  initShopperPassGenerator();
  initLiveSocialProof();
  initStatsCounter();
  initGallery();
  initFaqAccordion();
  initLegalModals();
});

/* ==========================================================================
   1. NAVBAR & SCROLL EFFECTS
   ========================================================================== */
function initNavbar() {
  const header = document.querySelector(".main-header");
  const mobileToggle = document.getElementById("mobile-toggle-btn") || document.querySelector(".mobile-menu-toggle");
  const drawer = document.getElementById("mobile-nav-drawer") || document.querySelector(".mobile-nav-drawer");
  const backdrop = document.getElementById("mobile-nav-backdrop") || document.querySelector(".mobile-nav-backdrop");
  const closeBtn = document.getElementById("drawer-close-btn") || document.querySelector(".drawer-close-btn");
  const drawerLinks = document.querySelectorAll(".drawer-link, .nav-link");

  function openMenu() {
    drawer?.classList.add("active");
    backdrop?.classList.add("active");
    mobileToggle?.setAttribute("aria-expanded", "true");
    document.body.classList.add("nav-drawer-open");
  }

  function closeMenu() {
    drawer?.classList.remove("active");
    backdrop?.classList.remove("active");
    mobileToggle?.setAttribute("aria-expanded", "false");
    document.body.classList.remove("nav-drawer-open");
  }

  mobileToggle?.addEventListener("click", (e) => {
    e.stopPropagation();
    if (drawer?.classList.contains("active")) {
      closeMenu();
    } else {
      openMenu();
    }
  });

  closeBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    closeMenu();
  });

  backdrop?.addEventListener("click", closeMenu);

  drawerLinks.forEach(link => {
    link.addEventListener("click", () => {
      closeMenu();
    });
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && drawer?.classList.contains("active")) {
      closeMenu();
    }
  });

  window.addEventListener("scroll", () => {
    if (window.scrollY > 30) {
      header?.classList.add("header-scrolled");
    } else {
      header?.classList.remove("header-scrolled");
    }
  });
}

/* ==========================================================================
   2. DUAL AUDIENCE TOGGLE (EXHIBITOR VS SHOPPER)
   ========================================================================== */
function initAudienceToggle() {
  const modeBtns = document.querySelectorAll(".mode-btn");

  modeBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      modeBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const mode = btn.getAttribute("data-mode");
      if (mode === "exhibitor") {
        document.getElementById("booking-studio")?.scrollIntoView({ behavior: "smooth" });
      } else if (mode === "shopper") {
        document.getElementById("shopper-pass")?.scrollIntoView({ behavior: "smooth" });
      }
    });
  });
}

/* ==========================================================================
   3. UPCOMING EXHIBITIONS RENDER & FILTER
   ========================================================================== */
function initUpcomingEvents() {
  const container = document.getElementById("events-grid-container");
  const filterBtns = document.querySelectorAll(".filter-btn");
  const searchInput = document.getElementById("home-event-search");
  const sortSelect = document.getElementById("home-event-sort");

  const eventsList = (window.EXPO_EVENTS && window.EXPO_EVENTS.length > 0) ? window.EXPO_EVENTS : exhibitionsData;

  let currentCity = "all";

  function renderEvents() {
    if (!container) return;

    let filtered = [...eventsList];

    // City Filter
    if (currentCity !== "all") {
      filtered = filtered.filter(e => (e.city || "").toLowerCase() === currentCity.toLowerCase());
    }

    // Search Query
    const query = searchInput?.value?.trim().toLowerCase();
    if (query) {
      filtered = filtered.filter(e => 
        (e.title || "").toLowerCase().includes(query) ||
        (e.venue || "").toLowerCase().includes(query) ||
        (e.city || "").toLowerCase().includes(query) ||
        (e.category || "").toLowerCase().includes(query)
      );
    }

    // Sort
    const sortVal = sortSelect?.value || "date_asc";
    if (sortVal === "price_asc") {
      filtered.sort((a, b) => (a.rawMinPrice || 4000) - (b.rawMinPrice || 4000));
    } else if (sortVal === "price_desc") {
      filtered.sort((a, b) => (b.rawMinPrice || 4000) - (a.rawMinPrice || 4000));
    } else if (sortVal === "stalls") {
      filtered.sort((a, b) => (b.availableStalls || 0) - (a.availableStalls || 0));
    } else {
      // date_asc
      filtered.sort((a, b) => (a.startDate || "").localeCompare(b.startDate || ""));
    }

    if (filtered.length === 0) {
      container.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 3.5rem 1.5rem; background: #ffffff; border-radius: 16px; border: 1.5px dashed var(--border-gold);">
          <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🎪</div>
          <h4 style="font-family: var(--font-cinzel); color: var(--burgundy-900); font-size: 1.3rem; margin-bottom: 0.35rem;">No Exhibitions Found</h4>
          <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.25rem;">Try adjusting your city filter or search keywords.</p>
          <button class="btn btn-outline-gold" onclick="document.getElementById('home-event-search').value=''; document.querySelector('.filter-btn[data-city=all]')?.click();">Reset Filters</button>
        </div>
      `;
      return;
    }

    container.innerHTML = filtered.map(item => {
      const availStalls = item.availableStalls !== undefined ? item.availableStalls : 15;
      const stallsBadge = availStalls <= 0 
        ? `<span style="background: #fee2e2; color: #dc2626; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 12px;">❌ Sold Out</span>`
        : (availStalls <= 5 
            ? `<span style="background: #fef3c7; color: #d97706; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 12px;">⚡ Only ${availStalls} Stalls Left</span>`
            : `<span style="background: #d1fae5; color: #065f46; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 12px;">✅ ${availStalls} Stalls Open</span>`);

      return `
      <div class="event-card" data-city="${(item.city || '').toLowerCase()}">
        <div class="event-card-header">
          <img src="${item.image}" alt="${item.title}" class="event-card-img" loading="lazy" />
          <div class="event-card-overlay"></div>
          <div class="event-date-badge">
            <div class="event-date-day">${item.date ? item.date.day : ''}</div>
            <div class="event-date-month">${item.date ? item.date.month : ''}</div>
          </div>
          <div class="event-city-badge">📍 ${item.city}</div>
        </div>

        <div class="event-card-body">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <span class="event-tagline-festive">${item.tagline || 'Lifestyle Exhibition'}</span>
            ${stallsBadge}
          </div>

          <h3 class="event-title">
            <a href="${item.detail_url || 'event.php?id=' + item.id}" style="color:inherit; text-decoration:none;">${item.title}</a>
          </h3>
          
          <div class="event-meta-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span><strong>${item.venue}</strong></span>
          </div>

          <div class="event-meta-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>${item.timing || '11:00 AM – 9:00 PM'}</span>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; background: #faf7f8; border: 1px solid #ebdada; border-radius: 8px; padding: 0.5rem 0.75rem; margin: 0.75rem 0 0.85rem;">
            <div style="font-size: 0.8rem; color: #666;">
              <span>Stalls:</span> <strong>Canopy / Table</strong>
            </div>
            <div style="text-align: right;">
              <span style="font-size: 0.72rem; color: #777;">Starting:</span>
              <strong style="color: var(--burgundy-900); font-family: var(--font-cinzel); font-size: 1.05rem; margin-left: 0.25rem;">${item.minPrice || '₹4,000'}</strong>
            </div>
          </div>

          <div class="event-card-footer" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
            <a href="${item.detail_url || 'event.php?id=' + item.id}" class="btn btn-outline-gold" style="font-size: 0.85rem; padding: 0.65rem 0.5rem; text-align: center; justify-content: center;">
              <span>View Details</span>
            </a>
            <a href="${item.book_url || ('book-a-stall.php?event_id=' + item.id)}" class="btn btn-burgundy" style="font-size: 0.85rem; padding: 0.65rem 0.5rem; text-align: center; justify-content: center;">
              <span>🎪 Book Stall</span>
            </a>
          </div>
        </div>
      </div>
    `;
    }).join('');
  }

  // Check URL params for city filter
  const urlParams = new URLSearchParams(window.location.search);
  currentCity = urlParams.get("city") || "all";

  // Initial render
  renderEvents();

  // Set active class on filter button matching URL param
  filterBtns.forEach(btn => {
    const btnCity = btn.getAttribute("data-city") || "all";
    if (btnCity.toLowerCase() === currentCity.toLowerCase()) {
      filterBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
    }
  });

  // Filter click handler
  filterBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      filterBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      currentCity = btn.getAttribute("data-city") || "all";
      renderEvents();
    });
  });

  // Search input handler
  searchInput?.addEventListener("input", () => {
    renderEvents();
  });

  // Sort select handler
  sortSelect?.addEventListener("change", () => {
    renderEvents();
  });
}

// Shortcut from event cards to Stall Booking Studio
window.selectEventForBooking = function(eventId) {
  // If on a page with wizard-exhibition-select, set and scroll
  const select = document.getElementById("wizard-exhibition-select");
  if (select) {
    select.value = eventId;
    updateStallSummary();
    document.getElementById("booking-studio")?.scrollIntoView({ behavior: "smooth" });
  } else {
    // Navigate to dedicated book-a-stall.php page with query param
    window.location.href = `book-a-stall.php?event_id=${eventId}`;
  }
};

// Shortcut from event cards to Shopper Pass
window.selectEventForPass = function(eventId) {
  const select = document.getElementById("shopper-event-select");
  if (select) {
    select.value = eventId;
    document.getElementById("shopper-pass")?.scrollIntoView({ behavior: "smooth" });
  } else {
    // Navigate to dedicated free-shopper-pass.php page with query param
    window.location.href = `free-shopper-pass.php?event_id=${eventId}`;
  }
};

/* ==========================================================================
   4. INTERACTIVE STALL BOOKING STUDIO (FOR EXHIBITORS)
   ========================================================================== */
function initStallBookingStudio() {
  const eventSelect = document.getElementById("wizard-exhibition-select");
  const categorySelect = document.getElementById("wizard-category-select");
  const brandNameInput = document.getElementById("wizard-brand-name");
  const stallCards = document.querySelectorAll(".stall-type-card");
  const addonCheckboxes = document.querySelectorAll(".addon-checkbox");
  const whatsappCtaBtn = document.getElementById("whatsapp-booking-btn");

  // Populate events in select dropdown
  const eventsSource = (window.EXPO_EVENTS && window.EXPO_EVENTS.length > 0) ? window.EXPO_EVENTS : exhibitionsData;
  if (eventSelect && eventSelect.children.length === 0) {
    eventSelect.innerHTML = eventsSource.map(e => `
      <option value="${e.id}">${e.title} (${e.city}) — ${e.date ? (e.date.day + ' ' + e.date.month) : ''}</option>
    `).join('');

    // Pre-select if URL has ?event=...
    const urlEvent = new URLSearchParams(window.location.search).get("event") || new URLSearchParams(window.location.search).get("event_id");
    if (urlEvent && eventsSource.some(e => String(e.id) === String(urlEvent))) {
      eventSelect.value = urlEvent;
    }
  }

  // Pre-select category if URL has ?category=...
  const urlCategory = new URLSearchParams(window.location.search).get("category");
  if (urlCategory && categorySelect) {
    categorySelect.value = urlCategory;
  }

  // Stall type selector
  stallCards.forEach(card => {
    card.addEventListener("click", () => {
      stallCards.forEach(c => c.classList.remove("selected"));
      card.classList.add("selected");
      updateStallSummary();
    });
  });

  // Watch input changes
  eventSelect?.addEventListener("change", updateStallSummary);
  categorySelect?.addEventListener("change", updateStallSummary);
  brandNameInput?.addEventListener("input", updateStallSummary);
  addonCheckboxes.forEach(cb => cb.addEventListener("change", updateStallSummary));

  // Initial update
  updateStallSummary();

  // WhatsApp click handler
  whatsappCtaBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    const eventsSource = (window.EXPO_EVENTS && window.EXPO_EVENTS.length > 0) ? window.EXPO_EVENTS : exhibitionsData;
    const eventObj = eventsSource.find(e => String(e.id) === String(eventSelect?.value)) || eventsSource[0];
    const category = categorySelect?.value || "Jewellery & Accessories";
    const brandName = brandNameInput?.value?.trim() || "My Brand";
    const selectedCard = document.querySelector(".stall-type-card.selected");
    const stallType = selectedCard?.getAttribute("data-stall") || "Prime Corner Canopy";

    const selectedAddons = [];
    addonCheckboxes.forEach(cb => {
      if (cb.checked) selectedAddons.push(cb.getAttribute("data-name"));
    });

    const addonsText = selectedAddons.length > 0 ? selectedAddons.join(", ") : "Standard Amenities";

    // Formatted WhatsApp message
    const msg = `Namaste Expo Tree Exhibitions! 🎪
I would like to enquire & book a stall for our brand:

• Brand Name: *${brandName}*
• Target Exhibition: *${eventObj.title}*
• Venue & City: *${eventObj.venue}, ${eventObj.city}* (${eventObj.date ? (eventObj.date.day + ' ' + eventObj.date.month) : ''})
• Category: *${category}*
• Preferred Stall Type: *${stallType}*
• Required Amenities: *${addonsText}*

Kindly share the stall layout blueprint, exact pricing quotation, and booking process. Thank you!`;

    const encoded = encodeURIComponent(msg);
    window.open(`https://wa.me/${EXPO_PHONE}?text=${encoded}`, "_blank");
  });
}

function updateStallSummary() {
  const eventSelect = document.getElementById("wizard-exhibition-select");
  const categorySelect = document.getElementById("wizard-category-select");
  const brandNameInput = document.getElementById("wizard-brand-name");
  const selectedCard = document.querySelector(".stall-type-card.selected");
  const addonCheckboxes = document.querySelectorAll(".addon-checkbox");

  const summaryEvent = document.getElementById("summary-event-name");
  const summaryCategory = document.getElementById("summary-category-name");
  const summaryStall = document.getElementById("summary-stall-type");
  const summaryAddons = document.getElementById("summary-addons-text");

  const eventsSource = (window.EXPO_EVENTS && window.EXPO_EVENTS.length > 0) ? window.EXPO_EVENTS : exhibitionsData;
  const eventObj = eventsSource.find(e => String(e.id) === String(eventSelect?.value)) || eventsSource[0];
  if (summaryEvent && eventObj) {
    summaryEvent.textContent = `${eventObj.title} (${eventObj.city})`;
  }

  if (summaryCategory && categorySelect) {
    summaryCategory.textContent = categorySelect.value || "Jewellery & Accessories";
  }

  if (summaryStall && selectedCard) {
    summaryStall.textContent = selectedCard.getAttribute("data-stall") || "Prime Corner Canopy";
  }

  const selectedAddons = [];
  addonCheckboxes.forEach(cb => {
    if (cb.checked) selectedAddons.push(cb.getAttribute("data-name"));
  });

  if (summaryAddons) {
    summaryAddons.textContent = selectedAddons.length > 0 ? selectedAddons.join(", ") : "Standard package";
  }
}

/* ==========================================================================
   5. SHOPPER VIP PASS & LUCKY DRAW GENERATOR ("Customer Jada Aai")
   ========================================================================== */
function initShopperPassGenerator() {
  const form = document.getElementById("shopper-pass-form");
  const passBox = document.getElementById("generated-vip-pass");
  const passHolderName = document.getElementById("pass-holder-name-display");
  const passIdBadge = document.getElementById("pass-id-number");
  const passEventBadge = document.getElementById("pass-event-name-display");
  const saveWhatsappBtn = document.getElementById("save-pass-whatsapp-btn");
  const eventSelect = document.getElementById("shopper-event-select");

  // Populate events in shopper dropdown
  const eventsSource = (window.EXPO_EVENTS && window.EXPO_EVENTS.length > 0) ? window.EXPO_EVENTS : exhibitionsData;
  if (eventSelect && eventSelect.children.length === 0) {
    eventSelect.innerHTML = eventsSource.map(e => `
      <option value="${e.id}">${e.title} (${e.city})</option>
    `).join('');

    // Pre-select if URL has ?event=...
    const urlEvent = new URLSearchParams(window.location.search).get("event") || new URLSearchParams(window.location.search).get("event_id");
    if (urlEvent && eventsSource.some(e => String(e.id) === String(urlEvent))) {
      eventSelect.value = urlEvent;
    }
  }

  form?.addEventListener("submit", (e) => {
    e.preventDefault();
    const name = document.getElementById("shopper-name")?.value?.trim() || "Festive Shopper";
    const phone = document.getElementById("shopper-phone")?.value?.trim() || "";
    const city = document.getElementById("shopper-city")?.value || "Delhi NCR";
    const selectedEventId = eventSelect?.value;
    const eventObj = eventsSource.find(e => String(e.id) === String(selectedEventId)) || eventsSource[0];

    // Generate random Pass ID
    const randomCode = Math.floor(1000 + Math.random() * 9000);
    const passCode = `EXPO-VIP-${randomCode}`;

    if (passHolderName) passHolderName.textContent = name;
    if (passIdBadge) passIdBadge.textContent = `PASS ID: ${passCode} • FREE VIP ENTRY`;
    if (passEventBadge) passEventBadge.textContent = `${eventObj.title} — ${eventObj.venue}, ${eventObj.city}`;

    // Hide form, show VIP Pass
    form.style.display = "none";
    if (passBox) {
      passBox.style.display = "block";
      passBox.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    // Trigger Festive Confetti
    launchConfetti();

    // Prepare WhatsApp Confirmation Link
    if (saveWhatsappBtn) {
      saveWhatsappBtn.onclick = () => {
        const msg = `Namaste Expo Tree Exhibitions! 🎟️
I have claimed my FREE VIP Entry Pass for *${eventObj.title}*!

• Name: *${name}*
• VIP Pass Code: *${passCode}*
• Venue: *${eventObj.venue}, ${eventObj.city}*
• Phone: *${phone}*

Please send me directions, exhibition schedule and entry into the *₹5,000 Festive Lucky Draw & Gift Hamper*! See you at the exhibition!`;
        window.open(`https://wa.me/${EXPO_PHONE}?text=${encodeURIComponent(msg)}`, "_blank");
      };
    }
  });
}

// Lightweight Pure Canvas Confetti Explosion
function launchConfetti() {
  try {
    const canvas = document.createElement("canvas");
    canvas.style.position = "fixed";
    canvas.style.top = "0";
    canvas.style.left = "0";
    canvas.style.width = "100vw";
    canvas.style.height = "100vh";
    canvas.style.pointerEvents = "none";
    canvas.style.zIndex = "9999";
    document.body.appendChild(canvas);

    const ctx = canvas.getContext("2d");
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const colors = ["#D4AF37", "#ECC554", "#931A2D", "#25D366", "#FFF9E6", "#FF9233"];
    const particles = [];

    for (let i = 0; i < 90; i++) {
      particles.push({
        x: canvas.width / 2,
        y: canvas.height / 2,
        w: Math.random() * 9 + 5,
        h: Math.random() * 5 + 3,
        color: colors[Math.floor(Math.random() * colors.length)],
        vx: (Math.random() - 0.5) * 14,
        vy: (Math.random() - 0.7) * 16,
        rotation: Math.random() * 360,
        rotationSpeed: (Math.random() - 0.5) * 10,
        opacity: 1
      });
    }

    let frames = 0;
    function animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      particles.forEach(p => {
        p.x += p.vx;
        p.y += p.vy;
        p.vy += 0.35; // gravity
        p.rotation += p.rotationSpeed;
        p.opacity -= 0.012;

        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate((p.rotation * Math.PI) / 180);
        ctx.globalAlpha = Math.max(0, p.opacity);
        ctx.fillStyle = p.color;
        ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
        ctx.restore();
      });

      frames++;
      if (frames < 90) {
        requestAnimationFrame(animate);
      } else {
        canvas.remove();
      }
    }
    requestAnimationFrame(animate);
  } catch (err) {
    console.error("Confetti animation note:", err);
  }
}

/* ==========================================================================
   6. LIVE SOCIAL PROOF NOTIFICATION CORNER (Bottom-Left)
   ========================================================================== */
function initLiveSocialProof() {
  const toast = document.getElementById("live-social-toast");
  const toastIcon = document.getElementById("toast-icon");
  const toastText = document.getElementById("toast-text");
  const toastTime = document.getElementById("toast-time");
  const closeBtn = document.getElementById("toast-close-btn");

  if (!toast) return;

  let currentIndex = 0;

  function showNextNotification() {
    const item = liveNotifications[currentIndex];
    if (toastIcon) toastIcon.textContent = item.icon;
    if (toastText) {
      toastText.innerHTML = `<strong>${item.name}</strong> (${item.city}) ${item.action} for <em>${item.event}</em>!`;
    }
    if (toastTime) {
      toastTime.textContent = "A few moments ago • Verified Activity";
    }

    toast.classList.add("show");

    // Hide after 6 seconds
    setTimeout(() => {
      toast.classList.remove("show");
    }, 6000);

    currentIndex = (currentIndex + 1) % liveNotifications.length;
  }

  // Start after 4 seconds, then repeat every 14 seconds
  setTimeout(() => {
    showNextNotification();
    setInterval(showNextNotification, 14000);
  }, 4000);

  closeBtn?.addEventListener("click", () => {
    toast.classList.remove("show");
  });
}

/* ==========================================================================
   7. ANIMATED STATS COUNTER
   ========================================================================== */
function initStatsCounter() {
  const statNumbers = document.querySelectorAll(".stat-number");
  let animated = false;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !animated) {
        animated = true;
        statNumbers.forEach(counter => {
          const target = parseFloat(counter.getAttribute("data-target") || "0");
          const prefix = counter.getAttribute("data-prefix") || "";
          const suffix = counter.getAttribute("data-suffix") || "";
          const isDecimal = target % 1 !== 0;
          let count = 0;
          const speed = 40;
          const step = target / speed;

          const timer = setInterval(() => {
            count += step;
            if (count >= target) {
              counter.textContent = `${prefix}${isDecimal ? target.toFixed(1) : Math.floor(target)}${suffix}`;
              clearInterval(timer);
            } else {
              counter.textContent = `${prefix}${isDecimal ? count.toFixed(1) : Math.floor(count)}${suffix}`;
            }
          }, 35);
        });
      }
    });
  }, { threshold: 0.3 });

  const statsSection = document.querySelector(".stats-section");
  if (statsSection) observer.observe(statsSection);
}

/* ==========================================================================
   8. REAL PHOTO GALLERY & LIGHTBOX
   ========================================================================== */
function initGallery() {
  const masonryContainer = document.getElementById("gallery-masonry");
  const filterBtns = document.querySelectorAll(".gallery-tab-btn");
  const lightbox = document.getElementById("lightbox-modal");
  const lightboxImg = document.getElementById("lightbox-image");
  const lightboxCaption = document.getElementById("lightbox-caption-text");
  const lightboxClose = document.getElementById("lightbox-close");
  const prevBtn = document.getElementById("lightbox-prev");
  const nextBtn = document.getElementById("lightbox-next");

  let activeList = [...galleryData];
  let currentLightboxIndex = 0;

  function renderGallery(cat = "all") {
    if (!masonryContainer) return;
    activeList = cat === "all" ? galleryData : galleryData.filter(g => g.category === cat);

    masonryContainer.innerHTML = activeList.map((item, idx) => `
      <div class="gallery-item" onclick="openLightbox(${idx})">
        <img src="${item.src}" alt="${item.title}" loading="lazy" />
        <div class="gallery-overlay">
          <div class="gallery-overlay-title">${item.title}</div>
          <div class="gallery-overlay-sub">${item.sub}</div>
        </div>
      </div>
    `).join('');
  }

  renderGallery("all");

  filterBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      filterBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      const cat = btn.getAttribute("data-gallery-cat") || "all";
      renderGallery(cat);
    });
  });

  window.openLightbox = function(index) {
    currentLightboxIndex = index;
    updateLightbox();
    lightbox?.classList.add("active");
  };

  function updateLightbox() {
    const item = activeList[currentLightboxIndex];
    if (!item) return;
    if (lightboxImg) lightboxImg.src = item.src;
    if (lightboxCaption) lightboxCaption.textContent = `${item.title} — ${item.sub}`;
  }

  lightboxClose?.addEventListener("click", () => {
    lightbox?.classList.remove("active");
  });

  lightbox?.addEventListener("click", (e) => {
    if (e.target === lightbox) lightbox.classList.remove("active");
  });

  prevBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    currentLightboxIndex = (currentLightboxIndex - 1 + activeList.length) % activeList.length;
    updateLightbox();
  });

  nextBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    currentLightboxIndex = (currentLightboxIndex + 1) % activeList.length;
    updateLightbox();
  });

  document.addEventListener("keydown", (e) => {
    if (!lightbox?.classList.contains("active")) return;
    if (e.key === "Escape") lightbox.classList.remove("active");
    if (e.key === "ArrowLeft") prevBtn?.click();
    if (e.key === "ArrowRight") nextBtn?.click();
  });
}

/* ==========================================================================
   9. FAQ ACCORDION
   ========================================================================== */
function initFaqAccordion() {
  const faqTabBtns = document.querySelectorAll(".faq-tab-btn");
  const faqContainers = document.querySelectorAll(".faq-list-tab");

  faqTabBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      faqTabBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      const targetTab = btn.getAttribute("data-faq-tab");

      faqContainers.forEach(container => {
        if (container.getAttribute("data-faq-content") === targetTab) {
          container.style.display = "block";
        } else {
          container.style.display = "none";
        }
      });
    });
  });

  // Accordion item expand / collapse
  const faqItems = document.querySelectorAll(".faq-item");
  faqItems.forEach(item => {
    const questionBtn = item.querySelector(".faq-question");
    questionBtn?.addEventListener("click", () => {
      const isActive = item.classList.contains("active");
      // Close all in this tab
      const parent = item.closest(".faq-list-tab");
      parent?.querySelectorAll(".faq-item").forEach(i => i.classList.remove("active"));
      if (!isActive) {
        item.classList.add("active");
      }
    });
  });
}

/* ==========================================================================
   10. LEGAL & POLICY MODALS
   ========================================================================== */
function initLegalModals() {
  const privacyLinks = document.querySelectorAll(".open-privacy-modal");
  const termsLinks = document.querySelectorAll(".open-terms-modal");
  const privacyModal = document.getElementById("privacy-modal");
  const termsModal = document.getElementById("terms-modal");
  const closeBtns = document.querySelectorAll(".legal-modal-close");

  privacyLinks.forEach(link => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      privacyModal?.classList.add("active");
    });
  });

  termsLinks.forEach(link => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      termsModal?.classList.add("active");
    });
  });

  closeBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      privacyModal?.classList.remove("active");
      termsModal?.classList.remove("active");
    });
  });

  [privacyModal, termsModal].forEach(modal => {
    modal?.addEventListener("click", (e) => {
      if (e.target === modal) modal.classList.remove("active");
    });
  });
}
