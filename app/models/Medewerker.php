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

    /**
     * Create a new Medewerker record.
     * @return bool True on success, false on failure.
     */
    public function create() {
        $this->db->query('INSERT INTO medewerker (gebruiker_id, nummer, medewerkersoort, is_actief, opmerking) VALUES (:gebruiker_id, :nummer, :medewerkersoort, :is_actief, :opmerking)');
        $this->db->bind(':gebruiker_id', $this->gebruiker_id);
        $this->db->bind(':nummer', $this->nummer);
        $this->db->bind(':medewerkersoort', $this->medewerkersoort);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Get all Medewerker records.
     * @return array An array of Medewerker objects.
     */
    public function getAll() {
        $this->db->query('SELECT * FROM medewerker');
        return $this->db->resultSet();
    }

    /**
     * Get a single Medewerker record by ID.
     * @param int $id The ID of the medewerker.
     * @return object|null The Medewerker object or null if not found.
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM medewerker WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get a Medewerker record by Gebruiker ID.
     * @param int $gebruiker_id The ID of the related gebruiker.
     * @return object|null The Medewerker object or null if not found.
     */
    public function getByGebruikerId($gebruiker_id) {
        $this->db->query('SELECT * FROM medewerker WHERE gebruiker_id = :gebruiker_id');
        $this->db->bind(':gebruiker_id', $gebruiker_id);
        return $this->db->single();
    }

    /**
     * Update an existing Medewerker record.
     * @return bool True on success, false on failure.
     */
    public function update() {
        $this->db->query('UPDATE medewerker SET gebruiker_id = :gebruiker_id, nummer = :nummer, medewerkersoort = :medewerkersoort, is_actief = :is_actief, opmerking = :opmerking WHERE id = :id');
        $this->db->bind(':id', $this->id);
        $this->db->bind(':gebruiker_id', $this->gebruiker_id);
        $this->db->bind(':nummer', $this->nummer);
        $this->db->bind(':medewerkersoort', $this->medewerkersoort);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Delete a Medewerker record by ID.
     * @param int $id The ID of the medewerker to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM medewerker WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
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