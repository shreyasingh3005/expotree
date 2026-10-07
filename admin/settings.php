<?php
/**
 * Website Settings & CMS Configuration
 * Expo Tree Exhibitions Admin
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$adminTitle = 'Website Settings & Content';
$activeMenu = 'settings';

$errors = [];
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token expired. Please refresh the page.';
    } else {
        $fields = [
            'site_name' => sanitize($_POST['site_name'] ?? ''),
            'site_title' => sanitize($_POST['site_title'] ?? ''),
            'admin_phone' => sanitize($_POST['admin_phone'] ?? ''),
            'admin_whatsapp' => sanitize($_POST['admin_whatsapp'] ?? ''),
            'admin_email' => sanitize($_POST['admin_email'] ?? ''),
            'office_address' => sanitize($_POST['office_address'] ?? ''),
            'instagram_handle' => sanitize($_POST['instagram_handle'] ?? ''),
            'instagram_followers' => sanitize($_POST['instagram_followers'] ?? ''),
            'hero_title' => sanitize($_POST['hero_title'] ?? ''),
            'hero_subtitle' => sanitize($_POST['hero_subtitle'] ?? ''),
            'hero_description' => sanitize($_POST['hero_description'] ?? ''),
            'meta_description' => sanitize($_POST['meta_description'] ?? ''),
            'top_announcement' => sanitize($_POST['top_announcement'] ?? '')
        ];

        foreach ($fields as $k => $v) {
            set_setting($k, $v);
        }

        setFlash('success', 'Website settings have been updated successfully!');
        header('Location: ' . BASE_URL . '/admin/settings.php');
        exit;
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Website Settings &amp; CMS Content</h3>
    <p style="font-size: 0.85rem; color: #6b7280;">
      Control phone hotlines, WhatsApp numbers, SEO metadata, Instagram metrics, and homepage copy.
    </p>
  </div>
</div>

<form method="POST" action="settings.php">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">

    <!-- Contact & Helpline Details Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h4 style="font-size: 1rem; font-weight: 700; color: #111827;">📞 Contact &amp; Hotline Configuration</h4>
      </div>
      <div class="admin-card-body">
        
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Primary Hotline Phone:</label>
          <input type="text" name="admin_phone" value="<?= e(get_setting('admin_phone', ADMIN_PHONE)) ?>" required
                 style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;" />
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">WhatsApp Number (10 digits):</label>
          <input type="text" name="admin_whatsapp" value="<?= e(get_setting('admin_whatsapp', ADMIN_WHATSAPP)) ?>" required
                 style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;" />
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Support Email:</label>
          <input type="email" name="admin_email" value="<?= e(get_setting('admin_email', ADMIN_EMAIL)) ?>" required
                 style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;" />
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Operational Office Address:</label>
          <input type="text" name="office_address" value="<?= e(get_setting('office_address', 'DLF CyberHub & Ambience Mall Corridor, Gurugram, Delhi NCR')) ?>"
                 style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;" />
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Top Bar Announcement Text:</label>
          <input type="text" name="top_announcement" value="<?= e(get_setting('top_announcement', 'Delhi NCR Premier Lifestyle & Festive Exhibition Partner • Hotline: 9811175057')) ?>"
                 style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;" />
        </div>

      </div>
    </div>

    <!-- Social Channels & Community Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h4 style="font-size: 1rem; font-weight: 700; color: #111827;">📱 Social Proof &amp; Brand Settings</h4>
      </div>
      <div class="admin-card-body">
        
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Website Brand Name:</label>
          <input type="text" name="site_name" value="<?= e(get_setting('site_name', SITE_NAME)) ?>" required
                 style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;" />
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Instagram Handle (without @):</label>
          <input type="text" name="instagram_handle" value="<?= e(get_setting('instagram_handle', INSTAGRAM_HANDLE)) ?>" required
                 style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;" />
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Instagram Follower Display Count:</label>
          <input type="text" name="instagram_followers" value="<?= e(get_setting('instagram_followers', INSTAGRAM_FOLLOWERS)) ?>" required
                 style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;" />
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Default SEO Meta Title:</label>
          <input type="text" name="site_title" value="<?= e(get_setting('site_title', 'Expo Tree Exhibitions | Premier Lifestyle & Festive Exhibitions Delhi NCR')) ?>"
                 style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;" />
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Default SEO Meta Description:</label>
          <textarea name="meta_description" rows="2" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.85rem;"><?= e(get_setting('meta_description', 'Delhi NCR premier lifestyle and festive exhibitions organizer. Book stalls across Gurugram, Noida, Delhi. Hotline: 9811175057')) ?></textarea>
        </div>

      </div>
    </div>

  </div>

  <!-- Homepage Hero Copy CMS Card -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h4 style="font-size: 1rem; font-weight: 700; color: #111827;">✨ Homepage Hero Section Copywriting</h4>
    </div>
    <div class="admin-card-body">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Hero Headline (H1):</label>
          <input type="text" name="hero_title" value="<?= e(get_setting('hero_title', 'Festive Melas & Lifestyle Exhibitions')) ?>"
                 style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;" />
        </div>
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Hero Subtitle:</label>
          <input type="text" name="hero_subtitle" value="<?= e(get_setting('hero_subtitle', 'Shop Handcrafted Wonders • Book Prime Footfall Stalls')) ?>"
                 style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;" />
        </div>
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem;">Hero Summary Paragraph:</label>
        <textarea name="hero_description" rows="2" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem;"><?= e(get_setting('hero_description', 'Curating high-energy festive exhibitions across Gurugram, Noida, and Delhi. Connecting 100+ boutique designers, handcrafted artisans, and home-grown brands with thousands of eager shoppers.')) ?></textarea>
      </div>

      <button type="submit" class="admin-btn admin-btn-gold" style="padding: 0.75rem 2rem;">
        <span>💾 Save All Settings</span>
      </button>
    </div>
  </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
