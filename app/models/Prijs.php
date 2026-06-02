<?php

class Prijs {
    private $db;

    public $id;
    public $tarief;
    public $is_actief;
    public $opmerking;
    public $datum_aangemaakt;
    public $datum_gewijzigd;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Create a new Prijs record.
     * @return bool True on success, false on failure.
     */
    public function create() {
        $this->db->query('INSERT INTO prijs (tarief, is_actief, opmerking) VALUES (:tarief, :is_actief, :opmerking)');
        $this->db->bind(':tarief', $this->tarief);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Get all Prijs records.
     * @return array An array of Prijs objects.
     */
    public function getAll() {
        $this->db->query('SELECT * FROM prijs');
        return $this->db->resultSet();
    }

    /**
     * Get a single Prijs record by ID.
     * @param int $id The ID of the prijs.
     * @return object|null The Prijs object or null if not found.
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM prijs WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Update an existing Prijs record.
     * @return bool True on success, false on failure.
     */
    public function update() {
        $this->db->query('UPDATE prijs SET tarief = :tarief, is_actief = :is_actief, opmerking = :opmerking WHERE id = :id');
        $this->db->bind(':id', $this->id);
        $this->db->bind(':tarief', $this->tarief);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Delete a Prijs record by ID.
     * @param int $id The ID of the prijs to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM prijs WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Relationship methods
    public function getTickets() {
        $ticketModel = new Ticket();
        return $ticketModel->getByPrijsId($this->id);
    }
}