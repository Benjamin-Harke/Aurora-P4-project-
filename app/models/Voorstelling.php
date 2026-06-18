<?php

class Voorstelling {
    private $db;

    public $id;
    public $medewerker_id;
    public $naam;
    public $beschrijving;
    public $datum;
    public $tijd;
    public $max_aantal_tickets;
    public $beschikbaarheid;
    public $is_actief;
    public $opmerking;
    public $datum_aangemaakt;
    public $datum_gewijzigd;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all Voorstelling records.
     * @return array An array of Voorstelling objects.
     */
    public function getAll() {
        $this->db->query('SELECT * FROM voorstelling');
        return $this->db->resultSet();
    }

    /**
     * Get a single Voorstelling record by ID.
     * @param int $id The ID of the voorstelling.
     * @return object|null The Voorstelling object or null if not found.
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM voorstelling WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get Voorstelling records by Medewerker ID.
     * @param int $medewerker_id The ID of the related medewerker.
     * @return array An array of Voorstelling objects.
     */
    public function getByMedewerkerId($medewerker_id) {
        $this->db->query('SELECT * FROM voorstelling WHERE medewerker_id = :medewerker_id');
        $this->db->bind(':medewerker_id', $medewerker_id);
        return $this->db->resultSet();
    }

    /**
     * Update an existing Voorstelling record.
     * @return bool True on success, false on failure.
     */
    public function update() {
        $this->db->query('UPDATE voorstelling SET medewerker_id = :medewerker_id, naam = :naam, beschrijving = :beschrijving, datum = :datum, tijd = :tijd, max_aantal_tickets = :max_aantal_tickets, beschikbaarheid = :beschikbaarheid, is_actief = :is_actief, opmerking = :opmerking WHERE id = :id');
        $this->db->bind(':id', $this->id);
        $this->db->bind(':medewerker_id', $this->medewerker_id);
        $this->db->bind(':naam', $this->naam);
        $this->db->bind(':beschrijving', $this->beschrijving);
        $this->db->bind(':datum', $this->datum);
        $this->db->bind(':tijd', $this->tijd);
        $this->db->bind(':max_aantal_tickets', $this->max_aantal_tickets);
        $this->db->bind(':beschikbaarheid', $this->beschikbaarheid);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Delete a Voorstelling record by ID.
     * @param int $id The ID of the voorstelling to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM voorstelling WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Relationship methods
    public function getMedewerker() {
        $medewerkerModel = new Medewerker();
        return $medewerkerModel->getById($this->medewerker_id);
    }

    public function getTickets() {
        $ticketModel = new Ticket();
        return $ticketModel->getByVoorstellingId($this->id);
    }

    public function create($data)
    {
        // We use 1 directly in the query for is_actief to avoid PDO BIT conversion issues
        $this->db->query("INSERT INTO voorstelling (
                            medewerker_id, 
                            naam, 
                            beschrijving, 
                            datum, 
                            tijd, 
                            max_aantal_tickets, 
                            beschikbaarheid, 
                            is_actief
                        ) VALUES (
                            :medewerker_id, 
                            :naam, 
                            :beschrijving, 
                            :datum, 
                            :tijd, 
                            :max_aantal_tickets, 
                            :beschikbaarheid, 
                            1
                        )");

        // Bind the parameters
        $this->db->bind(':medewerker_id', $data['medewerker_id']);
        $this->db->bind(':naam', $data['naam']);
        $this->db->bind(':beschrijving', $data['beschrijving']);
        $this->db->bind(':datum', $data['datum']);
        $this->db->bind(':tijd', $data['tijd']);
        $this->db->bind(':max_aantal_tickets', $data['max_aantal_tickets']);
        
        // This MUST be a string (VARCHAR)
        $this->db->bind(':beschikbaarheid', 'Zichtbaar'); 

        // Execute the query
        return $this->db->execute();
    }

}