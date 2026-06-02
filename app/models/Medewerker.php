<?php

class Medewerker {
    private $db;

    public $id;
    public $gebruiker_id;
    public $nummer;
    public $medewerkersoort;
    public $is_actief;
    public $opmerking;
    public $datum_aangemaakt;
    public $datum_gewijzigd;

    public function __construct() {
        $this->db = new Database();
    }

    private function safeQuery($sql) {
        try {
            $this->db->query($sql);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function safeExecute() {
        try {
            return $this->db->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    private function safeResultSet() {
        try {
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    private function safeSingle() {
        try {
            return $this->db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Create a new Medewerker record.
     * @return bool True on success, false on failure.
     */
    public function create() {
        if (!$this->safeQuery('INSERT INTO medewerker (gebruiker_id, nummer, medewerkersoort, is_actief, opmerking) VALUES (:gebruiker_id, :nummer, :medewerkersoort, :is_actief, :opmerking)')) {
            return false;
        }
        $this->db->bind(':gebruiker_id', $this->gebruiker_id);
        $this->db->bind(':nummer', $this->nummer);
        $this->db->bind(':medewerkersoort', $this->medewerkersoort);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->safeExecute();
    }

    /**
     * Get all Medewerker records.
     * @return array An array of Medewerker objects.
     */
    public function getAll() {
        if (!$this->safeQuery('SELECT * FROM medewerker')) {
            return [];
        }
        return $this->safeResultSet();
    }

    /**
     * Get a single Medewerker record by ID.
     * @param int $id The ID of the medewerker.
     * @return object|null The Medewerker object or null if not found.
     */
    public function getById($id) {
        if (!$this->safeQuery('SELECT * FROM medewerker WHERE id = :id')) {
            return null;
        }
        $this->db->bind(':id', $id);
        return $this->safeSingle();
    }

    /**
     * Get a Medewerker record by Gebruiker ID.
     * @param int $gebruiker_id The ID of the related gebruiker.
     * @return object|null The Medewerker object or null if not found.
     */
    public function getByGebruikerId($gebruiker_id) {
        if (!$this->safeQuery('SELECT * FROM medewerker WHERE gebruiker_id = :gebruiker_id')) {
            return null;
        }
        $this->db->bind(':gebruiker_id', $gebruiker_id);
        return $this->safeSingle();
    }

    /**
     * Update an existing Medewerker record.
     * @return bool True on success, false on failure.
     */
    public function update() {
        if (!$this->safeQuery('UPDATE medewerker SET gebruiker_id = :gebruiker_id, nummer = :nummer, medewerkersoort = :medewerkersoort, is_actief = :is_actief, opmerking = :opmerking WHERE id = :id')) {
            return false;
        }
        $this->db->bind(':id', $this->id);
        $this->db->bind(':gebruiker_id', $this->gebruiker_id);
        $this->db->bind(':nummer', $this->nummer);
        $this->db->bind(':medewerkersoort', $this->medewerkersoort);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->safeExecute();
    }

    /**
     * Delete a Medewerker record by ID.
     * @param int $id The ID of the medewerker to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        if (!$this->safeQuery('DELETE FROM medewerker WHERE id = :id')) {
            return false;
        }
        $this->db->bind(':id', $id);
        return $this->safeExecute();
    }

    // Relationship methods
    public function getGebruiker() {
        $gebruikerModel = new Gebruiker();
        return $gebruikerModel->getById($this->gebruiker_id);
    }

    public function getVoorstellingen() {
        $voorstellingModel = new Voorstelling();
        return $voorstellingModel->getByMedewerkerId($this->id);
    }

    public function getMeldingen() {
        $meldingModel = new Melding();
        return $meldingModel->getByMedewerkerId($this->id);
    }
}