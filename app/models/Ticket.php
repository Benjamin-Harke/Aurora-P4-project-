<?php

class Ticket {
    private $db;

    public $id;
    public $bezoeker_id;
    public $voorstelling_id;
    public $prijs_id;
    public $nummer;
    public $barcode;
    public $datum;
    public $tijd;
    public $status;
    public $is_actief;
    public $opmerking;
    public $datum_aangemaakt;
    public $datum_gewijzigd;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Create a new Ticket record.
     * @return bool True on success, false on failure.
     */
    public function create() {
        $this->db->query('INSERT INTO ticket (bezoeker_id, voorstelling_id, prijs_id, nummer, barcode, datum, tijd, status, is_actief, opmerking) VALUES (:bezoeker_id, :voorstelling_id, :prijs_id, :nummer, :barcode, :datum, :tijd, :status, :is_actief, :opmerking)');
        $this->db->bind(':bezoeker_id', $this->bezoeker_id);
        $this->db->bind(':voorstelling_id', $this->voorstelling_id);
        $this->db->bind(':prijs_id', $this->prijs_id);
        $this->db->bind(':nummer', $this->nummer);
        $this->db->bind(':barcode', $this->barcode);
        $this->db->bind(':datum', $this->datum);
        $this->db->bind(':tijd', $this->tijd);
        $this->db->bind(':status', $this->status);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Get all Ticket records.
     * @return array An array of Ticket objects.
     */
    /**
     * Get all tickets with JOINs for the Dashboard
     */
    public function getAll() {
        $this->db->query('
            SELECT 
                t.*, 
                v.naam as voorstelling_naam,
                v.id as voorstelling_id,
                g.voornaam, g.achternaam,
                p.tarief
            FROM ticket t
            JOIN voorstelling v ON t.voorstelling_id = v.id
            JOIN bezoeker b ON t.bezoeker_id = b.id
            JOIN gebruiker g ON b.gebruiker_id = g.id
            JOIN prijs p ON t.prijs_id = p.id
        ');
        return $this->db->resultSet();
    }

    /**
     * Get tickets for a specific show with Customer Names
     */
    public function getByVoorstellingIdWithNames($id) {
    $this->db->query('
        SELECT t.*, g.voornaam, g.tussenvoegsel, g.achternaam, p.tarief
        FROM ticket t
        JOIN bezoeker b ON t.bezoeker_id = b.id
        JOIN gebruiker g ON b.gebruiker_id = g.id
        JOIN prijs p ON t.prijs_id = p.id
        WHERE t.voorstelling_id = :id
    ');
    $this->db->bind(':id', $id);
    return $this->db->resultSet();
    }

    /**
     * Get a single Ticket record by ID.
     * @param int $id The ID of the ticket.
     * @return object|null The Ticket object or null if not found.
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM ticket WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get Ticket records by Bezoeker ID.
     * @param int $bezoeker_id The ID of the related bezoeker.
     * @return array An array of Ticket objects.
     */
    public function getByBezoekerId($bezoeker_id) {
        $this->db->query('SELECT * FROM ticket WHERE bezoeker_id = :bezoeker_id');
        $this->db->bind(':bezoeker_id', $bezoeker_id);
        return $this->db->resultSet();
    }

    /**
     * Get Ticket records by Voorstelling ID.
     * @param int $voorstelling_id The ID of the related voorstelling.
     * @return array An array of Ticket objects.
     */
    public function getByVoorstellingId($voorstelling_id) {
        $this->db->query('SELECT * FROM ticket WHERE voorstelling_id = :voorstelling_id');
        $this->db->bind(':voorstelling_id', $voorstelling_id);
        return $this->db->resultSet();
    }

    /**
     * Get Ticket records by Prijs ID.
     * @param int $prijs_id The ID of the related prijs.
     * @return array An array of Ticket objects.
     */
    public function getByPrijsId($prijs_id) {
        $this->db->query('SELECT * FROM ticket WHERE prijs_id = :prijs_id');
        $this->db->bind(':prijs_id', $prijs_id);
        return $this->db->resultSet();
    }

    /**
     * Update an existing Ticket record.
     * @return bool True on success, false on failure.
     */
    public function update() {
        $this->db->query('UPDATE ticket SET bezoeker_id = :bezoeker_id, voorstelling_id = :voorstelling_id, prijs_id = :prijs_id, nummer = :nummer, barcode = :barcode, datum = :datum, tijd = :tijd, status = :status, is_actief = :is_actief, opmerking = :opmerking WHERE id = :id');
        $this->db->bind(':id', $this->id);
        $this->db->bind(':bezoeker_id', $this->bezoeker_id);
        $this->db->bind(':voorstelling_id', $this->voorstelling_id);
        $this->db->bind(':prijs_id', $this->prijs_id);
        $this->db->bind(':nummer', $this->nummer);
        $this->db->bind(':barcode', $this->barcode);
        $this->db->bind(':datum', $this->datum);
        $this->db->bind(':tijd', $this->tijd);
        $this->db->bind(':status', $this->status);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Delete a Ticket record by ID.
     * @param int $id The ID of the ticket to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM ticket WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Relationship methods
    public function getBezoeker() {
        $bezoekerModel = new Bezoeker();
        return $bezoekerModel->getById($this->bezoeker_id);
    }

    public function getVoorstelling() {
        $voorstellingModel = new Voorstelling();
        return $voorstellingModel->getById($this->voorstelling_id);
    }

    public function getPrijs() {
        $prijsModel = new Prijs();
        return $prijsModel->getById($this->prijs_id);
    }
}