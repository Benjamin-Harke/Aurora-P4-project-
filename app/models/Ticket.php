<?php

class Ticket {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * ADMIN: Get all tickets with JOINs for the main dashboard
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
     * USER: Get tickets for a specific visitor (Jane Doe test)
     */
    public function getByBezoekerIdWithNames($bezoekerId) {
        $this->db->query('
            SELECT 
                t.*, 
                v.naam as voorstelling_naam, 
                v.datum as voorstelling_datum,
                v.tijd as voorstelling_tijd,
                p.tarief
            FROM ticket t
            JOIN voorstelling v ON t.voorstelling_id = v.id
            JOIN prijs p ON t.prijs_id = p.id
            WHERE t.bezoeker_id = :id
            ORDER BY v.datum ASC
        ');
        $this->db->bind(':id', $bezoekerId);
        return $this->db->resultSet();
    }

    /**
     * ADMIN: Get tickets for a specific show with Customer Names
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
     * Standard CRUD: Get one ticket by ID
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM ticket WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Standard CRUD: Create a new ticket
     */
    public function create($data) {
        $this->db->query('INSERT INTO ticket (bezoeker_id, voorstelling_id, prijs_id, nummer, barcode, datum, tijd, status) VALUES (:bezoeker_id, :voorstelling_id, :prijs_id, :nummer, :barcode, :datum, :tijd, :status)');
        $this->db->bind(':bezoeker_id', $data['bezoeker_id']);
        $this->db->bind(':voorstelling_id', $data['voorstelling_id']);
        $this->db->bind(':prijs_id', $data['prijs_id']);
        $this->db->bind(':nummer', $data['nummer']);
        $this->db->bind(':barcode', $data['barcode']);
        $this->db->bind(':datum', $data['datum']);
        $this->db->bind(':tijd', $data['tijd']);
        $this->db->bind(':status', $data['status']);
        return $this->db->execute();
    }

    /**
     * Standard CRUD: Delete a ticket
     */
    public function delete($id) {
        $this->db->query('DELETE FROM ticket WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}