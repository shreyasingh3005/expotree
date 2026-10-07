<?php
/**
 * Free Shopper VIP Pass Generator
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Free VIP Shopper Entry Pass | Expo Tree Exhibitions';
$pageDesc = 'Get your 100% Free VIP Entry Pass for upcoming lifestyle & festive exhibitions in Gurugram, Noida, Delhi NCR. Exclusive discounts and lucky draw coupons!';
$currentPage = 'free-shopper-pass';

// Fetch active events
$evStmt = $pdo->query("SELECT id, title, venue, city, start_date, end_date, date_display FROM events WHERE status IN ('published', 'active') ORDER BY start_date ASC");
$activeEvents = $evStmt->fetchAll();

$passSuccess = false;
$generatedPass = null;
$formErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_pass') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $formErrors[] = 'Security token invalid. Please refresh the page.';
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $city = sanitize($_POST['city'] ?? 'Gurugram');
        $eventId = !empty($_POST['event_id']) ? (int)$_POST['event_id'] : null;

        if (empty($name)) $formErrors[] = 'Full name is required.';
        if (empty($phone) || strlen($phone) < 10) $formErrors[] = 'Valid 10-digit mobile number is required.';

        if (empty($formErrors)) {
            $passCode = generatePassCode();

            $insStmt = $pdo->prepare("INSERT INTO shopper_passes (pass_code, event_id, name, phone, city) VALUES (?, ?, ?, ?, ?)");
            $res = $insStmt->execute([$passCode, $eventId, $name, $phone, $city]);

            if ($res) {
                // Fetch event details if selected
                $eventName = 'All Delhi NCR Exhibitions';
                if ($eventId) {
                    $evNameStmt = $pdo->prepare("SELECT title, venue, city, date_display FROM events WHERE id = ?");
                    $evNameStmt->execute([$eventId]);
                    $evInfo = $evNameStmt->fetch();
                    if ($evInfo) {
                        $eventName = $evInfo['title'] . ' (' . $evInfo['venue'] . ', ' . $evInfo['city'] . ')';
                    }
                }

                $generatedPass = [
                    'pass_code' => $passCode,
                    'name' => $name,
                    'phone' => $phone,
                    'city' => $city,
                    'event_name' => $eventName
                ];
                $passSuccess = true;
            } else {
                $formErrors[] = 'Could not generate pass. Please try again or WhatsApp ' . ADMIN_PHONE . '.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section page-hero-section">
  <div class="hero-bg-photo" aria-hidden="true" style="opacity: 0.18;"></div>
  <div class="container" style="position: relative; z-index: 2;">
    <span class="section-badge badge-gold">✦ 100% Free Entry Registration</span>
    <h1 class="section-title title-white">Claim Your Free VIP Shopper Pass</h1>
    <div class="festive-divider"><span class="festive-divider-icon">✦</span></div>
    <p class="section-subtitle subtitle-white" style="max-width: 780px; margin: 0 auto;">
      Enjoy free entry, express registration queues, exclusive exhibitor discounts, and entry into our hourly Festive Lucky Draw across all Delhi NCR exhibitions!
    </p>
  </div>
</section>

<!-- Content Section -->
<section class="section" style="padding-top: 3.5rem; padding-bottom: 5.5rem;">
  <div class="container" style="max-width: 950px;">

    <!-- Generated VIP Pass Card -->
    <?php if ($passSuccess && $generatedPass): ?>
      <div style="background: linear-gradient(135deg, #2b070d 0%, #150205 100%); border: 2px solid var(--gold-500); border-radius: 24px; padding: 2.5rem 2rem; color: #ffffff; box-shadow: 0 20px 50px rgba(0,0,0,0.5); margin-bottom: 3.5rem; text-align: center; position: relative; overflow: hidden;">
        <div style="position: absolute; top: -30px; right: -30px; width: 140px; height: 140px; background: radial-gradient(circle, rgba(212,175,55,0.2) 0%, transparent 70%); border-radius: 50%;"></div>

        <div style="font-size: 3rem; margin-bottom: 0.35rem;">🎟️</div>
        <span class="section-badge badge-gold" style="font-size: 0.78rem; margin-bottom: 0.75rem;">Verified VIP Pass</span>
        <h2 style="font-family: var(--font-cinzel); font-size: 2rem; color: var(--gold-300); margin-bottom: 0.25rem;">Expo Tree VIP Shopper Pass</h2>
        <p style="color: #e5c9cf; font-size: 0.95rem; margin-bottom: 1.5rem;">Present this digital badge or your Pass Code at the entry desk for instant priority access.</p>

        <!-- Digital Ticket Card -->
        <div style="background: #ffffff; color: #1a0408; border-radius: 16px; padding: 1.5rem; max-width: 480px; margin: 0 auto 1.75rem; box-shadow: 0 10px 30px rgba(0,0,0,0.3); border: 1.5px solid var(--border-gold); text-align: left;">
          <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px dashed #ebdada; padding-bottom: 1rem; margin-bottom: 1rem;">
            <img src="<?= BASE_URL ?>/images/logo.svg" alt="Expo Tree Logo" width="130" />
            <div style="text-align: right;">
              <span style="font-size: 0.75rem; color: #888; text-transform: uppercase;">Pass Code</span>
              <div style="font-size: 1.35rem; font-weight: 800; color: var(--burgundy-900); font-family: monospace; letter-spacing: 0.05em;"><?= e($generatedPass['pass_code']) ?></div>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.9rem; margin-bottom: 1rem;">
            <div><span style="color: #777;">VIP Guest:</span><br><strong><?= e($generatedPass['name']) ?></strong></div>
            <div><span style="color: #777;">Mobile:</span><br><strong><?= e($generatedPass['phone']) ?></strong></div>
            <div style="grid-column: span 2;"><span style="color: #777;">Access:</span><br><strong><?= e($generatedPass['event_name']) ?></strong></div>
          </div>

          <div style="background: #fdfaf6; border-radius: 8px; padding: 0.6rem; text-align: center; font-size: 0.78rem; color: var(--burgundy-950); font-weight: 600;">
            ✨ Free Entry • Express Queue • Hourly Lucky Draw Included
          </div>
        </div>

        <?php
          $waShareText = "Namaste Expo Tree! I have claimed my Free VIP Shopper Pass (" . $generatedPass['pass_code'] . ") for " . $generatedPass['name'] . ". Excited to visit!";
          $waShareUrl = 'https://wa.me/' . ADMIN_WHATSAPP . '?text=' . urlencode($waShareText);
        ?>
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
          <a href="<?= $waShareUrl ?>" target="_blank" rel="noopener noreferrer" class="btn" style="background: #25D366; color: #ffffff; font-size: 1rem; padding: 0.8rem 1.75rem;">
            <span>💬 Save &amp; Confirm on WhatsApp</span>
          </a>
          <button type="button" onclick="window.print();" class="btn btn-gold">
            <span>🖨️ Print / Save Pass</span>
          </button>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($formErrors)): ?>
      <div class="site-flash-alert alert-danger" style="margin-bottom: 2rem; border-radius: 12px;">
        <div class="container site-flash-inner" style="flex-direction: column; align-items: flex-start;">
          <strong>Please resolve the following:</strong>
          <ul style="margin: 0.5rem 0 0 1.25rem;">
            <?php foreach ($formErrors as $err): ?>
              <li><?= e($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>

    <!-- Two-Column Layout -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem; align-items: start;">
      
      <!-- Left: Pass Form -->
      <div style="background: #ffffff; border: 1.5px solid var(--border-gold); border-radius: 20px; padding: 2rem; box-shadow: var(--shadow-card);">
        <h3 style="font-family: var(--font-cinzel); font-size: 1.35rem; color: var(--burgundy-950); margin-bottom: 0.5rem;">
          Instant VIP Entry Registration
        </h3>
        <p style="font-size: 0.88rem; color: #666; margin-bottom: 1.5rem;">
          Zero registration charges. Valid for you + family members.
        </p>

        <form method="POST" action="free-shopper-pass.php">
          <input type="hidden" name="action" value="generate_pass" />
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />

          <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.35rem;">
              Full Name: <span style="color: #dc2626;">*</span>
            </label>
            <input type="text" name="name" placeholder="e.g. Meenakshi Sharma" required 
                   style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.35rem;">
              Mobile Number (10 Digits): <span style="color: #dc2626;">*</span>
            </label>
            <input type="tel" name="phone" placeholder="98111XXXXX" pattern="[0-9]{10}" required 
                   style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem;" />
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.35rem;">
              Your City:
            </label>
            <select name="city" style="width: 100%; padding: 0.75rem 0.85rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem; background: #fff;">
              <option value="Gurugram" selected>Gurugram</option>
              <option value="Noida">Noida</option>
              <option value="Delhi">Delhi</option>
              <option value="Faridabad">Faridabad</option>
              <option value="Ghaziabad">Ghaziabad</option>
              <option value="Greater Noida">Greater Noida</option>
            </select>
          </div>

          <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--burgundy-950); margin-bottom: 0.35rem;">
              Select Exhibition to Attend:
            </label>
            <select name="event_id" style="width: 100%; padding: 0.75rem 0.85rem; border: 1.5px solid #d8c3c7; border-radius: 10px; font-size: 0.92rem; background: #fff;">
              <option value="">All Upcoming Exhibitions</option>
              <?php foreach ($activeEvents as $aev): ?>
                <option value="<?= $aev['id'] ?>">
                  <?= e($aev['title']) ?> (<?= e($aev['venue']) ?>, <?= e($aev['city']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <button type="submit" class="btn btn-gold" style="width: 100%; justify-content: center; font-size: 1.05rem; padding: 0.85rem; box-shadow: 0 4px 15px rgba(212,175,55,0.4);">
            <span>🎟️ Generate My VIP Pass</span>
          </button>
        </form>
      </div>

      <!-- Right: VIP Perks Box -->
      <div>
        <div style="background: radial-gradient(circle at 50% 30%, #30060e 0%, #170205 100%); border: 1.5px solid var(--border-gold); border-radius: 20px; padding: 2rem; color: #ffffff; box-shadow: var(--shadow-card);">
          <span class="section-badge badge-gold" style="font-size: 0.72rem; margin-bottom: 0.75rem;">VIP Privileges</span>
          <h3 style="font-family: var(--font-cinzel); font-size: 1.4rem; color: var(--gold-300); margin-bottom: 1.25rem;">
            Why Register in Advance?
          </h3>

          <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div style="display: flex; gap: 1rem; align-items: flex-start;">
              <div style="font-size: 1.6rem; line-height: 1;">⚡</div>
              <div>
                <h4 style="font-size: 1rem; color: #ffffff; margin-bottom: 0.2rem;">Skip the Walk-in Queue</h4>
                <p style="font-size: 0.84rem; color: #dcb8bf; margin: 0;">Show your digital badge on your phone for immediate express entry even during peak festive rush.</p>
              </div>
            </div>

            <div style="display: flex; gap: 1rem; align-items: flex-start;">
              <div style="font-size: 1.6rem; line-height: 1;">🎁</div>
              <div>
                <h4 style="font-size: 1rem; color: #ffffff; margin-bottom: 0.2rem;">Exclusive Stall Discounts</h4>
                <p style="font-size: 0.84rem; color: #dcb8bf; margin: 0;">Access special 10% to 20% privilege discounts at participating designer jewellery &amp; apparel stalls.</p>
              </div>
            </div>

            <div style="display: flex; gap: 1rem; align-items: flex-start;">
              <div style="font-size: 1.6rem; line-height: 1;">🏆</div>
              <div>
                <h4 style="font-size: 1rem; color: #ffffff; margin-bottom: 0.2rem;">Hourly Lucky Draw Entry</h4>
                <p style="font-size: 0.84rem; color: #dcb8bf; margin: 0;">Every registered pass holder is entered into our hourly gift hampers and luxury festive voucher giveaways.</p>
              </div>
            </div>

            <div style="display: flex; gap: 1rem; align-items: flex-start;">
              <div style="font-size: 1.6rem; line-height: 1;">🅿️</div>
              <div>
                <h4 style="font-size: 1rem; color: #ffffff; margin-bottom: 0.2rem;">Parking &amp; Venue Directions</h4>
                <p style="font-size: 0.84rem; color: #dcb8bf; margin: 0;">Receive real-time WhatsApp updates with exact Google Maps navigation and covered parking zones.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
