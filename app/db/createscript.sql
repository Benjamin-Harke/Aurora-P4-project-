-- Theatre Ticket Management System Database Schema

-- Genres Table
CREATE TABLE IF NOT EXISTS `genres` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Shows Table (Theatre Productions/Plays)
CREATE TABLE IF NOT EXISTS `shows` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `genre_id` INT NOT NULL,
  `status` ENUM('on_sale', 'sold_out', 'cancelled', 'archived') DEFAULT 'on_sale',
  `image_url` VARCHAR(500),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`genre_id`) REFERENCES `genres`(`id`) ON DELETE RESTRICT,
  INDEX `idx_title` (`title`),
  INDEX `idx_genre_id` (`genre_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Performances Table (Individual Show Date/Time/Venue)
CREATE TABLE IF NOT EXISTS `performances` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `show_id` INT NOT NULL,
  `venue` VARCHAR(255) NOT NULL,
  `performance_date` DATE NOT NULL,
  `performance_time` TIME NOT NULL,
  `total_seats` INT NOT NULL,
  `available_seats` INT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `status` ENUM('on_sale', 'sold_out', 'cancelled') DEFAULT 'on_sale',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`show_id`) REFERENCES `shows`(`id`) ON DELETE CASCADE,
  INDEX `idx_show_id` (`show_id`),
  INDEX `idx_performance_date` (`performance_date`),
  INDEX `idx_status` (`status`),
  INDEX `idx_performance_datetime` (`performance_date`, `performance_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users Table (Theatre Customers)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `firstname` VARCHAR(100) NOT NULL,
  `infix` VARCHAR(50),
  `lastname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20),
  `role` ENUM('customer', 'admin', 'staff') DEFAULT 'customer',
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tickets Table (Individual Seats)
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `performance_id` INT NOT NULL,
  `seat_number` VARCHAR(10) NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `status` ENUM('available', 'booked', 'reserved', 'cancelled') DEFAULT 'available',
  `user_id` INT,
  `booking_date` TIMESTAMP NULL,
  `qr_code` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`performance_id`) REFERENCES `performances`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  UNIQUE KEY `unique_seat` (`performance_id`, `seat_number`),
  INDEX `idx_performance_id` (`performance_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_booking_date` (`booking_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
