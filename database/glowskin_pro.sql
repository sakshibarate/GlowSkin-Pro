CREATE DATABASE IF NOT EXISTS glowskin_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE glowskin_pro;

CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('user','admin') NOT NULL DEFAULT 'user',
 status ENUM('active','blocked') NOT NULL DEFAULT 'active',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS analyses (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 skin_type ENUM('Dry','Oily','Combination','Balanced') NOT NULL,
 main_concern VARCHAR(100) NOT NULL,
 sensitivity VARCHAR(120) NOT NULL,
 answers_json JSON NOT NULL,
 summary TEXT NOT NULL,
 image_path VARCHAR(255) NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_analyses_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_analyses_user_date(user_id,created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 category ENUM('Cleanser','Moisturiser','Sunscreen','Serum','Treatment') NOT NULL,
 skin_type ENUM('All','Dry','Oily','Combination','Balanced') NOT NULL DEFAULT 'All',
 description TEXT NOT NULL,
 usage_note TEXT NOT NULL,
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NULL,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(190) NOT NULL,
 subject VARCHAR(150) NOT NULL,
 message TEXT NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_contact_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO products(name,category,skin_type,description,usage_note) VALUES
('Gentle Hydrating Cleanser','Cleanser','Dry','A sample cleanser category focused on a gentle cleanse without leaving skin feeling stripped.','Use as directed on the product label; avoid hot water and aggressive scrubbing.'),
('Lightweight Gel Cleanser','Cleanser','Oily','A sample cleanser option for people who prefer a light, rinse-off texture.','Cleanse gently; washing repeatedly may worsen irritation.'),
('Balanced Daily Cleanser','Cleanser','Combination','A gentle cleanser example for a routine with different skin-feel areas.','Adjust frequency based on comfort and the product label.'),
('Barrier-support Moisturiser','Moisturiser','Dry','A moisturiser example designed around comfort and supporting the skin barrier.','Apply to clean skin; check the ingredient list for known sensitivities.'),
('Lightweight Moisturiser','Moisturiser','Oily','A lightweight moisturiser example for users who prefer less-rich textures.','Oily skin can still benefit from moisturising; choose a comfortable texture.'),
('Everyday Moisturiser','Moisturiser','All','A basic moisturiser example for routine consistency.','Use according to the label and your personal tolerance.'),
('Broad-spectrum Sunscreen SPF 30+','Sunscreen','All','A reminder category for broad-spectrum sun protection.','Follow label directions and reapply as indicated, especially outdoors.'),
('Simple Hydrating Serum','Serum','Dry','A sample hydrating serum category; ingredient suitability varies.','Introduce one new product at a time and patch test where appropriate.'),
('Niacinamide-style Serum Example','Serum','Oily','An ingredient-category example often explored in oiliness-focused routines.','Check concentration and full ingredients; stop if irritation occurs.'),
('Gentle Routine Support','Treatment','Balanced','A placeholder for a non-prescription routine-support product category.','Do not use as treatment advice; seek professional guidance for persistent concerns.');
