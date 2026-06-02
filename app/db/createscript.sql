-- Theatre Ticket Management System Database Schema
-- Based on the ERD provided
-- Created: June 1, 2026

-- ===================================
-- 1. GEBRUIKER (User) Table
-- ===================================
CREATE TABLE IF NOT EXISTS `gebruiker` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `voornaam` VARCHAR(50) NOT NULL,
  `tussenvoegsel` VARCHAR(10),
  `achternaam` VARCHAR(50) NOT NULL,
  `gebruikersnaam` VARCHAR(100) NOT NULL, -- No UNIQUE constraint as per new spec
  `wachtwoord` VARCHAR(255) NOT NULL,
  `is_ingelogd` BIT NOT NULL DEFAULT 0,
  `ingelogd_datum` DATETIME NULL,
  `uitgelogd_datum` DATETIME NULL,
  `is_actief` BIT NOT NULL DEFAULT 1,
  `opmerking` VARCHAR(250),
  `datum_aangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `datum_gewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  INDEX `idx_gebruikersnaam` (`gebruikersnaam`),
  INDEX `idx_is_actief` (`is_actief`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 2. ROL (Role) Table
-- ===================================
CREATE TABLE IF NOT EXISTS `rol` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `gebruiker_id` INT NOT NULL, -- No UNIQUE constraint as per new spec
  `naam` VARCHAR(100) NOT NULL,
  `is_actief` BIT NOT NULL DEFAULT 1,
  `opmerking` VARCHAR(250),
  `datum_aangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `datum_gewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruiker`(`id`) ON DELETE CASCADE,
  INDEX `idx_gebruiker_id` (`gebruiker_id`),
  INDEX `idx_naam` (`naam`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 3. CONTACT Table
-- ===================================
CREATE TABLE IF NOT EXISTS `contact` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `gebruiker_id` INT NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `mobiel` VARCHAR(20) NOT NULL,
  `is_actief` BIT NOT NULL DEFAULT 1,
  `opmerking` VARCHAR(250),
  `datum_aangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `datum_gewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruiker`(`id`) ON DELETE CASCADE,
  INDEX `idx_gebruiker_id` (`gebruiker_id`),
  INDEX `idx_email` (`email`) -- No UNIQUE KEY `unique_email_user` as per new spec
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 4. MEDEWERKER (Employee) Table
-- ===================================
CREATE TABLE IF NOT EXISTS `medewerker` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `gebruiker_id` INT NOT NULL,
  `nummer` MEDIUMINT NOT NULL UNIQUE,
  `medewerkersoort` VARCHAR(20) NOT NULL,
  `is_actief` BIT NOT NULL DEFAULT 1,
  `opmerking` VARCHAR(250),
  `datum_aangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `datum_gewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruiker`(`id`) ON DELETE CASCADE,
  INDEX `idx_gebruiker_id` (`gebruiker_id`),
  INDEX `idx_nummer` (`nummer`),
  INDEX `idx_medewerkersoort` (`medewerkersoort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 5. BEZOEKER (Visitor) Table
-- ===================================
CREATE TABLE IF NOT EXISTS `bezoeker` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `gebruiker_id` INT NOT NULL,
  `relatienummer` MEDIUMINT NOT NULL UNIQUE,
  `is_actief` BIT NOT NULL DEFAULT 1,
  `opmerking` VARCHAR(250),
  `datum_aangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `datum_gewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruiker`(`id`) ON DELETE CASCADE,
  INDEX `idx_gebruiker_id` (`gebruiker_id`),
  INDEX `idx_relatienummer` (`relatienummer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 6. PRIJS (Price) Table
-- ===================================
CREATE TABLE IF NOT EXISTS `prijs` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `tarief` DECIMAL(5,2) NOT NULL,
  `is_actief` BIT NOT NULL DEFAULT 1,
  `opmerking` VARCHAR(250),
  `datum_aangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `datum_gewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  INDEX `idx_is_actief` (`is_actief`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 7. VOORSTELLING (Performance) Table
-- ===================================
CREATE TABLE IF NOT EXISTS `voorstelling` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `medewerker_id` INT NOT NULL,
  `naam` VARCHAR(100) NOT NULL,
  `beschrijving` TEXT NULL,
  `datum` DATE NOT NULL,
  `tijd` TIME NOT NULL,
  `max_aantal_tickets` INT NOT NULL,
  `beschikbaarheid` VARCHAR(50) NOT NULL,
  `is_actief` BIT NOT NULL DEFAULT 1,
  `opmerking` VARCHAR(250),
  `datum_aangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `datum_gewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  FOREIGN KEY (`medewerker_id`) REFERENCES `medewerker`(`id`) ON DELETE CASCADE,
  INDEX `idx_medewerker_id` (`medewerker_id`),
  INDEX `idx_datum` (`datum`),
  INDEX `idx_beschikbaarheid` (`beschikbaarheid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 8. TICKET Table
-- ===================================
CREATE TABLE IF NOT EXISTS `ticket` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `bezoeker_id` INT NOT NULL,
  `voorstelling_id` INT NOT NULL,
  `prijs_id` INT NOT NULL,
  `nummer` MEDIUMINT NOT NULL UNIQUE,
  `barcode` VARCHAR(20) NOT NULL UNIQUE,
  `datum` DATE NOT NULL,
  `tijd` TIME NOT NULL,
  `status` VARCHAR(20) NOT NULL,
  `is_actief` BIT NOT NULL DEFAULT 1,
  `opmerking` VARCHAR(250),
  `datum_aangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `datum_gewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  FOREIGN KEY (`bezoeker_id`) REFERENCES `bezoeker`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`voorstelling_id`) REFERENCES `voorstelling`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`prijs_id`) REFERENCES `prijs`(`id`) ON DELETE RESTRICT,
  INDEX `idx_bezoeker_id` (`bezoeker_id`),
  INDEX `idx_voorstelling_id` (`voorstelling_id`),
  INDEX `idx_prijs_id` (`prijs_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_datum` (`datum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 9. MELDING (Notification/Report) Table
-- ===================================
CREATE TABLE IF NOT EXISTS `melding` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `bezoeker_id` INT,
  `medewerker_id` INT,
  `nummer` MEDIUMINT NOT NULL UNIQUE, -- "Uniek reserveringsnummer"
  `type` VARCHAR(20) NOT NULL,
  `bericht` VARCHAR(250) NOT NULL,
  `is_actief` BIT NOT NULL DEFAULT 1,
  `opmerking` VARCHAR(250),
  `datum_aangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `datum_gewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  FOREIGN KEY (`bezoeker_id`) REFERENCES `bezoeker`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`medewerker_id`) REFERENCES `medewerker`(`id`) ON DELETE SET NULL,
  INDEX `idx_bezoeker_id` (`bezoeker_id`),
  INDEX `idx_medewerker_id` (`medewerker_id`),
  INDEX `idx_type` (`type`),
  INDEX `idx_datum_aangemaakt` (`datum_aangemaakt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
