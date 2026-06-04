-- ========================================
-- Aurora Theatre Accounts Database
-- ========================================
-- This file contains the schema and seed data for the Accounts table
-- Email: admin@aurora.com
-- Password: Admin123!

-- Create Accounts Table
CREATE TABLE IF NOT EXISTS Accounts
(
    Id                  INT             NOT NULL AUTO_INCREMENT
   ,Email               VARCHAR(255)    NOT NULL UNIQUE
   ,Password            VARCHAR(255)    NOT NULL
   ,FirstName           VARCHAR(100)    NOT NULL
   ,LastName            VARCHAR(100)    NOT NULL
   ,PhoneNumber         VARCHAR(20)     NULL
   ,CreatedAt           DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
   ,UpdatedAt           DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   ,IsActive            BIT             NOT NULL DEFAULT 1
   ,PRIMARY KEY (Id)
);

-- Insert Default Admin Account
INSERT IGNORE INTO Accounts (Email, Password, FirstName, LastName, IsActive) 
VALUES (
    'admin@aurora.com',
    '$2y$10$wfDGQ0Gmbo9X4OIQx.F88.0/HcrsmvvnjJ4uSAGhabUEp2sPBdAZC',
    'Admin',
    'Aurora',
    1
);
