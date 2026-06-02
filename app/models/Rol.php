<?php

class Rol {
    private $db;

    public $id;
    public $gebruiker_id;
    public $naam;
    public $is_actief;
    public $opmerking;
    public $datum_aangemaakt;
    public $datum_gewijzigd;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Create a new Rol record.
     * @return bool True on success, false on failure.
     */
    public function create() {
        $this->db->query('INSERT INTO rol (gebruiker_id, naam, is_actief, opmerking) VALUES (:gebruiker_id, :naam, :is_actief, :opmerking)');
        $this->db->bind(':gebruiker_id', $this->gebruiker_id);
        $this->db->bind(':naam', $this->naam);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Get all Rol records.
     * @return array An array of Rol objects.
     */
    public function getAll() {
        $this->db->query('SELECT * FROM rol');
        return $this->db->resultSet();
    }

    /**
     * Get a single Rol record by ID.
     * @param int $id The ID of the rol.
     * @return object|null The Rol object or null if not found.
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM rol WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get Rol records by Gebruiker ID.
     * @param int $gebruiker_id The ID of the related gebruiker.
     * @return array An array of Rol objects.
     */
    public function getByGebruikerId($gebruiker_id) {
        $this->db->query('SELECT * FROM rol WHERE gebruiker_id = :gebruiker_id');
        $this->db->bind(':gebruiker_id', $gebruiker_id);
        return $this->db->resultSet();
    }

    /**
     * Update an existing Rol record.
     * @return bool True on success, false on failure.
     */
    public function update() {
        $this->db->query('UPDATE rol SET gebruiker_id = :gebruiker_id, naam = :naam, is_actief = :is_actief, opmerking = :opmerking WHERE id = :id');
        $this->db->bind(':id', $this->id);
        $this->db->bind(':gebruiker_id', $this->gebruiker_id);
        $this->db->bind(':naam', $this->naam);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Delete a Rol record by ID.
     * @param int $id The ID of the rol to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM rol WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Relationship methods
    public function getGebruiker() {
        $gebruikerModel = new Gebruiker();
        return $gebruikerModel->getById($this->gebruiker_id);
    }
}