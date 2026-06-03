<?php
/**
 * Database initialization - Create tables and default admin account
 * Table schemas are defined in createscript*.sql files
 */

function initializeDatabase() {
    try {
        $db = new Database();
        createAccountsTable($db);
        createDefaultAdmin($db);
    } catch (Exception $e) {
        // Silently fail during initialization
    }
}

function createAccountsTable($db) {
    try {
        $db->query("
            CREATE TABLE IF NOT EXISTS Accounts (
                Id INT AUTO_INCREMENT PRIMARY KEY,
                Email VARCHAR(255) UNIQUE NOT NULL,
                Password VARCHAR(255) NOT NULL,
                FirstName VARCHAR(100) NOT NULL,
                LastName VARCHAR(100) NOT NULL,
                PhoneNumber VARCHAR(20),
                CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
                UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                IsActive BIT DEFAULT 1
            )
        ");
        $db->execute();
    } catch (Exception $e) {
        // Silently fail
    }
}

function createDefaultAdmin($db) {
    try {
        // Check if admin already exists and has valid password
        $db->query("SELECT * FROM Accounts WHERE Email = :email AND LENGTH(Password) > 50");
        $db->bind(':email', 'admin@aurora.com', PDO::PARAM_STR);
        $admin = $db->single();
        
        if (!$admin) {
            // Delete any broken admin entry
            $db->query("DELETE FROM Accounts WHERE Email = :email");
            $db->bind(':email', 'admin@aurora.com', PDO::PARAM_STR);
            $db->execute();
            
            // Create admin account with password: Admin123!
            $hashedPassword = password_hash('Admin123!', PASSWORD_DEFAULT);
            
            $db->query("INSERT INTO Accounts (Email, Password, FirstName, LastName, IsActive) VALUES (:email, :password, :firstName, :lastName, 1)");
            $db->bind(':email', 'admin@aurora.com', PDO::PARAM_STR);
            $db->bind(':password', $hashedPassword, PDO::PARAM_STR);
            $db->bind(':firstName', 'Admin', PDO::PARAM_STR);
            $db->bind(':lastName', 'Aurora', PDO::PARAM_STR);
            $db->execute();
        }
    } catch (Exception $e) {
        // Silently fail
    }
}

// Run initialization
initializeDatabase();
