<?php

class Contact {
    private $db;

    public $id;
    public $gebruiker_id;
    public $email;
    public $mobiel;
    public $is_actief;
    public $opmerking;
    public $datum_aangemaakt;
    public $datum_gewijzigd;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Create a new Contact record.
     * @return bool True on success, false on failure.
     */
    public function create() {
        $this->db->query('INSERT INTO contact (gebruiker_id, email, mobiel, is_actief, opmerking) VALUES (:gebruiker_id, :email, :mobiel, :is_actief, :opmerking)');
        $this->db->bind(':gebruiker_id', $this->gebruiker_id, PDO::PARAM_INT);
        $this->db->bind(':email', $this->email, PDO::PARAM_STR);
        $this->db->bind(':mobiel', $this->mobiel, PDO::PARAM_STR);
        $isActief = ($this->is_actief) ? 1 : 0;
        $this->db->bind(':is_actief', $isActief, PDO::PARAM_INT);
        $this->db->bind(':opmerking', $this->opmerking, PDO::PARAM_STR);

        return $this->db->execute();
    }

    /**
     * Get all Contact records.
     * @return array An array of Contact objects.
     */
    public function getAll() {
        $this->db->query('SELECT * FROM contact');
        return $this->db->resultSet();
    }

    /**
     * Get a single Contact record by ID.
     * @param int $id The ID of the contact.
     * @return object|null The Contact object or null if not found.
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM contact WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get a Contact record by Gebruiker ID.
     * @param int $gebruiker_id The ID of the related gebruiker.
     * @return object|null The Contact object or null if not found.
     */
    public function getByGebruikerId($gebruiker_id) {
        $this->db->query('SELECT * FROM contact WHERE gebruiker_id = :gebruiker_id');
        $this->db->bind(':gebruiker_id', $gebruiker_id);
        return $this->db->single();
    }

    /**
     * Update an existing Contact record.
     * @return bool True on success, false on failure.
     */
    public function update() {
        $this->db->query('UPDATE contact SET gebruiker_id = :gebruiker_id, email = :email, mobiel = :mobiel, is_actief = :is_actief, opmerking = :opmerking WHERE id = :id');
        $this->db->bind(':id', $this->id, PDO::PARAM_INT);
        $this->db->bind(':gebruiker_id', $this->gebruiker_id, PDO::PARAM_INT);
        $this->db->bind(':email', $this->email, PDO::PARAM_STR);
        $this->db->bind(':mobiel', $this->mobiel, PDO::PARAM_STR);
        $isActief = ($this->is_actief) ? 1 : 0;
        $this->db->bind(':is_actief', $isActief, PDO::PARAM_INT);
        $this->db->bind(':opmerking', $this->opmerking, PDO::PARAM_STR);

        return $this->db->execute();
    }

    /**
     * Delete a Contact record by ID.
     * @param int $id The ID of the contact to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM contact WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Relationship methods
    public function getGebruiker() {
        $gebruikerModel = new Gebruiker();
        return $gebruikerModel->getById($this->gebruiker_id);
    }
}