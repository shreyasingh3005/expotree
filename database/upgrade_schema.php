<?php
/**
 * Database Migration & Schema Upgrade Script
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

echo "=== Upgrading Expo Tree Database Schema ===\n";

try {
    // 1. Upgrade `events` status column to support complete workflow:
    // draft, pending, approved, published, unpublished, rejected, active, inactive, archived
    $pdo->exec("
        ALTER TABLE `events` 
        MODIFY COLUMN `status` ENUM('draft', 'pending', 'approved', 'published', 'unpublished', 'rejected', 'active', 'inactive', 'archived') 
        NOT NULL DEFAULT 'published'
    ");
    echo "[OK] `events.status` ENUM upgraded.\n";

    // Migrate any existing 'active' events to 'published'
    $pdo->exec("UPDATE `events` SET `status` = 'published' WHERE `status` = 'active'");
    echo "[OK] Migrated active events to published status.\n";

    // 2. Add missing fields to `events` table if they do not exist
    $existingCols = $pdo->query("DESCRIBE `events`")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('stall_sizes', $existingCols)) {
        $pdo->exec("ALTER TABLE `events` ADD COLUMN `stall_sizes` VARCHAR(255) DEFAULT 'Canopy: 3x3m • Table: 6x2ft' AFTER `booked_stalls`");
        echo "[OK] Added `stall_sizes` to `events`.\n";
    }

    if (!in_array('facilities', $existingCols)) {
        $pdo->exec("ALTER TABLE `events` ADD COLUMN `facilities` TEXT DEFAULT NULL AFTER `stall_sizes`");
        echo "[OK] Added `facilities` to `events`.\n";
    }

    if (!in_array('booking_instructions', $existingCols)) {
        $pdo->exec("ALTER TABLE `events` ADD COLUMN `booking_instructions` TEXT DEFAULT NULL AFTER `facilities`");
        echo "[OK] Added `booking_instructions` to `events`.\n";
    }

    if (!in_array('terms_conditions', $existingCols)) {
        $pdo->exec("ALTER TABLE `events` ADD COLUMN `terms_conditions` TEXT DEFAULT NULL AFTER `booking_instructions`");
        echo "[OK] Added `terms_conditions` to `events`.\n";
    }

    if (!in_array('venue_images_json', $existingCols)) {
        $pdo->exec("ALTER TABLE `events` ADD COLUMN `venue_images_json` TEXT DEFAULT NULL AFTER `banner_url`");
        echo "[OK] Added `venue_images_json` to `events`.\n";
    }

    if (!in_array('rejection_reason', $existingCols)) {
        $pdo->exec("ALTER TABLE `events` ADD COLUMN `rejection_reason` TEXT DEFAULT NULL AFTER `source`");
        echo "[OK] Added `rejection_reason` to `events`.\n";
    }

    // 3. Create `event_stalls` table for granular stall type & inventory tracking
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `event_stalls` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `event_id` INT NOT NULL,
            `stall_type` VARCHAR(100) NOT NULL,
            `stall_name` VARCHAR(150) NOT NULL,
            `stall_size` VARCHAR(100) DEFAULT '3x3m',
            `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `total_quantity` INT NOT NULL DEFAULT 10,
            `available_quantity` INT NOT NULL DEFAULT 10,
            `booked_quantity` INT NOT NULL DEFAULT 0,
            `status` ENUM('available', 'reserved', 'booked', 'unavailable') NOT NULL DEFAULT 'available',
            `facilities` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_event_id` (`event_id`),
            INDEX `idx_stall_status` (`status`),
            CONSTRAINT `fk_event_stalls_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "[OK] `event_stalls` table created/verified.\n";

    // 4. Create `site_settings` table for admin CMS configuration
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `site_settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `setting_key` VARCHAR(100) NOT NULL UNIQUE,
            `setting_value` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_setting_key` (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "[OK] `site_settings` table created/verified.\n";

    // 5. Populate default site settings if empty
    $defaultSettings = [
        'site_name' => 'Expo Tree Exhibitions',
        'site_title' => 'Expo Tree Exhibitions | Delhi NCR Premier Lifestyle & Festive Exhibitions',
        'admin_phone' => '9811175057',
        'admin_whatsapp' => '9811175057',
        'admin_email' => 'expotreeexhibitions@gmail.com',
        'instagram_handle' => 'expo_tree_exhibitions',
        'instagram_followers' => '88.9K+',
        'office_address' => 'DLF CyberHub & Ambience Mall Corridor, Gurugram / Sector 18 Noida, Delhi NCR',
        'hero_title' => 'Festive Melas & Lifestyle Exhibitions',
        'hero_subtitle' => 'Shop Handcrafted Wonders • Book Prime Footfall Stalls',
        'hero_description' => 'Curating high-energy festive exhibitions across Gurugram, Noida, and Delhi. Connecting 100+ boutique designers, handcrafted artisans, and home-grown brands with thousands of eager shoppers.',
        'meta_description' => 'Shop, Explore, Support, Indulge. Delhi NCR premier lifestyle & festive exhibitions organizer across Gurugram, Noida, Delhi. Book stalls or claim free shopper pass. Hotline: 9811175057'
    ];

    $checkSetting = $pdo->prepare("SELECT COUNT(*) FROM `site_settings` WHERE `setting_key` = ?");
    $insertSetting = $pdo->prepare("INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES (?, ?)");

    foreach ($defaultSettings as $k => $v) {
        $checkSetting->execute([$k]);
        if ($checkSetting->fetchColumn() == 0) {
            $insertSetting->execute([$k, $v]);
        }
    }
    echo "[OK] Default settings populated.\n";

    echo "=== Database Schema Upgrade Complete ===\n";
} catch (Exception $e) {
    echo "[ERROR] Schema upgrade failed: " . $e->getMessage() . "\n";
}
