<?php
/**
 * Database Initialization Script
 * This file initializes the theatre ticket system database schema
 * Run this via browser: http://localhost/db-init.php
 */

// Load configuration
require_once __DIR__ . '/../app/config/config.php';

try {
    // Create PDO connection (without selecting database yet)
    $dsn = 'mysql:host=' . DB_HOST;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ));
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
    
    // Select database
    $pdo->exec("USE `" . DB_NAME . "`");
    
    // Define SQL statements directly
    $tables = [
        "genres" => "CREATE TABLE IF NOT EXISTS `genres` (
            `id` INT PRIMARY KEY AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL UNIQUE,
            `description` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "shows" => "CREATE TABLE IF NOT EXISTS `shows` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "performances" => "CREATE TABLE IF NOT EXISTS `performances` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "users" => "CREATE TABLE IF NOT EXISTS `users` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "tickets" => "CREATE TABLE IF NOT EXISTS `tickets` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    // Execute each CREATE TABLE statement
    $count = 0;
    foreach ($tables as $tableName => $sql) {
        $pdo->exec($sql);
        $count++;
        echo "✓ Created table: <strong>" . $tableName . "</strong><br>";
    }
    
    echo "<h3 style='color: green;'>✓ Database schema created successfully! (" . $count . " tables)</h3>";
    echo "<p><a href='/sample-data.php'>→ Load Sample Data</a></p>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>✗ Database Error</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Code:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
}
?>


