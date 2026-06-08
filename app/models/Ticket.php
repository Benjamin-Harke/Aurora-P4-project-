<?php

class Ticket
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * ADMIN: Get all tickets with JOINs for the main dashboard
     */
    public function getAll()
    {
        $this->db->query("SELECT t.*, 
                                 v.naam as show_title, 
                                 CONCAT(g.voornaam, ' ', g.achternaam) as customer_name,
                                 p.tarief
                          FROM ticket t
                          JOIN voorstelling v ON t.voorstelling_id = v.id
                          JOIN bezoeker b ON t.bezoeker_id = b.id
                          JOIN gebruiker g ON b.gebruiker_id = g.id
                          JOIN prijs p ON t.prijs_id = p.id
                          ORDER BY t.datum_aangemaakt DESC");
        
        return $this->db->resultSet();
    }

    /**
     * USER: Get tickets for a specific visitor (My Tickets)
     */
    public function getByBezoekerIdWithNames($bezoekerId)
    {
        $this->db->query("SELECT t.*, 
                                 v.naam as voorstelling_naam, 
                                 v.datum as voorstelling_datum, 
                                 v.tijd as voorstelling_tijd,
                                 p.tarief
                          FROM ticket t
                          JOIN voorstelling v ON t.voorstelling_id = v.id
                          JOIN prijs p ON t.prijs_id = p.id
                          WHERE t.bezoeker_id = :id
                          ORDER BY v.datum ASC");
        
        $this->db->bind(':id', $bezoekerId);
        return $this->db->resultSet();
    }

    /**
     * SEAT SELECTION: Get taken seats for a performance
     */
    public function getTakenSeats($performanceId)
    {
        $this->db->query("SELECT nummer FROM ticket WHERE voorstelling_id = :id AND status != 'cancelled'");
        $this->db->bind(':id', $performanceId);
        $results = $this->db->resultSet();
        return array_column($results, 'nummer');
    }

    /**
     * SCANNING: Get stats for a performance
     */
    public function getStats($perfId)
    {
        $this->db->query("SELECT 
                            COUNT(*) as total, 
                            SUM(CASE WHEN status = 'Gescand' THEN 1 ELSE 0 END) as scanned 
                          FROM ticket WHERE voorstelling_id = :id");
        $this->db->bind(':id', $perfId);
        return $this->db->single();
    }

    /**
     * SCANNING: Verify and Mark as used
     */
    public function verifyAndScan($barcode, $perfId) {
        $this->db->query("SELECT t.*, p.tarief as prijs, 
                                 CONCAT(g.voornaam, ' ', g.achternaam) as owner_name
                          FROM ticket t
                          JOIN bezoeker b ON t.bezoeker_id = b.id
                          JOIN gebruiker g ON b.gebruiker_id = g.id
                          JOIN prijs p ON t.prijs_id = p.id
                          WHERE t.barcode = :barcode AND t.voorstelling_id = :perfId");
        
        $this->db->bind(':barcode', $barcode);
        $this->db->bind(':perfId', $perfId);
        $ticket = $this->db->single();

        if (!$ticket) return 'INVALID';
        if ($ticket->status === 'Gescand') return 'ALREADY_SCANNED';

        $this->db->query("UPDATE ticket SET status = 'Gescand' WHERE id = :id");
        $this->db->bind(':id', $ticket->id);
        
        if ($this->db->execute()) {
            $ticket->naam = $ticket->owner_name; 
            return $ticket;
        }
        return 'INVALID';
    }

    /**
     * CRUD: Get one ticket by ID (Full Details)
     */
    public function getById($id)
    {
        $this->db->query("SELECT t.*, 
                                 v.naam as voorstelling_naam, 
                                 v.datum as voorstelling_datum, 
                                 v.tijd as voorstelling_tijd,
                                 v.beschrijving as voorstelling_beschrijving,
                                 p.tarief
                          FROM ticket t
                          JOIN voorstelling v ON t.voorstelling_id = v.id
                          JOIN prijs p ON t.prijs_id = p.id
                          WHERE t.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * VALIDATION: Get ticket by barcode
     */
    public function getByBarcode($barcode) {
        $this->db->query("SELECT t.*, v.naam as voorstelling_naam 
                          FROM ticket t 
                          JOIN voorstelling v ON t.voorstelling_id = v.id 
                          WHERE t.barcode = :barcode");
        $this->db->bind(':barcode', $barcode);
        return $this->db->single();
    }

    /**
     * CRUD: Create a new ticket
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO ticket (bezoeker_id, voorstelling_id, prijs_id, nummer, barcode, datum, tijd, status) 
                          VALUES (:bezoeker_id, :voorstelling_id, :prijs_id, :nummer, :barcode, :datum, :tijd, :status)");
        
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
     * CRUD: Update status
     */
    public function updateStatus($id, $status)
    {
        $this->db->query("UPDATE ticket SET status = :status WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    /**
     * CRUD: Delete a ticket
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM ticket WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * PUBLIC: Get list of tickets for a show to count availability
     */
    public function getByVoorstellingIdWithNames($id) {
        $this->db->query('SELECT * FROM ticket WHERE voorstelling_id = :id AND status != "cancelled"');
        $this->db->bind(':id', $id);
        return $this->db->resultSet();
    }

    /**
     * HELPERS: Get all performances
     */
    public function getPerformances()
    {
        $this->db->query("SELECT * FROM voorstelling WHERE is_actief = 1 ORDER BY datum ASC");
        return $this->db->resultSet();
    }
}