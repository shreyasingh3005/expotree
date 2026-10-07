-- Expo Tree Exhibitions Database Schema
-- Complete Event & Stall Booking System

CREATE DATABASE IF NOT EXISTS `expotree_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `expotree_db`;

-- 1. Admin Users Table
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(60) NOT NULL UNIQUE,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) DEFAULT 'Admin',
    `role` ENUM('admin', 'manager') DEFAULT 'admin',
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Events Table (Incorporating all Excel columns & organizer fields)
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `category` VARCHAR(100) DEFAULT 'Lifestyle & Festive',
    `event_type` VARCHAR(100) NOT NULL DEFAULT 'Exhibition', -- Corporate, Society, Mall, Exhibition
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `date_display` VARCHAR(100) NULL, -- e.g. "18-19-20 October 2026", "27-28 Oct"
    `venue` VARCHAR(255) NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `state` VARCHAR(100) NOT NULL DEFAULT 'Delhi NCR',
    `pincode` VARCHAR(20) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `location_type` VARCHAR(50) DEFAULT 'Outdoors', -- Indoors, Outdoors
    `footfall` VARCHAR(100) DEFAULT NULL, -- e.g. "3k employees", "1000 families"
    `gentry` VARCHAR(100) DEFAULT 'Middle Class', -- Middle Class, Premium Class
    `timings` VARCHAR(100) DEFAULT '10:00 AM - 8:00 PM', -- e.g. "10-8 PM", "4-10 PM"
    `layout_type` VARCHAR(100) DEFAULT 'First come first serve', -- First come first serve, Layout blueprint available
    `fan_charge` VARCHAR(50) DEFAULT '300 RS', -- 300 RS, -
    
    -- Specific Excel Pricing Columns
    `price_shopping_canopy` DECIMAL(10,2) DEFAULT NULL, -- Canopy (2 Tables + 2 Chairs + light)
    `price_shopping_table1` DECIMAL(10,2) DEFAULT NULL, -- 1 Open table (1 Table + 1 Chair)
    `price_shopping_table2` DECIMAL(10,2) DEFAULT NULL, -- 2 open tables (2 Tables + 2 chairs)
    `price_food_canopy` DECIMAL(10,2) DEFAULT NULL,     -- Food Canopy
    `price_food_table2` DECIMAL(10,2) DEFAULT NULL,     -- Food 2 open tables
    `price_promotional` VARCHAR(100) DEFAULT '10k onwards', -- Promotional Canopy (10k onwards, 20k onwards)
    
    -- General / Default daily stall price for simple calculations
    `daily_stall_price` DECIMAL(10,2) NOT NULL DEFAULT 5000.00,
    
    -- Stalls management
    `total_stalls` INT NOT NULL DEFAULT 20,
    `available_stalls` INT NOT NULL DEFAULT 20,
    `booked_stalls` INT NOT NULL DEFAULT 0,
    
    -- Content & Visuals
    `description` TEXT DEFAULT NULL,
    `image_url` VARCHAR(500) DEFAULT NULL,
    `banner_url` VARCHAR(500) DEFAULT NULL,
    `highlights` TEXT DEFAULT NULL, -- Comma-separated or JSON
    `map_link` TEXT DEFAULT NULL,
    
    -- Organizer Information
    `organizer_name` VARCHAR(150) DEFAULT 'Expo Tree Exhibitions',
    `organizer_company` VARCHAR(150) DEFAULT 'Expo Tree Events Pvt Ltd',
    `organizer_phone` VARCHAR(30) DEFAULT '9811175057',
    `organizer_whatsapp` VARCHAR(30) DEFAULT '9811175057',
    `organizer_email` VARCHAR(150) DEFAULT 'expotreeexhibitions@gmail.com',
    `organizer_website` VARCHAR(255) DEFAULT NULL,
    
    -- Status & Controls (Auto-approved by default)
    `status` ENUM('active', 'inactive', 'archived') NOT NULL DEFAULT 'active',
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `source` ENUM('admin', 'organizer_submission') NOT NULL DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_dates` (`start_date`, `end_date`),
    INDEX `idx_city` (`city`),
    INDEX `idx_type` (`event_type`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bookings Table
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_number` VARCHAR(50) NOT NULL UNIQUE,
    `event_id` INT NOT NULL,
    `customer_name` VARCHAR(150) NOT NULL,
    `business_name` VARCHAR(150) DEFAULT NULL,
    `mobile` VARCHAR(30) NOT NULL,
    `whatsapp` VARCHAR(30) NOT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `category_name` VARCHAR(100) DEFAULT 'Jewellery & Accessories',
    `stall_type` VARCHAR(150) NOT NULL, -- e.g. Shopping Canopy, 1 Open Table, Food Canopy, etc.
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `days_count` INT NOT NULL DEFAULT 1,
    `stalls_count` INT NOT NULL DEFAULT 1,
    `price_per_day` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `addons_json` TEXT DEFAULT NULL,
    `addons_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `notes` TEXT DEFAULT NULL,
    `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'confirmed',
    `whatsapp_sent` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_bookings_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Free Shopper VIP Passes Table
CREATE TABLE IF NOT EXISTS `shopper_passes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pass_code` VARCHAR(50) NOT NULL UNIQUE,
    `event_id` INT DEFAULT NULL,
    `name` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
