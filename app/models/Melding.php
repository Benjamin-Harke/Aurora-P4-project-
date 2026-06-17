<?php

class Melding
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

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
            die('Database fout bij opslaan: ' . $e->getMessage());
        }
    }

    public function getByNummer($nummer)
    {
        try {
            $this->db->query('SELECT id FROM melding WHERE nummer = :nummer');
            $this->db->bind(':nummer', $nummer);

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

            $this->db->bind(':bezoeker_id', $bezoeker_id);

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

            $this->db->bind(':medewerker_id', $medewerker_id);

            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllBezoekers()
    {
        try {
            $this->db->query('SELECT id FROM bezoeker WHERE is_actief = 1');
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllMedewerkers()
    {
        try {
            $this->db->query('SELECT id FROM medewerker WHERE is_actief = 1');
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }
}