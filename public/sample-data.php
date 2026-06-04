<?php
require_once __DIR__ . '/../app/config/config.php';
require_once APPROOT . '/libraries/Database.php';

$db = new Database();

try {
    // 1. Cleanup existing data to avoid Duplicate Entry errors
    $db->query("SET FOREIGN_KEY_CHECKS = 0");
    $db->execute();
    $tables = ['melding', 'ticket', 'voorstelling', 'prijs', 'bezoeker', 'medewerker', 'contact', 'rol', 'gebruiker'];
    foreach($tables as $table) {
        $db->query("TRUNCATE TABLE `$table` ");
        $db->execute();
    }
    $db->query("SET FOREIGN_KEY_CHECKS = 1");
    $db->execute();
    echo "✓ Database cleared for fresh migration<br>";

    // 2. Insert Admin User
    $adminPwd = password_hash('Admin123!', PASSWORD_DEFAULT);
    $db->query("INSERT INTO gebruiker (voornaam, achternaam, gebruikersnaam, wachtwoord, is_actief) VALUES ('Admin', 'User', 'admin@aurora.com', :pwd, 1)");
    $db->bind(':pwd', $adminPwd);
    $db->execute();

    $db->query("SELECT id FROM gebruiker WHERE gebruikersnaam = 'admin@aurora.com'");
    $adminUserId = $db->single()->id;

    // 3. Insert Admin Role
    $db->query("INSERT INTO rol (gebruiker_id, naam, is_actief) VALUES (:uid, 'Admin', 1)");
    $db->bind(':uid', $adminUserId);
    $db->execute();

    // 4. Insert Medewerker (Staff) for admin
    $db->query("INSERT INTO medewerker (gebruiker_id, nummer, medewerkersoort, is_actief) VALUES (:uid, 101, 'Beheerder', 1)");
    $db->bind(':uid', $adminUserId);
    $db->execute();
    $db->query("SELECT id FROM medewerker WHERE nummer = 101");
    $medewerkerId = $db->single()->id;

    // 5. Insert Regular User
    $userPwd = password_hash('password123', PASSWORD_DEFAULT);
    $db->query("INSERT INTO gebruiker (voornaam, achternaam, gebruikersnaam, wachtwoord, is_actief) VALUES ('John', 'Doe', 'john@aurora.com', :pwd, 1)");
    $db->bind(':pwd', $userPwd);
    $db->execute();

    $db->query("SELECT id FROM gebruiker WHERE gebruikersnaam = 'john@aurora.com'");
    $userId = $db->single()->id;

    // 6. Insert User Role
    $db->query("INSERT INTO rol (gebruiker_id, naam, is_actief) VALUES (:uid, 'Bezoeker', 1)");
    $db->bind(':uid', $userId);
    $db->execute();

    // 7. Insert Bezoeker (Visitor)
    $db->query("INSERT INTO bezoeker (gebruiker_id, relatienummer, is_actief) VALUES (:uid, 50001, 1)");
    $db->bind(':uid', $userId);
    $db->execute();

    $db->query("SELECT id FROM bezoeker WHERE relatienummer = 50001");
    $bezoekerId = $db->single()->id;

    // 8. Insert Prices
    $prices = [25.50, 35.00, 45.00];
    $priceIds = [];
    foreach ($prices as $price) {
        $db->query("INSERT INTO prijs (tarief) VALUES (:price)");
        $db->bind(':price', $price);
        $db->execute();
        $db->query("SELECT id FROM prijs WHERE tarief = :price");
        $db->bind(':price', $price);
        $priceIds[] = $db->single()->id;
    }

    // 9. Insert Multiple Voorstellingen (Shows)
    $shows = [
        ['Hamlet New Era', '2026-06-15', '19:30:00', 100],
        ['Romeo & Juliet', '2026-06-20', '20:00:00', 80],
        ['Macbeth', '2026-06-25', '19:00:00', 120],
        ['A Midsummer Night\'s Dream', '2026-07-05', '18:30:00', 90]
    ];

    $voorstellingIds = [];
    foreach ($shows as $show) {
        $db->query("INSERT INTO voorstelling (medewerker_id, naam, datum, tijd, max_aantal_tickets, beschikbaarheid, is_actief) 
                    VALUES (:mid, :naam, :datum, :tijd, :max, 'Ingepland', 1)");
        $db->bind(':mid', $medewerkerId);
        $db->bind(':naam', $show[0]);
        $db->bind(':datum', $show[1]);
        $db->bind(':tijd', $show[2]);
        $db->bind(':max', $show[3]);
        $db->execute();
        
        $db->query("SELECT id FROM voorstelling WHERE naam = :naam");
        $db->bind(':naam', $show[0]);
        $voorstellingIds[] = $db->single()->id;
    }

    // 10. Insert Sample Tickets
    $db->query("INSERT INTO ticket (bezoeker_id, voorstelling_id, prijs_id, nummer, barcode, datum, tijd, status, is_actief) 
                VALUES (:bid, :vid, :pid, 88001, 'BC-12345', :datum, :tijd, 'Gereserveerd', 1)");
    $db->bind(':bid', $bezoekerId);
    $db->bind(':vid', $voorstellingIds[0]);
    $db->bind(':pid', $priceIds[0]);
    $db->bind(':datum', '2026-06-15');
    $db->bind(':tijd', '19:30:00');
    $db->execute();

    echo "✓ Database setup completed successfully!<br>";
    echo "<h3 style='color:green'>✓ Success!</h3>";
    echo "<p><strong>Admin Account:</strong> admin@aurora.com / Admin123!</p>";
    echo "<p><strong>User Account:</strong> john@aurora.com / password123</p>";
    echo "<a href='/publictickets'>→ Go to Public Tickets</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>