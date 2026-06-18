<?php

class Bezoeker {
    private $db;

    public $id;
    public $gebruiker_id;
    public $relatienummer;
    public $is_actief;
    public $opmerking;
    public $datum_aangemaakt;
    public $datum_gewijzigd;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Create a new Bezoeker record.
     * @return bool True on success, false on failure.
     */
    public function create() {
        $this->db->query('INSERT INTO bezoeker (gebruiker_id, relatienummer, is_actief, opmerking) VALUES (:gebruiker_id, :relatienummer, :is_actief, :opmerking)');
        $this->db->bind(':gebruiker_id', $this->gebruiker_id);
        $this->db->bind(':relatienummer', $this->relatienummer);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Get all Bezoeker records.
     * @return array An array of Bezoeker objects.
     */
    public function getAll() {
        $this->db->query('SELECT * FROM bezoeker');
        return $this->db->resultSet();
    }

    /**
     * Get all Bezoeker records with their Gebruiker name fields.
     * Used to populate the bezoeker dropdown on the ticket-create form.
     */
    public function getAllWithNames() {
        $this->db->query(
            'SELECT b.*, g.voornaam, g.tussenvoegsel, g.achternaam
             FROM bezoeker b
             JOIN gebruiker g ON b.gebruiker_id = g.id
             WHERE b.is_actief = 1
             ORDER BY g.achternaam ASC, g.voornaam ASC'
        );
        return $this->db->resultSet();
    }

    /**
     * Get a single Bezoeker record by ID.
     * @param int $id The ID of the bezoeker.
     * @return object|null The Bezoeker object or null if not found.
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM bezoeker WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get a Bezoeker record by Gebruiker ID.
     * @param int $gebruiker_id The ID of the related gebruiker.
     * @return object|null The Bezoeker object or null if not found.
     */
    public function getByGebruikerId($gebruiker_id) {
        $this->db->query('SELECT * FROM bezoeker WHERE gebruiker_id = :gebruiker_id');
        $this->db->bind(':gebruiker_id', $gebruiker_id);
        return $this->db->single();
    }

    /**
     * Update an existing Bezoeker record.
     * @return bool True on success, false on failure.
     */
    public function update() {
        $this->db->query('UPDATE bezoeker SET gebruiker_id = :gebruiker_id, relatienummer = :relatienummer, is_actief = :is_actief, opmerking = :opmerking WHERE id = :id');
        $this->db->bind(':id', $this->id);
        $this->db->bind(':gebruiker_id', $this->gebruiker_id);
        $this->db->bind(':relatienummer', $this->relatienummer);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Delete a Bezoeker record by ID.
     * @param int $id The ID of the bezoeker to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM bezoeker WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Relationship methods
    public function getGebruiker() {
        $gebruikerModel = new Gebruiker();
        return $gebruikerModel->getById($this->gebruiker_id);
    }

    public function getTickets() {
        $ticketModel = new Ticket();
        return $ticketModel->getByBezoekerId($this->id);
    }

    public function getMeldingen() {
        $meldingModel = new Melding();
        return $meldingModel->getByBezoekerId($this->id);
    }
}