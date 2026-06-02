<?php
require_once 'app/require.php';

$db = new Database();

// Delete existing admin account if it exists
$db->query("DELETE FROM Accounts WHERE Email = :email");
$db->bind(':email', 'admin@aurora.com', PDO::PARAM_STR);
$db->execute();

// Insert new admin account with correct password
$hashedPassword = '$2y$10$wfDGQ0Gmbo9X4OIQx.F88.0/HcrsmvvnjJ4uSAGhabUEp2sPBdAZC';
$db->query("INSERT INTO Accounts (Email, Password, FirstName, LastName, IsActive) VALUES (:email, :password, :firstName, :lastName, 1)");
$db->bind(':email', 'admin@aurora.com', PDO::PARAM_STR);
$db->bind(':password', $hashedPassword, PDO::PARAM_STR);
$db->bind(':firstName', 'Admin', PDO::PARAM_STR);
$db->bind(':lastName', 'Aurora', PDO::PARAM_STR);
$db->execute();

// Verify
$db->query("SELECT Email, Password FROM Accounts WHERE Email = :email");
$db->bind(':email', 'admin@aurora.com', PDO::PARAM_STR);
$account = $db->single();

if ($account) {
    echo "✓ Admin account created successfully!\n";
    echo "Email: " . $account->Email . "\n";
    echo "Password length: " . strlen($account->Password) . " characters\n";
    
    // Test password verification
    if (password_verify('Admin123!', $account->Password)) {
        echo "✓ Password verification works!\n";
    } else {
        echo "✗ Password verification FAILED\n";
    }
} else {
    echo "✗ Failed to create admin account\n";
}
?>
