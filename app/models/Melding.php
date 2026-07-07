<?php

class Melding
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

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

    public function create(array $data = []) //Velden voor de melding formulier.
    {
        try {
            $isActief = ((int) $data['is_actief'] === 1) ? "b'1'" : "b'0'";

            $this->db->query(
                "INSERT INTO melding
                (bezoeker_id, medewerker_id, nummer, type, bericht, is_actief, opmerking)
                VALUES
                (:bezoeker_id, :medewerker_id, :nummer, :type, :bericht, $isActief, :opmerking)"
            );

            $this->db->bind(':bezoeker_id', $data['bezoeker_id'] ?? null);
            $this->db->bind(':medewerker_id', $data['medewerker_id'] ?? null);
            $this->db->bind(':nummer', $data['nummer']);
            $this->db->bind(':type', $data['type']);
            $this->db->bind(':bericht', $data['bericht']);
            $this->db->bind(':opmerking', $data['opmerking'] ?? null);

            return $this->db->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

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

    public function getById($id)
    {
        try {
            $this->db->query('SELECT * FROM melding WHERE id = :id LIMIT 1');
            $this->db->bind(':id', $id, PDO::PARAM_INT);
            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

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

    public function getBezoekerById($id)
    {
        try {
            $this->db->query(
                'SELECT id
                 FROM bezoeker
                 WHERE id = :id
                 AND is_actief = 1
                 LIMIT 1'
            );

            $this->db->bind(':id', $id, PDO::PARAM_INT);
            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

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

    public function createUnhappy() // unhappy scenario lege DB bij meldingen
    {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=unhappymeldingen',
                'root',
                ''
            );

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO melding (bericht) VALUES ('test')";
            $pdo->exec($sql);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function markeerAlsActief($id) // voor als je op het knop van gelezen klikt.
    {
        try {
            $this->db->query("
            UPDATE melding
            SET is_actief = b'1'
            WHERE id = :id
        ");

            $this->db->bind(':id', $id, PDO::PARAM_INT);

            return $this->db->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function markeerAlsGelezen($id) // Gelezen notificatie
    {
        try {
            $this->db->query("
            UPDATE melding
            SET is_actief = b'0'
            WHERE id = :id
        ");

            $this->db->bind(':id', $id, PDO::PARAM_INT);

            return $this->db->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}