<?php
/**
 * AURORA P4 - System Test
 * This file tests if all components are working correctly
 */

echo "<h1>🧪 Aurora P4 - System Test</h1>";
echo "<hr>";

// Test 1: PHP Version
echo "<h2>✅ Test 1: PHP Version</h2>";
echo "PHP Version: <strong>" . phpversion() . "</strong><br>";
if (phpversion() >= '8.0') {
    echo "✓ PHP 8.0+ is installed<br>";
} else {
    echo "✗ PHP version is too old<br>";
}

// Test 2: Required Extensions
echo "<h2>✅ Test 2: Required PHP Extensions</h2>";
$extensions = ['pdo', 'pdo_mysql'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ $ext is loaded<br>";
    } else {
        echo "✗ $ext is NOT loaded<br>";
    }
}

// Test 3: Load Configuration
echo "<h2>✅ Test 3: Application Configuration</h2>";
try {
    // Load only the config and libraries, not the Core instantiation
    require_once '../app/libraries/Core.php';
    require_once '../app/libraries/BaseController.php';
    require_once '../app/libraries/Database.php';
    require_once '../app/config/config.php';
    
    echo "✓ Configuration loaded successfully<br>";
    echo "URLROOT: <strong>" . URLROOT . "</strong><br>";
    if (defined('SITENAME')) {
        echo "SITENAME: <strong>" . SITENAME . "</strong><br>";
    } else {
        echo "SITENAME: <em>Not defined in config</em><br>";
    }
} catch (Exception $e) {
    echo "✗ Failed to load configuration: " . $e->getMessage() . "<br>";
}

// Test 4: Database Connection
echo "<h2>✅ Test 4: Database Connection</h2>";
try {
    $db = new Database();
    echo "✓ Database connection successful<br>";
    echo "DB Host: <strong>" . DB_HOST . "</strong><br>";
    echo "DB Name: <strong>" . DB_NAME . "</strong><br>";
    echo "DB User: <strong>" . DB_USER . "</strong><br>";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 5: File System Checks
echo "<h2>✅ Test 5: File System Checks</h2>";
$files = [
    'Controllers' => '../app/controllers/Homepages.php',
    'Views' => '../app/views/homepages/index.php',
    'Libraries' => '../app/libraries/Core.php',
];

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        echo "✓ $name directory exists<br>";
    } else {
        echo "✗ $name directory NOT found at $path<br>";
    }
}

// Test 6: Autoloading
echo "<h2>✅ Test 6: Core Framework</h2>";
try {
    if (class_exists('Core')) {
        echo "✓ Core class loaded<br>";
    }
    if (class_exists('BaseController')) {
        echo "✓ BaseController class loaded<br>";
    }
    if (class_exists('Database')) {
        echo "✓ Database class loaded<br>";
    }
} catch (Exception $e) {
    echo "✗ Framework error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>✅ All Tests Completed!</h2>";
echo "<p>If all tests passed, your Aurora P4 application is ready to go! 🚀</p>";
echo "<p><a href='/'>Go to Home Page</a></p>";
?>
