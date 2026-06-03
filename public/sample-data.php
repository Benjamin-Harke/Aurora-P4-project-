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

    // 2. Insert Gebruikers
    $pwd = password_hash('password123', PASSWORD_DEFAULT);
    $db->query("INSERT INTO gebruiker (voornaam, achternaam, gebruikersnaam, wachtwoord) VALUES ('John', 'Doe', 'john@aurora.com', :pwd)");
    $db->bind(':pwd', $pwd);
    $db->execute();

    $db->query("SELECT id FROM gebruiker WHERE gebruikersnaam = 'john@aurora.com'");
    $userId = $db->single()->id;

    // 3. Insert Rol
    $db->query("INSERT INTO rol (gebruiker_id, naam) VALUES (:uid, 'Bezoeker')");
    $db->bind(':uid', $userId);
    $db->execute();

    // 4. Insert Contact
    $db->query("INSERT INTO contact (gebruiker_id, email, mobiel) VALUES (:uid, 'john@example.com', '0612345678')");
    $db->bind(':uid', $userId);
    $db->execute();

    // 5. Insert Bezoeker
    $db->query("INSERT INTO bezoeker (gebruiker_id, relatienummer) VALUES (:uid, 50001)");
    $db->bind(':uid', $userId);
    $db->execute();

    $db->query("SELECT id FROM bezoeker WHERE relatienummer = 50001");
    $bezoekerId = $db->single()->id;

    // 6. Insert Medewerker (Admin)
    $db->query("INSERT INTO gebruiker (voornaam, achternaam, gebruikersnaam, wachtwoord) VALUES ('Admin', 'User', 'admin@aurora.com', :pwd)");
    $db->bind(':pwd', $pwd);
    $db->execute();
    $db->query("SELECT id FROM gebruiker WHERE gebruikersnaam = 'admin@aurora.com'");
    $adminUserId = $db->single()->id;

    $db->query("INSERT INTO rol (gebruiker_id, naam, is_actief) VALUES (:uid, 'Admin', 1)");
    $db->bind(':uid', $adminUserId);
    $db->execute();

    $db->query("INSERT INTO medewerker (gebruiker_id, nummer, medewerkersoort) VALUES (:uid, 101, 'Beheerder')");
    $db->bind(':uid', $adminUserId);
    $db->execute();
    $db->query("SELECT id FROM medewerker WHERE nummer = 101");
    $medewerkerId = $db->single()->id;

    // 7. Insert Prijs
    $db->query("INSERT INTO prijs (tarief) VALUES (25.50)");
    $db->execute();
    $db->query("SELECT id FROM prijs WHERE tarief = 25.50");
    $prijsId = $db->single()->id;

    // 8. Insert Voorstelling
    $db->query("INSERT INTO voorstelling (medewerker_id, naam, datum, tijd, max_aantal_tickets, beschikbaarheid) 
                VALUES (:mid, 'Hamlet New Era', '2026-06-15', '19:30:00', 100, 'Ingepland')");
    $db->bind(':mid', $medewerkerId);
    $db->execute();
    $db->query("SELECT id FROM voorstelling WHERE naam = 'Hamlet New Era'");
    $voorstellingId = $db->single()->id;

    // 9. Insert Ticket (Using new schema fields)
    $db->query("INSERT INTO ticket (bezoeker_id, voorstelling_id, prijs_id, nummer, barcode, datum, tijd, status) 
                VALUES (:bid, :vid, :pid, 88001, 'BC-12345', '2026-06-15', '19:30:00', 'Gereserveerd')");
    $db->bind(':bid', $bezoekerId);
    $db->bind(':vid', $voorstellingId);
    $db->bind(':pid', $prijsId);
    $db->execute();

    echo "✓ Migration Sample Data loaded successfully!<br>";
    echo "<h3 style='color:green'>✓ Success!</h3>";
    echo "<a href='/publictickets'>→ Go to Public Tickets</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>