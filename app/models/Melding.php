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

    /**
     * Safely execute a query and return whether it was prepared successfully.
     */
    private function safeQuery($sql)
    {
        try {
            $this->db->query($sql);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Safely execute a statement and return the result.
     */
    private function safeExecute()
    {
        try {
            return $this->db->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Safely fetch multiple rows from the current statement.
     */
    private function safeResultSet()
    {
        try {
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Safely fetch a single row from the current statement.
     */
    private function safeSingle()
    {
        try {
            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Create a new Melding record.
     * Accepts an optional array $data. If provided, uses array values.
     * Otherwise falls back to object properties.
     *
     * @param array|null $data
     * @return bool True on success, false on failure.
     */
    public function create(array $data = [])
    {
        $bezoeker_id = $data['bezoeker_id'] ?? $this->bezoeker_id;
        $medewerker_id = $data['medewerker_id'] ?? $this->medewerker_id;
        $nummer = $data['nummer'] ?? $this->nummer;
        $type = $data['type'] ?? $this->type;
        $bericht = $data['bericht'] ?? $this->bericht;
        $is_actief = $data['is_actief'] ?? $this->is_actief;
        $opmerking = $data['opmerking'] ?? $this->opmerking;

        if (!$this->safeQuery('INSERT INTO melding (bezoeker_id, medewerker_id, nummer, type, bericht, is_actief, opmerking) VALUES (:bezoeker_id, :medewerker_id, :nummer, :type, :bericht, :is_actief, :opmerking)')) {
            return false;
        }

        $this->db->bind(':bezoeker_id', $bezoeker_id);
        $this->db->bind(':medewerker_id', $medewerker_id);
        $this->db->bind(':nummer', $nummer);
        $this->db->bind(':type', $type);
        $this->db->bind(':bericht', $bericht);
        $this->db->bind(':is_actief', $is_actief);
        $this->db->bind(':opmerking', $opmerking);

        return $this->safeExecute();
    }

    /**
     * Get all Melding records.
     */
    public function getAll()
    {
        if (!$this->safeQuery('SELECT * FROM melding')) {
            return [];
        }
        return $this->safeResultSet();
    }

    /**
     * Get a single Melding record by ID.
     */
    public function getById($id)
    {
        if (!$this->safeQuery('SELECT * FROM melding WHERE id = :id')) {
            return null;
        }
        $this->db->bind(':id', $id);
        return $this->safeSingle();
    }

    /**
     * Get Melding records by Bezoeker ID.
     */
    public function getByBezoekerId($bezoeker_id)
    {
        if (!$this->safeQuery('SELECT * FROM melding WHERE bezoeker_id = :bezoeker_id')) {
            return [];
        }
        $this->db->bind(':bezoeker_id', $bezoeker_id);
        return $this->safeResultSet();
    }

    /**
     * Get Melding records by Medewerker ID.
     */
    public function getByMedewerkerId($medewerker_id)
    {
        if (!$this->safeQuery('SELECT * FROM melding WHERE medewerker_id = :medewerker_id')) {
            return [];
        }
        $this->db->bind(':medewerker_id', $medewerker_id);
        return $this->safeResultSet();
    }

    /**
     * Update an existing Melding record.
     */
    public function update()
    {
        if (!$this->safeQuery('UPDATE melding SET bezoeker_id = :bezoeker_id, medewerker_id = :medewerker_id, nummer = :nummer, type = :type, bericht = :bericht, is_actief = :is_actief, opmerking = :opmerking WHERE id = :id')) {
            return false;
        }
        $this->db->bind(':id', $this->id);
        $this->db->bind(':bezoeker_id', $this->bezoeker_id);
        $this->db->bind(':medewerker_id', $this->medewerker_id);
        $this->db->bind(':nummer', $this->nummer);
        $this->db->bind(':type', $this->type);
        $this->db->bind(':bericht', $this->bericht);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->safeExecute();
    }

    /**
     * Delete a Melding record by ID.
     */
    public function delete($id)
    {
        if (!$this->safeQuery('DELETE FROM melding WHERE id = :id')) {
            return false;
        }
        $this->db->bind(':id', $id);
        return $this->safeExecute();
    }

    // Relationship methods
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