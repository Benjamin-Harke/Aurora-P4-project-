<?php
require_once __DIR__ . '/../app/config/config.php';
require_once APPROOT . '/libraries/Database.php';

$db = new Database();

try {
    // Insert genres
    $genres = [
        ['name' => 'Drama'], ['name' => 'Comedy'], 
        ['name' => 'Musical'], ['name' => 'Shakespeare']
    ];
    
    foreach ($genres as $g) {
        $db->query("INSERT IGNORE INTO genres (name) VALUES (:name)");
        $db->bind(':name', $g['name'], PDO::PARAM_STR);
        $db->execute();
    }
    echo "✓ Genres added<br>";
    
    // Insert shows
    $db->query("INSERT INTO shows (title, genre_id, status, description) 
               VALUES ('Hamlet', 4, 'on_sale', 'Shakespeare tragedy')");
    $db->execute();
    
    $db->query("INSERT INTO shows (title, genre_id, status, description) 
               VALUES ('Comedy Night', 2, 'on_sale', 'Laugh out loud')");
    $db->execute();
    
    echo "✓ Shows added<br>";
    
    // Insert performances
    $db->query("INSERT INTO performances (show_id, venue, performance_date, performance_time, total_seats, available_seats) 
               VALUES (1, 'Aurora Theatre', '2026-06-15', '19:30', 100, 100)");
    $db->execute();
    
    $db->query("INSERT INTO performances (show_id, venue, performance_date, performance_time, total_seats, available_seats) 
               VALUES (2, 'Aurora Theatre', '2026-07-05', '20:00', 80, 80)");
    $db->execute();
    
    echo "✓ Performances added<br>";
    
    // Insert test user
    $db->query("INSERT IGNORE INTO users (firstname, lastname, email, password, role) 
               VALUES ('Admin', 'Test', 'admin@test.com', :pwd, 'admin')");
    $db->bind(':pwd', password_hash('admin123', PASSWORD_BCRYPT), PDO::PARAM_STR);
    $db->execute();
    
    echo "✓ Test admin user created<br>";
    echo "<h3 style='color:green'>✓ Sample data loaded!</h3>";
    echo "<p><strong>Admin Login:</strong> admin@test.com / admin123</p>";
    echo "<a href='/publictickets'>→ Go to Public Tickets</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>