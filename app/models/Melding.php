<?php

class Melding
{
    // Database verbinding
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Rollen ophalen van een gebruiker
    public function getRollenByGebruikerId($gebruiker_id)
    {
        try {
            $this->db->query(
                'SELECT naam
                 FROM rol
                 WHERE gebruiker_id = :gebruiker_id
                 AND is_actief = 1'
            );

            $this->db->bind(':gebruiker_id', $gebruiker_id, PDO::PARAM_INT);

            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Nieuwe melding opslaan in de database
    public function create(array $data = [])
    {
        try {
            $this->db->query(
                'INSERT INTO melding
                (bezoeker_id, medewerker_id, nummer, type, bericht, is_actief, opmerking)
                VALUES
                (:bezoeker_id, :medewerker_id, :nummer, :type, :bericht, :is_actief, :opmerking)'
            );

            $this->db->bind(':bezoeker_id', $data['bezoeker_id'] ?? null);
            $this->db->bind(':medewerker_id', $data['medewerker_id'] ?? null);
            $this->db->bind(':nummer', $data['nummer']);
            $this->db->bind(':type', $data['type']);
            $this->db->bind(':bericht', $data['bericht']);
            $this->db->bind(':is_actief', (int) $data['is_actief'], PDO::PARAM_INT);
            $this->db->bind(':opmerking', $data['opmerking'] ?? null);

            return $this->db->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Controleren of een meldingsnummer al bestaat
    public function getByNummer($nummer)
    {
        try {
            $this->db->query('SELECT id FROM melding WHERE nummer = :nummer');
            $this->db->bind(':nummer', $nummer, PDO::PARAM_INT);

            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    // Meldingen ophalen voor een bezoeker
    public function getByBezoekerId($bezoeker_id)
    {
        try {
            $this->db->query(
                'SELECT *
                 FROM melding
                 WHERE bezoeker_id = :bezoeker_id
                 ORDER BY datum_aangemaakt DESC'
            );

            $this->db->bind(':bezoeker_id', $bezoeker_id, PDO::PARAM_INT);

            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Meldingen ophalen voor een medewerker
    public function getByMedewerkerId($medewerker_id)
    {
        try {
            $this->db->query(
                'SELECT *
                 FROM melding
                 WHERE medewerker_id = :medewerker_id
                 ORDER BY datum_aangemaakt DESC'
            );

            $this->db->bind(':medewerker_id', $medewerker_id, PDO::PARAM_INT);

            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Alle actieve bezoekers ophalen
    public function getAllBezoekers()
    {
        try {
            $this->db->query(
                'SELECT id
                 FROM bezoeker
                 WHERE is_actief = 1'
            );

            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Alle actieve medewerkers ophalen
    public function getAllMedewerkers()
    {
        try {
            $this->db->query(
                'SELECT id
                 FROM medewerker
                 WHERE is_actief = 1'
            );

            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Bezoeker id ophalen via gebruiker id
    public function getBezoekerByGebruikerId($gebruiker_id)
    {
        try {
            $this->db->query(
                'SELECT id
                 FROM bezoeker
                 WHERE gebruiker_id = :gebruiker_id
                 AND is_actief = 1
                 LIMIT 1'
            );

            $this->db->bind(':gebruiker_id', $gebruiker_id, PDO::PARAM_INT);

            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    // Medewerker id ophalen via gebruiker id
    public function getMedewerkerByGebruikerId($gebruiker_id)
    {
        try {
            $this->db->query(
                'SELECT id
                 FROM medewerker
                 WHERE gebruiker_id = :gebruiker_id
                 AND is_actief = 1
                 LIMIT 1'
            );

            $this->db->bind(':gebruiker_id', $gebruiker_id, PDO::PARAM_INT);

            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function createUnhappy()
    {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=unhappymeldingen',
                'root',
                ''
            );

            $sql = "INSERT INTO melding (bericht) VALUES ('test')";
            $pdo->exec($sql);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}