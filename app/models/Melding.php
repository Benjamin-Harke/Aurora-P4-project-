<?php

class Melding
{
    private $db;

    public $id;
    public $bezoeker_id;
    public $medewerker_id;
    public $nummer;
    public $type;
    public $bericht;
    public $is_actief;
    public $opmerking;
    public $datum_aangemaakt;
    public $datum_gewijzigd;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function create(array $data = [])
    {
        $bezoeker_id = $data['bezoeker_id'] ?? null;
        $nummer = $data['nummer'] ?? null;
        $type = $data['type'] ?? null;
        $bericht = $data['bericht'] ?? null;
        $is_actief = $data['is_actief'] ?? 1;
        $opmerking = $data['opmerking'] ?? null;

        try {
            $this->db->query(
                'INSERT INTO melding 
                (bezoeker_id, nummer, type, bericht, is_actief, opmerking) 
                VALUES 
                (:bezoeker_id, :nummer, :type, :bericht, :is_actief, :opmerking)'
            );

            $this->db->bind(':bezoeker_id', $bezoeker_id);
            $this->db->bind(':nummer', $nummer);
            $this->db->bind(':type', $type);
            $this->db->bind(':bericht', $bericht);
            $this->db->bind(':is_actief', (int) $is_actief, PDO::PARAM_INT);
            $this->db->bind(':opmerking', $opmerking);

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

    public function getAll()
    {
        try {
            $this->db->query('SELECT * FROM melding ORDER BY datum_aangemaakt DESC');

            return $this->db->resultSet();

        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id)
    {
        try {
            $this->db->query('SELECT * FROM melding WHERE id = :id');
            $this->db->bind(':id', $id);

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

    public function update()
    {
        try {
            $this->db->query(
                'UPDATE melding 
                 SET bezoeker_id = :bezoeker_id,
                     nummer = :nummer,
                     type = :type,
                     bericht = :bericht,
                     is_actief = :is_actief,
                     opmerking = :opmerking
                 WHERE id = :id'
            );

            $this->db->bind(':id', $this->id);
            $this->db->bind(':bezoeker_id', $this->bezoeker_id);
            $this->db->bind(':nummer', $this->nummer);
            $this->db->bind(':type', $this->type);
            $this->db->bind(':bericht', $this->bericht);
            $this->db->bind(':is_actief', $this->is_actief);
            $this->db->bind(':opmerking', $this->opmerking);

            return $this->db->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $this->db->query('DELETE FROM melding WHERE id = :id');
            $this->db->bind(':id', $id);

            return $this->db->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteAll()
    {
        try {
            $this->db->query('DELETE FROM melding');

            return $this->db->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    public function getBezoeker()
    {
        $bezoekerModel = new Bezoeker();

        return $bezoekerModel->getById($this->bezoeker_id);
    }

    public function getMedewerker()
    {
        $medewerkerModel = new Medewerker();

        return $medewerkerModel->getById($this->medewerker_id);
    }
}