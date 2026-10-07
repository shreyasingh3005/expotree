-- ==========================================================
-- Expo Tree Exhibitions Production Database Dump
-- Target Hostinger Database: u424679052_expotree
-- Generated: 2026-10-07 16:12:33
-- ==========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

-- ----------------------------------------------------------
-- Table structure for `admin_users`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT 'Admin',
  `role` enum('admin','manager') DEFAULT 'admin',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `admin_users` (1 rows)
INSERT INTO `admin_users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `status`, `created_at`, `updated_at`) VALUES
('1', 'admin', 'admin@expotreeexhibitions.com', '$2y$10$BJmgVDd.S40RmQ5oofQE.O809jSg5hcccalNxI6ahn.T.ZwmWAAK6', 'Expo Tree Administrator', 'admin', 'active', '2026-10-07 09:22:08', '2026-10-07 09:22:08');

-- ----------------------------------------------------------
-- Table structure for `events`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'Lifestyle & Festive',
  `event_type` varchar(100) NOT NULL DEFAULT 'Exhibition',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_display` varchar(100) DEFAULT NULL,
  `venue` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL DEFAULT 'Delhi NCR',
  `pincode` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `location_type` varchar(50) DEFAULT 'Outdoors',
  `footfall` varchar(100) DEFAULT NULL,
  `gentry` varchar(100) DEFAULT 'Middle Class',
  `timings` varchar(100) DEFAULT '10:00 AM - 8:00 PM',
  `layout_type` varchar(100) DEFAULT 'First come first serve',
  `fan_charge` varchar(50) DEFAULT '300 RS',
  `price_shopping_canopy` decimal(10,2) DEFAULT NULL,
  `price_shopping_table1` decimal(10,2) DEFAULT NULL,
  `price_shopping_table2` decimal(10,2) DEFAULT NULL,
  `price_food_canopy` decimal(10,2) DEFAULT NULL,
  `price_food_table2` decimal(10,2) DEFAULT NULL,
  `price_promotional` varchar(100) DEFAULT '10k onwards',
  `daily_stall_price` decimal(10,2) NOT NULL DEFAULT 5000.00,
  `total_stalls` int(11) NOT NULL DEFAULT 20,
  `available_stalls` int(11) NOT NULL DEFAULT 20,
  `booked_stalls` int(11) NOT NULL DEFAULT 0,
  `stall_sizes` varchar(255) DEFAULT 'Canopy: 3x3m • Table: 6x2ft',
  `facilities` text DEFAULT NULL,
  `booking_instructions` text DEFAULT NULL,
  `terms_conditions` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `banner_url` varchar(500) DEFAULT NULL,
  `venue_images_json` text DEFAULT NULL,
  `highlights` text DEFAULT NULL,
  `map_link` text DEFAULT NULL,
  `organizer_name` varchar(150) DEFAULT 'Expo Tree Exhibitions',
  `organizer_company` varchar(150) DEFAULT 'Expo Tree Events Pvt Ltd',
  `organizer_phone` varchar(30) DEFAULT '9811175057',
  `organizer_whatsapp` varchar(30) DEFAULT '9811175057',
  `organizer_email` varchar(150) DEFAULT 'expotreeexhibitions@gmail.com',
  `organizer_website` varchar(255) DEFAULT NULL,
  `status` enum('draft','pending','approved','published','unpublished','rejected','active','inactive','archived') NOT NULL DEFAULT 'published',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `source` enum('admin','organizer_submission') NOT NULL DEFAULT 'admin',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_dates` (`start_date`,`end_date`),
  KEY `idx_city` (`city`),
  KEY `idx_type` (`event_type`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `events` (23 rows)
INSERT INTO `events` (`id`, `title`, `slug`, `category`, `event_type`, `start_date`, `end_date`, `date_display`, `venue`, `city`, `state`, `pincode`, `address`, `location_type`, `footfall`, `gentry`, `timings`, `layout_type`, `fan_charge`, `price_shopping_canopy`, `price_shopping_table1`, `price_shopping_table2`, `price_food_canopy`, `price_food_table2`, `price_promotional`, `daily_stall_price`, `total_stalls`, `available_stalls`, `booked_stalls`, `stall_sizes`, `facilities`, `booking_instructions`, `terms_conditions`, `description`, `image_url`, `banner_url`, `venue_images_json`, `highlights`, `map_link`, `organizer_name`, `organizer_company`, `organizer_phone`, `organizer_whatsapp`, `organizer_email`, `organizer_website`, `status`, `is_featured`, `source`, `rejection_reason`, `created_at`, `updated_at`) VALUES
('1', 'Lucerna Towers Corporate Lifestyle Carnival', 'lucerna-towers-corporate-lifestyle-carnival-noida', 'Lifestyle & Festive', 'Corporate', '2026-10-13', '2026-10-13', '13 Oct 2026', 'Lucerna Towers', 'Noida', 'Uttar Pradesh', NULL, NULL, 'Outdoors', '3,000+ Employees', 'Middle Class', '10:00 AM - 8:00 PM', 'First come first serve', '300 RS', '5000.00', '3500.00', '4000.00', '5000.00', '4500.00', '10k onwards', '5000.00', '20', '16', '4', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Exclusive corporate festive exhibition at Lucerna Towers Noida. Direct access to 3,000 corporate professionals looking for festive gifting, apparel, perfumes, jewellery & food stalls.', 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('2', 'Smartworks Noida Premium Tech Park Popup - Day 1', 'smartworks-noida-premium-tech-park-popup-day-1-noida', 'Lifestyle & Festive', 'Corporate', '2026-10-13', '2026-10-13', '13 Oct 2026', 'Smartworks Co-Working Hub', 'Noida', 'Uttar Pradesh', NULL, NULL, 'Indoors', '3,000+ Employees', 'Middle Class', '10:00 AM - 8:00 PM', 'First come first serve', 'Included', NULL, '6000.00', '7000.00', NULL, NULL, '10K onwards', '6000.00', '10', '8', '2', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Fully air-conditioned indoor corporate market inside Smartworks, Sector 125 Noida. High-purchasing power IT & management staff.', 'https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '0', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('3', 'Smartworks Noida Premium Tech Park Popup - Day 2', 'smartworks-noida-premium-tech-park-popup-day-2-noida', 'Lifestyle & Festive', 'Corporate', '2026-10-14', '2026-10-14', '14 Oct 2026', 'Smartworks Co-Working Hub', 'Noida', 'Uttar Pradesh', NULL, NULL, 'Indoors', '3,000+ Employees', 'Middle Class', '10:00 AM - 8:00 PM', 'First come first serve', 'Included', NULL, '6000.00', '7000.00', NULL, NULL, '10K onwards', '6000.00', '10', '9', '1', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Mid-week festive shopping extravaganza for tech & corporate employees at Smartworks Noida.', 'https://images.unsplash.com/photo-1528698827591-e19ccd7bc23d?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '0', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('4', 'Bestech Park View Spa Next Festive Society Mela', 'bestech-park-view-spa-next-festive-society-mela-gurugram', 'Lifestyle & Festive', 'Society', '2026-10-17', '2026-10-17', '17 Oct 2026', 'Bestech Park View Spa Next, Sector 67', 'Gurugram', 'Haryana', NULL, NULL, 'Outdoors', '570+ Elite Families', 'Middle Class', '4:00 PM - 10:00 PM', 'First come first serve', '300 RS', '4000.00', '3000.00', '3500.00', '6000.00', '5000.00', '10k onwards', '4000.00', '30', '24', '6', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Pre-Diwali evening festive bazaar in premium high-rise society. Ideal for women lehengas, imitation jewellery, footwear, kidswear, bakery & food stalls.', 'https://images.unsplash.com/photo-1577900232427-18219b9166a0?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('5', 'M3M Skyheights Luxury Pre-Diwali Trunk Show', 'm3m-skyheights-luxury-pre-diwali-trunk-show-gurugram', 'Lifestyle & Festive', 'Society', '2026-10-17', '2026-10-17', '17 Oct 2026', 'M3M Skyheights Clubhouse & Lawns', 'Gurugram', 'Haryana', NULL, NULL, 'Outdoors', '1,000+ Premium Families', 'Premium Class', '4:00 PM - 10:00 PM', 'First come first serve', '300 RS', '8000.00', '5000.00', '6000.00', '7000.00', '5000.00', '10k onwards', '8000.00', '20', '14', '6', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Ultra-luxury residential mela at M3M Golf Course Extension Road. High-spending residents eager for designer jewellery, dry fruits, home decor & couture.', 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('6', 'Ireo Victory Valley 3-Day Grand Festive Extravaganza', 'ireo-victory-valley-3-day-grand-festive-extravaganza-gurugram', 'Lifestyle & Festive', 'Society', '2026-10-18', '2026-10-20', '18-19-20 Oct 2026', 'Ireo Victory Valley Grand Central Lawns, Sector 67', 'Gurugram', 'Haryana', NULL, NULL, 'Outdoors', '800+ Affluent Families', 'Premium Class', '4:00 PM - 10:00 PM', 'First come first serve', '300 RS', '6000.00', '4000.00', '5000.00', '7000.00', '6000.00', '10k onwards', '6000.00', '25', '18', '7', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Grand 3-day weekend festive market in Gurugram’s tallest residential enclave. Massive crowd footfall across 3 days.', 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('7', 'Ireo City Central Luxury Mall Festive Flea', 'ireo-city-central-luxury-mall-festive-flea-gurugram', 'Lifestyle & Festive', 'Mall', '2026-10-19', '2026-10-20', '19-20 Oct 2026', 'Ireo City Central Mall Boulevard, Sector 59', 'Gurugram', 'Haryana', NULL, NULL, 'Outdoors', '1,000+ Daily Visitors', 'Premium Class', '10:00 AM - 10:00 PM', 'Layout blueprint provided', 'Included', '6000.00', NULL, NULL, '6000.00', NULL, '10K onwards', '6000.00', '10', '7', '3', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Prime high-street shopping boulevard inside Ireo City Central. Strategic assigned canopy layouts with full day footfall.', 'https://images.unsplash.com/photo-1534452203293-494d7ddbf7e0?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('8', 'Stellar 1425 Corporate Diwali Bazaar', 'stellar-1425-corporate-diwali-bazaar-noida', 'Lifestyle & Festive', 'Corporate', '2026-10-27', '2026-10-27', '27 Oct 2026', 'Stellar 1425 Atrium, Sector 142', 'Noida', 'Uttar Pradesh', NULL, NULL, 'Indoors', '5,000+ Corporate Employees', 'Middle Class', '10:00 AM - 8:00 PM', 'First come first serve', 'Included', NULL, '5000.00', '6000.00', NULL, NULL, '10K onwards', '5000.00', '10', '8', '2', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Prominent IT corridor tech park along Noida-Greater Noida Expressway. Pre-Diwali corporate gifting rush.', 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '0', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('9', 'Stellar IT Park Mega Tech Hub Exhibition', 'stellar-it-park-mega-tech-hub-exhibition-noida', 'Lifestyle & Festive', 'Corporate', '2026-10-27', '2026-10-28', '27-28 Oct 2026', 'Stellar IT Park Open Plaza, Sector 62', 'Noida', 'Uttar Pradesh', NULL, NULL, 'Outdoors', '12,000+ Employees', 'Middle Class', '10:00 AM - 8:00 PM', 'First come first serve', '300 RS', '6000.00', NULL, NULL, '5000.00', '4000.00', '20k onwards', '6000.00', '20', '15', '5', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Massive 2-day outdoor carnival with 12,000 corporate footfall. High demand for apparel, dry fruit hampers, organic cosmetics & live food.', 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('10', 'Stellar 1423 Tech Corridor Corporate Exhibition', 'stellar-1423-tech-corridor-corporate-exhibition-noida', 'Lifestyle & Festive', 'Corporate', '2026-10-28', '2026-10-28', '28 Oct 2026', 'Stellar 1423 Ground Concourse, Sector 142', 'Noida', 'Uttar Pradesh', NULL, NULL, 'Indoors', '5,000+ Corporate Employees', 'Middle Class', '10:00 AM - 8:00 PM', 'First come first serve', 'Included', NULL, '5000.00', '6000.00', NULL, NULL, '10K onwards', '5000.00', '10', '8', '2', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'High-end corporate building mela right before Diwali vacation. Ideal for scented candles, luxury chocolates, perfumes & handicrafts.', 'https://images.unsplash.com/photo-1526178613552-2b45c6c302f0?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '0', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('11', 'M3M Skyheights Dhanteras Festive Evening Bazaar', 'm3m-skyheights-dhanteras-festive-evening-bazaar-gurugram', 'Lifestyle & Festive', 'Society', '2026-10-30', '2026-10-30', '30 Oct 2026', 'M3M Skyheights Poolside Lawns, Sector 65', 'Gurugram', 'Haryana', NULL, NULL, 'Outdoors', '1,000+ Premium Families', 'Premium Class', '4:00 PM - 10:00 PM', 'First come first serve', '300 RS', '8000.00', '5000.00', '6000.00', '7000.00', '5000.00', '10k onwards', '8000.00', '20', '13', '7', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Dhanteras special luxury society market with peak shopping fervor for 92.5 silver jewellery, festive pooja decor, dry fruits & sweets.', 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('12', 'The Nile Gurugram Post-Diwali Weekend Mela', 'the-nile-gurugram-post-diwali-weekend-mela-gurugram', 'Lifestyle & Festive', 'Society', '2026-11-01', '2026-11-01', '01 Nov 2026', 'The Nile Society Central Amphitheatre, Sohna Road', 'Gurugram', 'Haryana', NULL, NULL, 'Outdoors', '450+ Active Families', 'Middle Class', '4:00 PM - 10:00 PM', 'First come first serve', 'Included', '6000.00', '4000.00', '5000.00', '7000.00', '5000.00', '10k onwards', '6000.00', '20', '16', '4', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Cozy family-centric post-festive gathering with high interest in winter wear, shawls, pottery, wooden toys & bakery items.', 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '0', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('13', 'Stellar 1425 Winter Lifestyle Kickoff', 'stellar-1425-winter-lifestyle-kickoff-noida', 'Lifestyle & Festive', 'Corporate', '2026-11-03', '2026-11-03', '03 Nov 2026', 'Stellar 1425 Concourse, Sector 142', 'Noida', 'Uttar Pradesh', NULL, NULL, 'Indoors', '5,000+ Corporate Employees', 'Middle Class', '10:00 AM - 8:00 PM', 'First come first serve', 'Included', NULL, '5000.00', '6000.00', NULL, NULL, '10K onwards', '5000.00', '10', '8', '2', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Winter collection debut popup for corporate employees. Handbags, perfumes, winter apparel & stationery.', 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '0', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('14', 'Lucerna Towers 2-Day Winter Carnival', 'lucerna-towers-2-day-winter-carnival-noida', 'Lifestyle & Festive', 'Corporate', '2026-11-03', '2026-11-04', '3-4 Nov 2026', 'Lucerna Towers Plaza, Sector 125', 'Noida', 'Uttar Pradesh', NULL, NULL, 'Outdoors', '15,000+ Corporate Employees', 'Middle Class', '10:00 AM - 8:00 PM', 'First come first serve', 'Included', '5000.00', NULL, NULL, '5000.00', '4500.00', '10k onwards', '5000.00', '20', '14', '6', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Massive 2-day outdoor tech carnival with over 15,000 footfall across leading multinational offices.', 'https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('15', 'Urbtech Trade Centre Corporate Lifestyle Mela', 'urbtech-trade-centre-corporate-lifestyle-mela-noida', 'Lifestyle & Festive', 'Corporate', '2026-11-04', '2026-11-04', '04 Nov 2026', 'Urbtech Trade Centre Courtyard, Sector 132', 'Noida', 'Uttar Pradesh', NULL, NULL, 'Outdoors', '3,000+ Corporate Staff', 'Middle Class', '10:00 AM - 8:00 PM', 'First come first serve', 'Included', '5000.00', NULL, NULL, '5000.00', '4500.00', '10k onwards', '5000.00', '20', '17', '3', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Corporate lifestyle exhibition at Sector 132 expressway corporate park.', 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '0', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('16', 'Central Park Flower Valley Grand Winter Carnival', 'central-park-flower-valley-grand-winter-carnival-gurugram', 'Lifestyle & Festive', 'Society', '2026-11-04', '2026-11-04', '04 Nov 2026', 'Central Park Flower Valley Clubhouse & Grand Lawns', 'Gurugram', 'Haryana', NULL, NULL, 'Outdoors', '2,000+ Luxury Families', 'Premium Class', '4:00 PM - 10:00 PM', 'First come first serve', 'Included', '10000.00', NULL, NULL, NULL, NULL, '10k onwards', '10000.00', '30', '22', '8', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'One of Gurugram’s largest ultra-luxury township exhibitions with high Net-Worth residents.', 'https://images.unsplash.com/photo-1519567241046-7f570eee3ce6?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('17', 'South Delhi Autumn Fashion & Luxury Popup', 'south-delhi-autumn-fashion-luxury-popup-delhi', 'Lifestyle & Festive', 'Mall', '2026-11-07', '2026-11-08', '07-08 Nov 2026', 'The Grand Pavilion, Vasant Kunj / Saket Hub', 'Delhi', 'Delhi NCR', NULL, NULL, 'Indoors', '18,000+ Affluent Shoppers', 'Premium Class', '11:30 AM - 9:30 PM', 'Layout blueprint provided', 'Included', '8500.00', '5500.00', '7000.00', '8000.00', '6000.00', '15k onwards', '8500.00', '45', '35', '10', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'South Delhi haute couture trunk show. Designer silk lehengas, Polki & diamond jewellery, home decor and tarot readings in air-conditioned grand pavilion.', 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('18', 'Royal Winter & Wedding Trunk Show Faridabad', 'royal-winter-wedding-trunk-show-faridabad-faridabad', 'Lifestyle & Festive', 'Exhibition', '2026-11-21', '2026-11-22', '21-22 Nov 2026', 'Radisson Blu Grand Ballroom & Lawns', 'Faridabad', 'Haryana', NULL, NULL, 'Indoors', '15,000+ Wedding Shoppers', 'Premium Class', '11:00 AM - 9:00 PM', 'Layout blueprint provided', 'Included', '7500.00', '4500.00', '6000.00', '7000.00', '5000.00', '12k onwards', '7500.00', '35', '28', '7', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Wedding season trunk show in 5-star Radisson Blu ballroom. Bridal trousseau, footwear, luxury gifting & skincare.', 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('19', 'Delhi NCR Artisan & Handicrafts Carnival Ghaziabad', 'delhi-ncr-artisan-handicrafts-carnival-ghaziabad-ghaziabad', 'Lifestyle & Festive', 'Mall', '2026-12-05', '2026-12-06', '05-06 Dec 2026', 'Mahagun Metro Mall Atrium, Vaishali', 'Ghaziabad', 'Uttar Pradesh', NULL, NULL, 'Indoors', '20,000+ Weekend Shoppers', 'Middle Class', '11:00 AM - 10:00 PM', 'First come first serve', 'Included', '6000.00', '4000.00', '5000.00', '6500.00', '4500.00', '10k onwards', '6000.00', '30', '25', '5', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Handmade pottery, crochet, organic soaps, home furnishings and live craft workshops inside Vaishali premier mall.', 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('20', 'Grand Christmas & New Year Lifestyle Expo Greater Noida', 'grand-christmas-new-year-lifestyle-expo-greater-noida-greater-noida', 'Lifestyle & Festive', 'Exhibition', '2026-12-19', '2026-12-20', '19-20 Dec 2026', 'India Expo Centre & Mart Hub', 'Greater Noida', 'Uttar Pradesh', NULL, NULL, 'Indoors', '35,000+ Mega Crowd', 'Premium Class', '11:00 AM - 10:00 PM', 'Layout blueprint provided', 'Included', '9000.00', '6000.00', '7500.00', '8500.00', '6500.00', '20k onwards', '9000.00', '60', '45', '15', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Year-end mega extravaganza at India Expo Mart with giant kids play zone, celebrity guests, all 16 categories and huge footfall.', 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 09:22:08', '2026-10-07 14:03:43'),
('23', 'Grand DLF Phase 5 Diwali Mela 2026', 'grand-dlf-phase-5-diwali-mela-2026-dlf-club-5-phase-5-gurugram', 'Lifestyle & Festive', 'Premium Society', '2026-10-24', '2026-10-25', '24–25 Oct 2026', 'DLF Club 5, Phase 5', 'Gurugram', 'Delhi NCR', '122009', 'Club5 Drive, DLF Phase 5', 'Indoors', '2500+ resident families', 'HNIs & Executives', '11:00 AM - 9:00 PM', 'First come first serve', '300 RS', '6500.00', '4500.00', NULL, NULL, NULL, '10k onwards', '5500.00', '30', '30', '0', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Annual festive extravaganza featuring high-end apparel, fine jewellery, artisanal sweets and kids games.', 'https://images.unsplash.com/photo-1577900232427-18219b9166a0?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Vikram Chadha', 'DLF Phase 5 RWA Events', '9811199887', '9811199887', 'events@dlfphase5.org', NULL, 'published', '0', 'organizer_submission', NULL, '2026-10-07 10:55:23', '2026-10-07 14:03:43'),
('25', 'Ireo city central Mall Lifestyle Exhibition', 'ireo-city-central-mall-lifestyle-exhibition-2026-10-27', 'Lifestyle & Festive', 'Mall', '2026-10-27', '2026-10-28', '27-28 Oct 2026', 'Ireo city central', 'Gurugram', 'Delhi NCR', NULL, NULL, 'outdoors', '1000+', 'Premium Class', '10-10 PM', 'We have layout here', '-', '6000.00', NULL, NULL, '6000.00', NULL, '10K onwards', '6000.00', '10', '10', '0', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Curated Mall exhibition hosted at Ireo city central, Gurugram. Direct exposure to 1000+ affluent attendees with dedicated setups for Jewellery, Apparel, Home Decor, Bakery & Food counters.', 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 12:06:51', '2026-10-07 14:03:43'),
('26', 'Stellar 1423 Corporate Lifestyle Exhibition', 'stellar-1423-corporate-lifestyle-exhibition-2026-11-04', 'Lifestyle & Festive', 'Corporate', '2026-11-04', '2026-11-04', '2026-11-04', 'Stellar 1423', 'Noida', 'Delhi NCR', NULL, NULL, 'Indoors', '5k employees', 'Middle Class', '10-8 PM', 'First come first serve', '-', NULL, '5000.00', '6000.00', NULL, NULL, '10K onwards', '5000.00', '10', '10', '0', 'Canopy: 3x3m • Table: 6x2ft', NULL, NULL, NULL, 'Curated Corporate exhibition hosted at Stellar 1423, Noida. Direct exposure to 5k employees affluent attendees with dedicated setups for Jewellery, Apparel, Home Decor, Bakery & Food counters.', 'https://images.unsplash.com/photo-1526178613552-2b45c6c302f0?auto=format&fit=crop&w=1200&q=80', NULL, NULL, NULL, NULL, 'Expo Tree Exhibitions', 'Expo Tree Events Pvt Ltd', '9811175057', '9811175057', 'expotreeexhibitions@gmail.com', NULL, 'published', '1', 'admin', NULL, '2026-10-07 12:06:51', '2026-10-07 14:03:43');

-- ----------------------------------------------------------
-- Table structure for `event_stalls`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `event_stalls`;
CREATE TABLE `event_stalls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `stall_type` varchar(100) NOT NULL,
  `stall_name` varchar(150) NOT NULL,
  `stall_size` varchar(100) DEFAULT '3x3m',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_quantity` int(11) NOT NULL DEFAULT 10,
  `available_quantity` int(11) NOT NULL DEFAULT 10,
  `booked_quantity` int(11) NOT NULL DEFAULT 0,
  `status` enum('available','reserved','booked','unavailable') NOT NULL DEFAULT 'available',
  `facilities` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`),
  KEY `idx_stall_status` (`status`),
  CONSTRAINT `fk_event_stalls_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `event_stalls` (78 rows)
INSERT INTO `event_stalls` (`id`, `event_id`, `stall_type`, `stall_name`, `stall_size`, `price`, `total_quantity`, `available_quantity`, `booked_quantity`, `status`, `facilities`, `created_at`, `updated_at`) VALUES
('1', '1', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '5000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('2', '1', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '3500.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('3', '1', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '4000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('4', '1', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '5000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('5', '1', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '4500.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('6', '2', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '6000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('7', '2', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '7000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('8', '3', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '6000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('9', '3', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '7000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('10', '4', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '4000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('11', '4', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '3000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('12', '4', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '3500.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('13', '4', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '6000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('14', '4', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '5000.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('15', '5', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '8000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('16', '5', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '5000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('17', '5', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '6000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('18', '5', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '7000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('19', '5', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '5000.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('20', '6', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '6000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('21', '6', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '4000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('22', '6', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '5000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('23', '6', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '7000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('24', '6', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '6000.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('25', '7', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '6000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('26', '7', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '6000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('27', '8', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '5000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('28', '8', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '6000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('29', '9', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '6000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('30', '9', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '5000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('31', '9', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '4000.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('32', '25', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '6000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('33', '25', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '6000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('34', '10', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '5000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('35', '10', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '6000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('36', '11', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '8000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('37', '11', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '5000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('38', '11', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '6000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('39', '11', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '7000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('40', '11', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '5000.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('41', '12', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '6000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('42', '12', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '4000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('43', '12', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '5000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('44', '12', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '7000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('45', '12', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '5000.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('46', '13', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '5000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('47', '13', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '6000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('48', '26', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '5000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('49', '26', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '6000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('50', '15', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '5000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51');

INSERT INTO `event_stalls` (`id`, `event_id`, `stall_type`, `stall_name`, `stall_size`, `price`, `total_quantity`, `available_quantity`, `booked_quantity`, `status`, `facilities`, `created_at`, `updated_at`) VALUES
('51', '15', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '5000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('52', '15', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '4500.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('53', '16', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '10000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('54', '14', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '5000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('55', '14', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '5000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('56', '14', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '4500.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('57', '17', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '8500.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('58', '17', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '5500.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('59', '17', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '7000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('60', '17', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '8000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('61', '17', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '6000.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('62', '18', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '7500.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('63', '18', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '4500.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('64', '18', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '6000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('65', '18', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '7000.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('66', '18', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '5000.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('67', '19', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '6000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('68', '19', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '4000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('69', '19', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '5000.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('70', '19', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '6500.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('71', '19', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '4500.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('72', '20', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '9000.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('73', '20', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '6000.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('74', '20', '2 Open Tables', '2 Open Tables Space (12x2ft)', '12x2ft Twin Table Setup', '7500.00', '4', '4', '0', 'available', '2 Draped Tables, 2 Chairs, Spotlights', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('75', '20', 'Food Canopy', 'Gourmet Food Canopy (3x3m)', '3x3m Food Zone Canopy', '8500.00', '4', '4', '0', 'available', '2 Heavy Duty Tables, 2 Chairs, High-Wattage Power Connection, Waste Disposal', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('76', '20', 'Food 2 Open Tables', 'Bakery / Dryfruits Table Setup', '12x2ft Twin Table Space', '6500.00', '4', '4', '0', 'available', '2 Tables, 2 Chairs, Tasting Counter Space', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('77', '23', 'Shopping Canopy', 'Shopping Canopy Stall (3x3m)', '3x3m Covered Canopy', '6500.00', '8', '8', '0', 'available', '2 Draped Tables, 2 Cushioned Chairs, LED Spotlight, 15A Power Backup', '2026-10-07 12:06:51', '2026-10-07 12:06:51'),
('78', '23', '1 Open Table', '1 Open Table Space (6x2ft)', '6x2ft Boutique Table', '4500.00', '6', '6', '0', 'available', '1 Draped Table, 1 Cushioned Chair, Light Point', '2026-10-07 12:06:51', '2026-10-07 12:06:51');

-- ----------------------------------------------------------
-- Table structure for `bookings`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_number` varchar(50) NOT NULL,
  `event_id` int(11) NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `business_name` varchar(150) DEFAULT NULL,
  `mobile` varchar(30) NOT NULL,
  `whatsapp` varchar(30) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `category_name` varchar(100) DEFAULT 'Jewellery & Accessories',
  `stall_type` varchar(150) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days_count` int(11) NOT NULL DEFAULT 1,
  `stalls_count` int(11) NOT NULL DEFAULT 1,
  `price_per_day` decimal(10,2) NOT NULL DEFAULT 0.00,
  `addons_json` text DEFAULT NULL,
  `addons_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'confirmed',
  `whatsapp_sent` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_number` (`booking_number`),
  KEY `fk_bookings_event` (`event_id`),
  CONSTRAINT `fk_bookings_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `bookings` (3 rows)
INSERT INTO `bookings` (`id`, `booking_number`, `event_id`, `customer_name`, `business_name`, `mobile`, `whatsapp`, `email`, `address`, `category_name`, `stall_type`, `start_date`, `end_date`, `days_count`, `stalls_count`, `price_per_day`, `addons_json`, `addons_total`, `total_amount`, `notes`, `status`, `whatsapp_sent`, `created_at`, `updated_at`) VALUES
('1', 'EXPO-BK-202610-001', '1', 'Ritu Verma', 'Aroma Glow Soy Candles', '9876543210', '9876543210', 'rituv@aromaglow.com', 'Sector 45, Gurugram', 'Candles & Soaps', 'Shopping Canopy (2 Tables + 2 Chairs + light)', '2026-10-13', '2026-10-13', '1', '1', '5000.00', NULL, '300.00', '5300.00', 'Need corner stall near main entrance', 'confirmed', '1', '2026-10-07 09:22:08', '2026-10-07 09:22:08'),
('2', 'EXPO-BK-202610-62678', '1', 'Aarti Sharma', 'Aarti Boutique', '9811122334', '9811122334', 'aarti@example.com', 'Noida Sec 15', 'Jewellery & Accessories', 'Shopping Canopy', '2026-10-13', '2026-10-13', '1', '1', '5000.00', '[\"Industrial Fan (\\u20b9300\\/day x 1 days x 1 stalls) = \\u20b9300\"]', '300.00', '5300.00', 'Near entrance', 'confirmed', '1', '2026-10-07 10:54:00', '2026-10-07 10:54:00'),
('3', 'EXPO-BK-202610-15760', '1', 'Aarti Sharma', 'Aarti Boutique', '9811122334', '9811122334', 'aarti@example.com', 'Noida Sec 15', 'Jewellery & Accessories', 'Shopping Canopy', '2026-10-13', '2026-10-13', '1', '1', '5000.00', '[\"Industrial Fan (\\u20b9300\\/day x 1 days x 1 stalls) = \\u20b9300\"]', '300.00', '5300.00', 'Near entrance', 'confirmed', '1', '2026-10-07 10:54:43', '2026-10-07 10:54:43');

-- ----------------------------------------------------------
-- Table structure for `shopper_passes`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `shopper_passes`;
CREATE TABLE `shopper_passes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pass_code` varchar(50) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `city` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `pass_code` (`pass_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `shopper_passes` (1 rows)
INSERT INTO `shopper_passes` (`id`, `pass_code`, `event_id`, `name`, `phone`, `city`, `created_at`) VALUES
('1', 'EXPO-VIP-1689', '1', 'Kavita Singhal', '9876543210', 'Gurugram', '2026-10-07 10:50:23');

-- ----------------------------------------------------------
-- Table structure for `site_settings`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `idx_setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `site_settings` (12 rows)
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
('1', 'site_name', 'Expo Tree Exhibitions', '2026-10-07 12:03:21', '2026-10-07 12:03:21'),
('2', 'site_title', 'Expo Tree Exhibitions | Delhi NCR Premier Lifestyle & Festive Exhibitions', '2026-10-07 12:03:21', '2026-10-07 12:03:21'),
('3', 'admin_phone', '9811175057', '2026-10-07 12:03:21', '2026-10-07 12:03:21'),
('4', 'admin_whatsapp', '9811175057', '2026-10-07 12:03:21', '2026-10-07 12:03:21'),
('5', 'admin_email', 'expotreeexhibitions@gmail.com', '2026-10-07 12:03:21', '2026-10-07 12:03:21'),
('6', 'instagram_handle', 'expo_tree_exhibitions', '2026-10-07 12:03:21', '2026-10-07 12:03:21'),
('7', 'instagram_followers', '88.9K+', '2026-10-07 12:03:21', '2026-10-07 12:03:21'),
('8', 'office_address', 'DLF CyberHub & Ambience Mall Corridor, Gurugram / Sector 18 Noida, Delhi NCR', '2026-10-07 12:03:21', '2026-10-07 12:03:21'),
('9', 'hero_title', 'Festive Melas & Lifestyle Exhibitions', '2026-10-07 12:03:21', '2026-10-07 12:03:21'),
('10', 'hero_subtitle', 'Shop Handcrafted Wonders • Book Prime Footfall Stalls', '2026-10-07 12:03:21', '2026-10-07 12:03:21'),
('11', 'hero_description', 'Curating high-energy festive exhibitions across Gurugram, Noida, and Delhi. Connecting 100+ boutique designers, handcrafted artisans, and home-grown brands with thousands of eager shoppers.', '2026-10-07 12:03:21', '2026-10-07 12:03:21'),
('12', 'meta_description', 'Shop, Explore, Support, Indulge. Delhi NCR premier lifestyle & festive exhibitions organizer across Gurugram, Noida, Delhi. Book stalls or claim free shopper pass. Hotline: 9811175057', '2026-10-07 12:03:21', '2026-10-07 12:03:21');

SET FOREIGN_KEY_CHECKS = 1;
-- End of dump
