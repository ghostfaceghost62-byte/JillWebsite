-- ============================================================
-- Jill Hotel Upgrade Migrations
-- Run this in phpMyAdmin against hotelreservation_db
-- ============================================================

USE hotelreservation_db;

-- 1. Extend rooms status enum with housekeeping workflow states
ALTER TABLE rooms
  MODIFY COLUMN status
    ENUM('AVAILABLE','OCCUPIED','BOOKED','CHECKED_IN','CHECKED_OUT',
         'MAINTENANCE','NEEDS_CLEANING','INSPECTED','INACTIVE')
    NOT NULL DEFAULT 'AVAILABLE';

-- 2. Add housekeeping permission flag to users (no extra table needed)
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS housekeeping TINYINT(1) NOT NULL DEFAULT 0;

-- 3. Reviews table
CREATE TABLE IF NOT EXISTS reviews (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,
  room_id      INT NOT NULL,
  reservation_id INT NOT NULL,
  rating       TINYINT UNSIGNED NOT NULL DEFAULT 5,
  title        VARCHAR(160),
  comment      TEXT,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_room_res (user_id, reservation_id),
  INDEX(room_id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(room_id) REFERENCES rooms(id) ON DELETE CASCADE,
  FOREIGN KEY(reservation_id) REFERENCES reservations(id) ON DELETE CASCADE
);

-- 4. Add-ons / upsells
CREATE TABLE IF NOT EXISTS add_ons (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(160) NOT NULL,
  description TEXT,
  price       DECIMAL(10,2) NOT NULL DEFAULT 0,
  active      TINYINT(1) NOT NULL DEFAULT 1,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed initial add-ons
INSERT IGNORE INTO add_ons (name, description, price) VALUES
  ('Airport Pickup',      'One-way private airport transfer to the hotel.',                   1200.00),
  ('Daily Breakfast Buffet','Access to the morning continental and local breakfast spread per guest/day.', 450.00),
  ('Extra Bed / Rollaway','Additional single rollaway bed setup for the room, per night.',     800.00),
  ('Spa Access Package',  'Day pass for sauna, steam room, and a 60-minute massage.',          1500.00);

-- 5. Reservation ↔ add-ons pivot
CREATE TABLE IF NOT EXISTS reservation_add_ons (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  reservation_id INT NOT NULL,
  add_on_id      INT NOT NULL,
  qty            INT NOT NULL DEFAULT 1,
  unit_price     DECIMAL(10,2) NOT NULL,
  INDEX(reservation_id),
  FOREIGN KEY(reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
  FOREIGN KEY(add_on_id)      REFERENCES add_ons(id) ON DELETE RESTRICT
);

-- 6. Currency exchange rate cache
CREATE TABLE IF NOT EXISTS currency_rates (
  currency   VARCHAR(10) NOT NULL PRIMARY KEY,
  rate       DECIMAL(18,6) NOT NULL,
  fetched_at DATETIME NOT NULL
);

-- Seed base PHP rate so the site has a starting point
INSERT IGNORE INTO currency_rates (currency, rate, fetched_at) VALUES
  ('PHP', 1.000000, NOW()),
  ('USD', 0.017300, NOW()),
  ('EUR', 0.016000, NOW()),
  ('JPY', 2.620000, NOW()),
  ('SGD', 0.023500, NOW()),
  ('AUD', 0.027000, NOW()),
  ('GBP', 0.013700, NOW());

-- ============================================================
-- Verify
-- ============================================================
SELECT 'Migrations applied successfully.' AS result;
