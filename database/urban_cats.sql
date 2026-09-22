-- Urban Cats Database — Premium cat-selling (Paystack) — XAMPP
CREATE DATABASE IF NOT EXISTS `urban_cats` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `urban_cats`;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

-- admins
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(190) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- customers
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(190) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(40) DEFAULT NULL,
  `whatsapp` VARCHAR(40) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `state` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- breeds
DROP TABLE IF EXISTS `breeds`;
CREATE TABLE `breeds` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(80) NOT NULL UNIQUE,
  `name` VARCHAR(80) NOT NULL,
  `short_description` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `temperament` VARCHAR(255) NOT NULL,
  `grooming` VARCHAR(255) NOT NULL,
  `activity_level` VARCHAR(100) NOT NULL,
  `size_info` VARCHAR(150) NOT NULL,
  `family_compatibility` VARCHAR(255) NOT NULL,
  `care_notes` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- cats
DROP TABLE IF EXISTS `cats`;
CREATE TABLE `cats` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `breed_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(80) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `sex` ENUM('male','female') NOT NULL,
  `date_of_birth` DATE NOT NULL,
  `colour` VARCHAR(80) NOT NULL,
  `location` VARCHAR(120) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL COMMENT 'Price in NGN',
  `deposit_amount` DECIMAL(10,2) DEFAULT NULL,
  `purchase_mode` ENUM('full','deposit','enquiry') NOT NULL DEFAULT 'full',
  `availability` ENUM('available','reserved','sold','coming_soon') NOT NULL DEFAULT 'available',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `personality` VARCHAR(255) NOT NULL,
  `vaccination_status` VARCHAR(255) NOT NULL,
  `health_notes` VARCHAR(255) NOT NULL,
  `litter_trained` TINYINT(1) NOT NULL DEFAULT 1,
  `feeding_info` VARCHAR(255) DEFAULT NULL,
  `parent_info` VARCHAR(255) DEFAULT NULL,
  `description` TEXT NOT NULL,
  `care_notes` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`breed_id`) REFERENCES `breeds`(`id`) ON DELETE CASCADE,
  INDEX (`availability`), INDEX (`breed_id`), INDEX (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- cat_images
DROP TABLE IF EXISTS `cat_images`;
CREATE TABLE `cat_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cat_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`cat_id`) REFERENCES `cats`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- favourites
DROP TABLE IF EXISTS `favourites`;
CREATE TABLE `favourites` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT UNSIGNED NOT NULL,
  `cat_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_fav` (`customer_id`,`cat_id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cat_id`) REFERENCES `cats`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- carts
DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT UNSIGNED DEFAULT NULL,
  `session_id` VARCHAR(128) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_customer` (`customer_id`),
  INDEX (`session_id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- cart_items
DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cart_id` INT UNSIGNED NOT NULL,
  `cat_id` INT UNSIGNED NOT NULL,
  `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_cart_cat` (`cart_id`,`cat_id`),
  FOREIGN KEY (`cart_id`) REFERENCES `carts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cat_id`) REFERENCES `cats`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- orders
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_reference` VARCHAR(32) NOT NULL UNIQUE,
  `customer_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `amount_kobo` INT UNSIGNED NOT NULL,
  `currency` VARCHAR(3) NOT NULL DEFAULT 'NGN',
  `status` ENUM('pending','paid','failed','cancelled','abandoned','refunded') NOT NULL DEFAULT 'pending',
  `paystack_reference` VARCHAR(100) DEFAULT NULL UNIQUE,
  `paystack_access_code` VARCHAR(100) DEFAULT NULL,
  `delivery_option` ENUM('collection','delivery') NOT NULL DEFAULT 'delivery',
  `customer_message` TEXT DEFAULT NULL,
  `consent` TINYINT(1) NOT NULL DEFAULT 0,
  `paid_at` TIMESTAMP NULL DEFAULT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
  INDEX (`status`), INDEX (`paystack_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- order_items
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `cat_id` INT UNSIGNED NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `payment_type` ENUM('full','deposit') NOT NULL DEFAULT 'full',
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cat_id`) REFERENCES `cats`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- payments
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `paystack_reference` VARCHAR(100) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `amount_kobo` INT UNSIGNED NOT NULL,
  `currency` VARCHAR(3) NOT NULL DEFAULT 'NGN',
  `status` VARCHAR(40) NOT NULL,
  `gateway_response` VARCHAR(255) DEFAULT NULL,
  `paid_at` TIMESTAMP NULL DEFAULT NULL,
  `raw_response` JSON DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  INDEX (`paystack_reference`), INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- enquiries
DROP TABLE IF EXISTS `enquiries`;
CREATE TABLE `enquiries` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `type` ENUM('general','cat_enquiry','reservation') NOT NULL DEFAULT 'general',
  `cat_id` INT UNSIGNED DEFAULT NULL,
  `customer_id` INT UNSIGNED DEFAULT NULL,
  `full_name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `phone` VARCHAR(40) NOT NULL,
  `whatsapp` VARCHAR(40) DEFAULT NULL,
  `location` VARCHAR(120) DEFAULT NULL,
  `preferred_contact` ENUM('email','phone','whatsapp') DEFAULT 'whatsapp',
  `home_info` TEXT DEFAULT NULL,
  `message` TEXT NOT NULL,
  `consent` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`cat_id`) REFERENCES `cats`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
  INDEX (`type`), INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- newsletter
DROP TABLE IF EXISTS `newsletter_subscribers`;
CREATE TABLE `newsletter_subscribers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(190) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `unsubscribed_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- faqs
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `question` VARCHAR(255) NOT NULL,
  `answer` TEXT NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- testimonials
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `author_name` VARCHAR(120) NOT NULL,
  `location` VARCHAR(120) DEFAULT NULL,
  `content` TEXT NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `is_demo` TINYINT(1) NOT NULL DEFAULT 1,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- care_articles
DROP TABLE IF EXISTS `care_articles`;
CREATE TABLE `care_articles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `title` VARCHAR(190) NOT NULL,
  `excerpt` VARCHAR(255) DEFAULT NULL,
  `content` TEXT NOT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- site_settings
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `setting_key` VARCHAR(100) PRIMARY KEY,
  `setting_value` TEXT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- login_attempts
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(190) DEFAULT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `success` TINYINT(1) NOT NULL DEFAULT 0,
  INDEX (`email`), INDEX (`ip_address`), INDEX (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS=1;

-- SEED breeds
INSERT INTO `breeds` (`slug`,`name`,`short_description`,`description`,`temperament`,`grooming`,`activity_level`,`size_info`,`family_compatibility`,`care_notes`,`image`) VALUES
('ragdoll','Ragdoll','Gentle, affectionate and famously relaxed.','Ragdolls are large, semi-longhaired cats known for their striking blue eyes and colour-point coats. Calm, people-oriented, ideal for indoor homes.','Calm, affectionate, sociable — enjoys being held','Semi-long coat; brush 2–3 times per week','Low to moderate','Large — 4 to 9 kg','Well suited to families','Daily interaction and grooming, indoor only. Vet advice essential.','assets/images/breeds/ragdoll.svg'),
('maine-coon','Maine Coon','The friendly giant — sociable and hardy.','Maine Coons are large, tufted ears, bushy tails, friendly and curious, kitten-like playfulness.','Friendly, curious, gentle giant','Long coat; brush 2–3 times weekly','Moderate to high','Very large — 4 to 10 kg','Good with children/pets','Sturdy posts, climbing spaces, grooming.','assets/images/breeds/maine-coon.svg'),
('british-shorthair','British Shorthair','Easygoing, dignified and plush-coated.','Compact, muscular, dense plush coats, round faces, steady affectionate.','Easygoing, loyal, independent yet affectionate','Dense short coat; weekly brushing','Low to moderate','Medium to large — 4 to 8 kg','Adaptable to families','Portion-controlled feeding, regular checks.','assets/images/breeds/british-shorthair.svg'),
('persian','Persian','Quiet, sweet-tempered and elegant.','Medium to large, long flowing coats, calm affectionate, serene indoor.','Calm, sweet, quiet — prefers routine','Long coat; daily brushing','Low','Medium to large — 3 to 6 kg','Suitable for calm households','Daily grooming essential.','assets/images/breeds/persian.svg');

-- SEED cats (demo, clearly marked)
INSERT INTO `cats` (`breed_id`,`name`,`slug`,`sex`,`date_of_birth`,`colour`,`location`,`price`,`deposit_amount`,`purchase_mode`,`availability`,`is_featured`,`is_active`,`personality`,`vaccination_status`,`health_notes`,`litter_trained`,`feeding_info`,`parent_info`,`description`,`care_notes`) VALUES
(1,'Milo','milo-ragdoll-male','male','2024-08-12','Seal point with mittens','Gwarinpa, Abuja',450000,135000,'full','available',1,1,'Gentle, cuddly, loves laps','Up to date — dewormed. Records at handover.','Vet-checked Jan 2025. No chronic issues.',1,'Premium kitten kibble','Dam: Luna — Sire: Oliver','Milo is a soft-natured Ragdoll boy, calm and affectionate, raised indoors with careful handling.','Brush 2–3 times weekly, indoor only, quiet spaces.'),
(1,'Bella','bella-ragdoll-female','female','2024-09-03','Blue point','Gwarinpa, Abuja',480000,144000,'deposit','available',1,1,'Sweet, quiet, people-oriented','Vaccinated, dewormed','Vet-checked Feb 2025.',1,'Kitten wet + dry','Dam: Coco — Sire: Milo Sr','Bella is serene with striking blue eyes, enjoys calm companionship and gentle toys.','Regular grooming, calm indoor.'),
(2,'Aslan','aslan-maine-coon-male','male','2024-05-20','Golden classic tabby','Gwarinpa, Abuja',650000,195000,'full','available',1,1,'Confident, playful, chirpy','Vaccinations current','Vet-checked Mar 2025.',1,'High-protein kitten food','Dam: Freya — Sire: Thor','Magnificent Maine Coon with plumed tail and friendly chirp.','Needs vertical space, frequent brushing.'),
(3,'Oliver','oliver-british-shorthair-male','male','2024-07-22','British Blue','Gwarinpa, Abuja',500000,150000,'deposit','available',1,1,'Steady, loyal, plush','Vaccinated','Vet-checked Feb 2025.',1,'Measured dry food','Dam: Misty — Sire: Winston','Classic British Blue, calm confident.','Weekly brushing, measured feeding.'),
(4,'Casper','casper-persian-male','male','2024-06-10','Cream','Gwarinpa, Abuja',520000,NULL,'enquiry','available',0,1,'Calm, dignified','Vaccinated, dewormed','Vet-checked Jan 2025.',1,'Persian kitten formula','Dam: Pearl — Sire: Cloud','Tranquil Persian prefers peaceful home.','Daily brushing, cool calm environment.'),
(4,'Luna','luna-persian-female','female','2024-10-01','White','Gwarinpa, Abuja',550000,165000,'coming_soon','coming_soon',0,1,'Sweet, quiet','First vaccinations','Vet-checked Mar 2025.',1,'Kitten milk + kibble','Dam: Snow — Sire: Casper Sr','Delicate Persian kitten, sweet nature.','Daily grooming, quiet home.');

INSERT INTO `cat_images` (`cat_id`,`image_path`,`is_primary`,`sort_order`) VALUES
(1,'assets/images/cats/milo-1.svg',1,0),(1,'assets/images/cats/milo-2.svg',0,1),
(2,'assets/images/cats/bella-1.svg',1,0),
(3,'assets/images/cats/aslan-1.svg',1,0),
(4,'assets/images/cats/oliver-1.svg',1,0),
(5,'assets/images/cats/casper-1.svg',1,0),
(6,'assets/images/cats/luna-1.svg',1,0);

-- faqs
INSERT INTO `faqs` (`question`,`answer`,`sort_order`) VALUES
('How does buying work?','Browse cats, review profile, submit details, pay securely via Paystack, receive confirmation, then finalise collection/delivery with Urban Cats.',1),
('Can I pay deposit?','Yes — cats marked Reserve allow a deposit. Full-payment cats require full amount. Enquiry-only cats require contacting us.',2),
('Are cats health-checked?','Each cat is vet-checked and records shared. We do not guarantee lifelong health — ongoing vet care is yours.',3),
('Delivery from Abuja?','Collection in Gwarinpa or coordinated delivery — confirmed after payment per location/welfare.',4),
('What if cat needs settling?','Normal to need days/weeks. We provide feeding/litter guidance and follow-up.',5);

-- testimonials demo
INSERT INTO `testimonials` (`author_name`,`location`,`content`,`rating`,`is_demo`) VALUES
('A. Okonkwo','Abuja','Calm and thorough — our British Shorthair settled beautifully. (Demo)',5,1),
('F. Adeyemi','Abuja','Transparent care, great support. (Demo)',5,1),
('K. Mensah','Lagos','Warm, professional, responsible. (Demo)',5,1);

-- care articles demo
INSERT INTO `care_articles` (`slug`,`title`,`excerpt`,`content`) VALUES
('kitten-care-basics','Kitten Care Basics','Feeding, litter, and settling your new kitten.','Provide fresh water, gradual food transition, quiet base room, scooped litter, and vet check within a week. (Demo)');

-- site_settings
INSERT INTO `site_settings` (`setting_key`,`setting_value`) VALUES
('site_name','Urban Cats'),
('tagline','Exceptional companions, thoughtfully placed'),
('contact_email','urbankitty0@gmail.com'),
('contact_phone','09122037945'),
('whatsapp_number','2349122037945'),
('whatsapp_display','09122037945'),
('address','Gwarinpa, Abuja, Nigeria'),
('opening_hours','Mon–Sat 9:00–18:00 WAT'),
('instagram_url','https://instagram.com'),
('facebook_url','https://facebook.com'),
('tiktok_url','https://tiktok.com'),
('base_url','http://localhost/urbancat'),
('announcement_text','New arrivals this season — enquire early for a calm, responsible placement.'),
('paystack_public_key','pk_test_placeholder'),
('paystack_secret_key','sk_test_placeholder'),
('paystack_webhook_secret','whsec_placeholder'),
('cart_expiry_hours','48'),
('delivery_note','Delivery confirmed after payment per location/welfare. Fees quoted separately.');

-- demo admin via setup script (no plaintext here)
