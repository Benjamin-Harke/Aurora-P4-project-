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
        PDO::ATTR_EMULATE_PREPARES => true // Required to execute multiple statements in one script
    ));
    
    // Drop and recreate database for a clean migration
    echo "Resetting database...<br>";
    $pdo->exec("DROP DATABASE IF EXISTS `" . DB_NAME . "`");
    $pdo->exec("CREATE DATABASE `" . DB_NAME . "`");
    
    // Select database
    $pdo->exec("USE `" . DB_NAME . "`");
    
    // Load new schema from file
    $sql = file_get_contents(__DIR__ . '/../app/db/createscript.sql');
    
    if ($sql === false) {
        throw new Exception("Cannot find createscript.sql");
    }

    // Execute the schema script
    $pdo->exec($sql);

    echo "<h3 style='color: green;'>✓ New Database schema (9 tables) created successfully!</h3>";
    echo "<p><a href='/sample-data.php'>→ Load Sample Data</a></p>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>✗ Database Error</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Code:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
}
?>
