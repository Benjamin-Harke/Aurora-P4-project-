<?php
/**
 * Database initialization - Create default admin account
 * Table schemas are defined in createscript*.sql files
 */

function initializeDatabase() {
    try {
        $db = new Database();
        createDefaultAdmin($db);
    } catch (Exception $e) {
        // Silently fail during initialization
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
