<?php

class Ticket
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getPerformances()
    {
        // Fix: Table name is 'voorstelling' based on your SQL script
        $this->db->query("SELECT * FROM voorstelling WHERE is_actief = 1 ORDER BY datum ASC");
        return $this->db->resultSet();
    }

    public function getStats($perfId)
    {
        // Fix: Table name is 'ticket', column is 'status'
        $this->db->query("SELECT 
                            COUNT(*) as total, 
                            SUM(CASE WHEN status = 'Scanned' THEN 1 ELSE 0 END) as scanned 
                          FROM ticket WHERE voorstelling_id = :id");
        $this->db->bind(':id', $perfId);
        return $this->db->single();
    }

    public function verifyAndScan($barcode, $perfId)
{
    // REMOVED "OR t.id = :id" for security. Now only checks the random barcode.
    $this->db->query("SELECT t.*, p.tarief as prijs, 
                             CONCAT(g.voornaam, ' ', g.achternaam) as owner_name
                      FROM ticket t
                      JOIN bezoeker b ON t.bezoeker_id = b.id
                      JOIN gebruiker g ON b.gebruiker_id = g.id
                      JOIN prijs p ON t.prijs_id = p.id
                      WHERE t.barcode = :barcode 
                      AND t.voorstelling_id = :perfId");
    
    $this->db->bind(':barcode', $barcode);
    $this->db->bind(':perfId', $perfId);
    
    $ticket = $this->db->single();

    if (!$ticket) {
        return 'INVALID';
    }

    if ($ticket->status === 'Scanned') {
        return 'ALREADY_SCANNED';
    }

    $this->db->query("UPDATE ticket SET status = 'Scanned' WHERE id = :id");
    $this->db->bind(':id', $ticket->id);
    
    if ($this->db->execute()) {
        $ticket->naam = $ticket->owner_name; 
        return $ticket;
    }
    
    return 'INVALID';
}

    /**
     * Get all tickets for a specific visitor with show and price details
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
                          WHERE t.bezoeker_id = :bezoeker_id
                          ORDER BY v.datum DESC");
        
        $this->db->bind(':bezoeker_id', $bezoekerId);
        return $this->db->resultSet();
    }

    /**
     * Get a single ticket by ID with full details (likely needed for the view page)
     */
    public function getTicketByIdWithNames($id)
    {
        $this->db->query("SELECT t.*, 
                                 v.naam as voorstelling_naam, 
                                 v.datum as voorstelling_datum, 
                                 v.tijd as voorstelling_tijd,
                                 v.beschrijving,
                                 p.tarief
                          FROM ticket t
                          JOIN voorstelling v ON t.voorstelling_id = v.id
                          JOIN prijs p ON t.prijs_id = p.id
                          WHERE t.id = :id");
        
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get a ticket by its ID
     * This is used by Usertickets->viewTicket()
     */
    public function getById($id)
    {
        // We join with voorstelling and prijs so the ticket view 
        // actually shows the name of the show and the cost.
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
     * Update the status of a ticket
     * Used for marking tickets as Scanned, Invalid, or Active
     */
    public function updateStatus($id, $status)
    {
        $this->db->query("UPDATE ticket SET status = :status WHERE id = :id");
        
        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get performance details by ID
     * Used by Publictickets controller to show the booking page for a show
     */
    /**
     * Get ALL tickets for a specific performance
     * Returns an array so the controller can count them
     */
    public function getByVoorstellingIdWithNames($id)
    {
        // We query the TICKET table to see how many have been sold
        $this->db->query("SELECT * FROM ticket WHERE voorstelling_id = :id");
        $this->db->bind(':id', $id);
        
        // resultSet() returns an ARRAY, which allows count() to work
        return $this->db->resultSet();
    }

    /**
     * INSERT a new ticket into the database
     */
    public function create($data)
    {
        // Based on your SQL schema: table is 'ticket'
        $this->db->query("INSERT INTO ticket (
                            bezoeker_id, 
                            voorstelling_id, 
                            prijs_id, 
                            nummer, 
                            barcode, 
                            datum, 
                            tijd, 
                            status
                          ) VALUES (
                            :bezoeker_id, 
                            :voorstelling_id, 
                            :prijs_id, 
                            :nummer, 
                            :barcode, 
                            :datum, 
                            :tijd, 
                            :status
                          )");
        
        // Bind the parameters
        $this->db->bind(':bezoeker_id', $data['bezoeker_id']);
        $this->db->bind(':voorstelling_id', $data['voorstelling_id']);
        $this->db->bind(':prijs_id', $data['prijs_id']);
        $this->db->bind(':nummer', $data['nummer']);
        $this->db->bind(':barcode', $data['barcode']);
        $this->db->bind(':datum', $data['datum']);
        $this->db->bind(':tijd', $data['tijd']);
        $this->db->bind(':status', $data['status']);

        // Execute the query
        return $this->db->execute();
    }

    /**
     * Get a list of all taken seat numbers for a performance
     */
    public function getTakenSeats($performanceId)
    {
        $this->db->query("SELECT nummer FROM ticket WHERE voorstelling_id = :id AND status != 'cancelled'");
        $this->db->bind(':id', $performanceId);
        $results = $this->db->resultSet();
        
        // Return just an array of numbers: [1, 5, 12...]
        return array_column($results, 'nummer');
    }
}