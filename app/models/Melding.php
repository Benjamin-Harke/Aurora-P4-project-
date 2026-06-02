<?php

class Melding {
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

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Create a new Melding record.
     * @return bool True on success, false on failure.
     */
    public function create() {
        $this->db->query('INSERT INTO melding (bezoeker_id, medewerker_id, nummer, type, bericht, is_actief, opmerking) VALUES (:bezoeker_id, :medewerker_id, :nummer, :type, :bericht, :is_actief, :opmerking)');
        $this->db->bind(':bezoeker_id', $this->bezoeker_id);
        $this->db->bind(':medewerker_id', $this->medewerker_id);
        $this->db->bind(':nummer', $this->nummer);
        $this->db->bind(':type', $this->type);
        $this->db->bind(':bericht', $this->bericht);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Get all Melding records.
     * @return array An array of Melding objects.
     */
    public function getAll() {
        $this->db->query('SELECT * FROM melding');
        return $this->db->resultSet();
    }

    /**
     * Get a single Melding record by ID.
     * @param int $id The ID of the melding.
     * @return object|null The Melding object or null if not found.
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM melding WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get Melding records by Bezoeker ID.
     * @param int $bezoeker_id The ID of the related bezoeker.
     * @return array An array of Melding objects.
     */
    public function getByBezoekerId($bezoeker_id) {
        $this->db->query('SELECT * FROM melding WHERE bezoeker_id = :bezoeker_id');
        $this->db->bind(':bezoeker_id', $bezoeker_id);
        return $this->db->resultSet();
    }

    /**
     * Get Melding records by Medewerker ID.
     * @param int $medewerker_id The ID of the related medewerker.
     * @return array An array of Melding objects.
     */
    public function getByMedewerkerId($medewerker_id) {
        $this->db->query('SELECT * FROM melding WHERE medewerker_id = :medewerker_id');
        $this->db->bind(':medewerker_id', $medewerker_id);
        return $this->db->resultSet();
    }

    /**
     * Update an existing Melding record.
     * @return bool True on success, false on failure.
     */
    public function update() {
        $this->db->query('UPDATE melding SET bezoeker_id = :bezoeker_id, medewerker_id = :medewerker_id, nummer = :nummer, type = :type, bericht = :bericht, is_actief = :is_actief, opmerking = :opmerking WHERE id = :id');
        $this->db->bind(':id', $this->id);
        $this->db->bind(':bezoeker_id', $this->bezoeker_id);
        $this->db->bind(':medewerker_id', $this->medewerker_id);
        $this->db->bind(':nummer', $this->nummer);
        $this->db->bind(':type', $this->type);
        $this->db->bind(':bericht', $this->bericht);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Delete a Melding record by ID.
     * @param int $id The ID of the melding to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM melding WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Relationship methods
    public function getBezoeker() {
        $bezoekerModel = new Bezoeker();
        return $bezoekerModel->getById($this->bezoeker_id);
    }

    public function getMedewerker() {
        $medewerkerModel = new Medewerker();
        return $medewerkerModel->getById($this->medewerker_id);
    }
}