<?php
/**
 * Create an admin user in the current schema (gebruiker, rol, contact)
 * Run: php public/setup_admin.php
 */

require_once __DIR__ . '/../app/config/config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $email = 'admin@aurora.com';
    $passwordPlain = 'Admin123!';
    $hashed = password_hash($passwordPlain, PASSWORD_DEFAULT);

    // If a gebruiker already exists with this gebruikersnaam, delete related records so we can recreate
    $stmt = $pdo->prepare('SELECT id FROM gebruiker WHERE gebruikersnaam = :email');
    $stmt->execute([':email' => $email]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        $gid = (int)$existing['id'];
        // delete will cascade to rol/contact if foreign keys are set; otherwise remove manually
        $pdo->prepare('DELETE FROM rol WHERE gebruiker_id = :id')->execute([':id' => $gid]);
        $pdo->prepare('DELETE FROM contact WHERE gebruiker_id = :id')->execute([':id' => $gid]);
        $pdo->prepare('DELETE FROM bezoeker WHERE gebruiker_id = :id')->execute([':id' => $gid]);
        $pdo->prepare('DELETE FROM medewerker WHERE gebruiker_id = :id')->execute([':id' => $gid]);
        $pdo->prepare('DELETE FROM gebruiker WHERE id = :id')->execute([':id' => $gid]);
        echo "Removed existing gebruiker with id=$gid\n";
    }

    // Insert gebruiker
    $ins = $pdo->prepare('INSERT INTO gebruiker (voornaam, achternaam, gebruikersnaam, wachtwoord, is_ingelogd, is_actief) VALUES (:voornaam, :achternaam, :gebruikersnaam, :wachtwoord, 0, 1)');
    $ins->execute([
        ':voornaam' => 'Admin',
        ':achternaam' => 'Aurora',
        ':gebruikersnaam' => $email,
        ':wachtwoord' => $hashed
    ]);

    $gebruikerId = (int)$pdo->lastInsertId();
    echo "Created gebruiker id=$gebruikerId\n";

    // Insert role
    $r = $pdo->prepare('INSERT INTO rol (gebruiker_id, naam, is_actief) VALUES (:gebruiker_id, :naam, 1)');
    $r->execute([':gebruiker_id' => $gebruikerId, ':naam' => 'admin']);

    // Insert contact
    $c = $pdo->prepare('INSERT INTO contact (gebruiker_id, email, mobiel, is_actief) VALUES (:gebruiker_id, :email, :mobiel, 1)');
    $c->execute([':gebruiker_id' => $gebruikerId, ':email' => $email, ':mobiel' => '']);

    echo "✓ Admin created. Login with {$email} / {$passwordPlain}\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
}

?>
