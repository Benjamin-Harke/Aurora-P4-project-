<?php
require_once __DIR__ . '/../app/config/config.php';
require_once APPROOT . '/libraries/Database.php';

$db = new Database();

try {
    // Clear all user accounts to show empty state
    $db->query("SET FOREIGN_KEY_CHECKS = 0");
    $db->execute();
    
    $db->query("TRUNCATE TABLE `melding`");
    $db->execute();
    
    $db->query("TRUNCATE TABLE `ticket`");
    $db->execute();
    
    $db->query("TRUNCATE TABLE `bezoeker`");
    $db->execute();
    
    $db->query("TRUNCATE TABLE `medewerker`");
    $db->execute();
    
    $db->query("TRUNCATE TABLE `contact`");
    $db->execute();
    
    $db->query("TRUNCATE TABLE `rol`");
    $db->execute();
    
    $db->query("TRUNCATE TABLE `gebruiker`");
    $db->execute();
    
    $db->query("TRUNCATE TABLE `voorstelling`");
    $db->execute();
    
    $db->query("SET FOREIGN_KEY_CHECKS = 1");
    $db->execute();
    
    echo "<h2 style='color:green'>✓ All accounts cleared!</h2>";
    echo "<p>You can now see the empty accounts page.</p>";
    echo "<a href='" . URLROOT . "/accounts'>→ Go to Accounts Overview</a><br>";
    echo "<a href='" . URLROOT . "/sample-data.php'>→ Reload Sample Data</a>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
}
?>
