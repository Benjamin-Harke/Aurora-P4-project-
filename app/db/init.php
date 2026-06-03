<?php
/**
 * Database initialization - Legacy support
 * The database schema is now created via public/db-init.php using createscript.sql
 * This file is kept for backward compatibility but doesn't need to do anything
 */

function initializeDatabase() {
    // Database initialization is now handled by public/db-init.php
    // which loads createscript.sql with the full ERD schema
    try {
        $db = new Database();
        // Verify that the required tables exist
        $db->query("SHOW TABLES LIKE 'gebruiker'");
        $result = $db->resultSet();
        if (empty($result)) {
            // Tables don't exist, would need to run db-init.php
            error_log("Database tables not found. Please run /db-init.php");
        }
    } catch (Exception $e) {
        error_log("Database initialization check failed: " . $e->getMessage());
    }
}

// Run initialization
initializeDatabase();

