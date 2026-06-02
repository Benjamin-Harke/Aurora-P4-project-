<?php
/**
 * Database initialization
 * This file checks if required tables exist and creates them if not
 */

function initializeDatabase() {
    try {
        $db = new Database();
        
        // Check if Accounts table exists
        $result = $db->outQuery("SHOW TABLES LIKE 'Accounts'");
        
        if ($result->rowCount() == 0) {
            // Create Accounts table
            $sql = "CREATE TABLE IF NOT EXISTS Accounts (
                Id INT NOT NULL AUTO_INCREMENT,
                Email VARCHAR(255) NOT NULL UNIQUE,
                Password VARCHAR(255) NOT NULL,
                FirstName VARCHAR(100) NOT NULL,
                LastName VARCHAR(100) NOT NULL,
                PhoneNumber VARCHAR(20) NULL,
                CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UpdatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                IsActive BIT NOT NULL DEFAULT 1,
                PRIMARY KEY (Id)
            )";
            
            $db->outQuery($sql);
        }
        
        // Always check and create default admin account if it doesn't exist
        createDefaultAdmin($db);
        
    } catch (Exception $e) {
        // Silently fail during initialization
    }
}

function createDefaultAdmin($db) {
    try {
        // Check if admin already exists and has valid password
        $db->query("SELECT * FROM Accounts WHERE Email = :email");
        $db->bind(':email', 'admin@aurora.com', PDO::PARAM_STR);
        $admin = $db->single();
        
        // Delete if exists (to replace broken entries)
        if ($admin) {
            $db->query("DELETE FROM Accounts WHERE Email = :email");
            $db->bind(':email', 'admin@aurora.com', PDO::PARAM_STR);
            $db->execute();
        }
        
        // Create admin account with password: Admin123!
        $hashedPassword = password_hash('Admin123!', PASSWORD_DEFAULT);
        
        $db->query("INSERT INTO Accounts (Email, Password, FirstName, LastName, IsActive) VALUES (:email, :password, :firstName, :lastName, 1)");
        $db->bind(':email', 'admin@aurora.com', PDO::PARAM_STR);
        $db->bind(':password', $hashedPassword, PDO::PARAM_STR);
        $db->bind(':firstName', 'Admin', PDO::PARAM_STR);
        $db->bind(':lastName', 'Aurora', PDO::PARAM_STR);
        $db->execute();
        
    } catch (Exception $e) {
        // Silently fail
    }
}

// Run initialization
initializeDatabase();
