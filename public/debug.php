<?php
/**
 * Debug page - Check database accounts
 * Remove this file after debugging
 */

require_once '../app/require.php';

echo '<pre style="background: #000; color: #0f0; padding: 20px; font-family: monospace;">';
echo "=== DATABASE DEBUG ===\n\n";

try {
    $db = new Database();
    
    // Get all accounts
    $db->query('SELECT * FROM Accounts');
    $accounts = $db->resultSet();
    
    echo "Total accounts: " . count($accounts) . "\n\n";
    
    foreach ($accounts as $account) {
        echo "ID: " . $account->Id . "\n";
        echo "Email: " . $account->Email . "\n";
        echo "First Name: " . $account->FirstName . "\n";
        echo "Password Hash: " . substr($account->Password, 0, 20) . "...\n";
        echo "IsActive: " . $account->IsActive . "\n";
        
        // Test password verify
        echo "Testing password_verify with 'Admin123!': ";
        if ($account->Email == 'admin@aurora.com') {
            $result = password_verify('Admin123!', $account->Password);
            echo ($result ? "✓ WORKS" : "✗ FAILED") . "\n";
        } else {
            echo "(not admin account)\n";
        }
        echo "\n";
    }
    
    if (empty($accounts)) {
        echo "⚠ No accounts found in database!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

echo '</pre>';
?>
