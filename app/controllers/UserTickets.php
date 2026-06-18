<?php

class Usertickets extends BaseController {
    private $ticketModel;
    private $gebruikerModel;
    private $bezoekerModel;

    public function __construct() {
        parent::__construct();
        // Load the Dutch ERD Models
        $this->ticketModel = $this->model('Ticket');
        $this->gebruikerModel = $this->model('Gebruiker');
        $this->bezoekerModel = $this->model('Bezoeker');
    }

    /**
     * Default method redirects to mytickets
     */
    public function index() {
        $this->mytickets();
    }

    /**
     * MY TICKETS: The personal overview for a logged-in user
     * URL: /usertickets/mytickets
     */
    public function mytickets() {
        // 1. Session Security Check
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please log in to view your tickets';
            // Redirect to your test switcher for easy testing
            header("Location: /test/index");
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // 2. Fetch the User (Gebruiker)
        $user = $this->gebruikerModel->getById($userId);
        
        if (!$user) {
            die("Error: User ID $userId exists in session but not in 'gebruiker' table. Reset your DB data.");
        }

        // Map names for the "Welcome, Jane" message in the view
        $user->firstname = $user->voornaam;
        $user->lastname = $user->achternaam;

        // 3. Find the Visitor (Bezoeker) record for this user
        $bezoeker = $this->bezoekerModel->getByGebruikerId($userId);
        
        if (!$bezoeker) {
            // If the user exists but has no visitor record, they can't have tickets
            $allTickets = [];
        } else {
            // 4. Get the tickets for this specific visitor
            $allTickets = $this->ticketModel->getByBezoekerIdWithNames($bezoeker->id);
        }
        
        $upcomingTickets = [];
        $pastTickets = [];
        $today = date('Y-m-d');

        // 5. DATA MAPPING: Translate DB Dutch to View English
        foreach ($allTickets as $ticket) {
            $ticket->show_title = $ticket->voorstelling_naam;
            $ticket->performance_date = $ticket->voorstelling_datum;
            $ticket->performance_time = $ticket->voorstelling_tijd;
            $ticket->price = $ticket->tarief;
            $ticket->seat_number = $ticket->nummer;
            $ticket->venue = "Main Hall"; // Placeholder

            // Validate ticket status (UNHAPPY FLOW 1: Check if expired)
            $today = date('Y-m-d');
            $isExpired = $ticket->voorstelling_datum < $today && $ticket->status !== 'gescand';
            $ticket->is_expired = $isExpired;
            $ticket->is_invalid = $isExpired || ($ticket->status === 'invalid' || $ticket->status === 'cancelled');
            $ticket->expiry_message = '';
            
            if ($isExpired && $ticket->status !== 'gescand') {
                $ticket->expiry_message = 'This ticket has expired and can no longer be used.';
            } elseif ($ticket->status === 'invalid') {
                $ticket->expiry_message = 'This ticket is invalid and cannot be used.';
            } elseif ($ticket->status === 'cancelled') {
                $ticket->expiry_message = 'This ticket has been cancelled.';
            }

            // Sort into Upcoming, Past, or Invalid categories
            if ($ticket->is_invalid) {
                // Invalid tickets are treated as past
                $pastTickets[] = $ticket;
            } elseif ($ticket->voorstelling_datum >= $today) {
                $upcomingTickets[] = $ticket;
            } else {
                $pastTickets[] = $ticket;
            }
        }

        // 6. Build Data array with every key the view expects
        $data = [
            'user' => $user,
            'tickets' => $allTickets,
            'upcomingTickets' => $upcomingTickets,
            'pastTickets' => $pastTickets,
            'has_tickets' => count($allTickets) > 0,
            'has_upcoming' => count($upcomingTickets) > 0,
            'has_past' => count($pastTickets) > 0
        ];

        // Load the view folder 'usertickets' and file 'mytickets'
        $this->view('usertickets/mytickets', $data);
    }

    /**
     * TEST SCENARIO 1: Mark ticket as expired/invalid for testing
     * URL: /usertickets/testMarkInvalid/[ticketId]
     */
    public function testMarkInvalid($ticketId = null) {
        if (!$ticketId || !isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Invalid test request';
            redirect('usertickets/mytickets');
        }

        // Load ticket and verify ownership
        $ticket = $this->ticketModel->getById($ticketId);
        $bezoeker = $this->bezoekerModel->getByGebruikerId($_SESSION['user_id']);

        if (!$ticket || $ticket->bezoeker_id != $bezoeker->id) {
            $_SESSION['error'] = 'Ticket not found or does not belong to you';
            redirect('usertickets/mytickets');
        }

        // Update ticket status to invalid
        $this->ticketModel->updateStatus($ticketId, 'invalid');
        $_SESSION['success'] = 'Test: Ticket marked as invalid for testing unhappy scenario';
        redirect('usertickets/mytickets');
    }

    /**
     * TEST SCENARIO 2: Reset ticket status back to normal
     * URL: /usertickets/testResetTicket/[ticketId]
     */
    public function testResetTicket($ticketId = null) {
        if (!$ticketId || !isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Invalid test request';
            redirect('usertickets/mytickets');
        }

        // Load ticket and verify ownership
        $ticket = $this->ticketModel->getById($ticketId);
        $bezoeker = $this->bezoekerModel->getByGebruikerId($_SESSION['user_id']);

        if (!$ticket || $ticket->bezoeker_id != $bezoeker->id) {
            $_SESSION['error'] = 'Ticket not found or does not belong to you';
            redirect('usertickets/mytickets');
        }

        // Reset ticket status back to booked
        $this->ticketModel->updateStatus($ticketId, 'booked');
        $_SESSION['success'] = 'Test: Ticket reset to booked status';
        redirect('usertickets/mytickets');
    }

    /**
     * VIEW TICKET: Detail view for a single ticket
     * URL: /usertickets/viewTicket/1
     */
    public function viewTicket($id = null) {
        if (!$id || !isset($_SESSION['user_id'])) {
            redirect('usertickets/mytickets');
        }

        $ticket = $this->ticketModel->getById($id);
        $bezoeker = $this->bezoekerModel->getByGebruikerId($_SESSION['user_id']);

        // Security: Ensure this ticket belongs to the visitor currently "logged in"
        if (!$ticket || $ticket->bezoeker_id != $bezoeker->id) {
            redirect('usertickets/mytickets');
        }

        // Get performance details
        $voorstellingModel = $this->model('Voorstelling');
        $voorstelling = $voorstellingModel->getById($ticket->voorstelling_id);

        // Map data for the detailed view with all required properties
        $ticket->show_title = $voorstelling->naam ?? 'Unknown';
        $ticket->performance_date = $voorstelling->datum ?? '';
        $ticket->performance_time = $voorstelling->tijd ?? '';
        $ticket->genre_name = $voorstelling->genre ?? 'Unknown';
        $ticket->venue = "Main Hall"; // Placeholder - can be expanded with venue table
        $ticket->price = $ticket->tarief ?? 0;
        $ticket->seat_number = $ticket->nummer ?? 'N/A';
        $ticket->qr_code = $ticket->barcode ?? '';
        
        // Generate validation code: Ticket ID padded with zeros (e.g., 00001)
        $ticket->validation_code = str_pad($ticket->id, 5, '0', STR_PAD_LEFT);

        $this->view('usertickets/ticket_detail', ['ticket' => $ticket]);
    }

    /**
     * BOOK TICKET: Create a new ticket for the logged-in visitor
     * URL: /usertickets/book/[performanceId]
     */
    /**
     * Step 1: Show the seat selection page
     */
    public function selectSeat($performanceId) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /test/index");
            exit;
        }

        $performance = $this->model('Voorstelling')->getById($performanceId);
        $takenSeats = $this->ticketModel->getTakenSeats($performanceId);

        $data = [
            'performance' => $performance,
            'takenSeats' => $takenSeats,
            'totalSeats' => $performance->max_aantal_tickets
        ];

        $this->view('usertickets/select_seat', $data);
    }

    /**
     * Step 2: Process the actual booking
     */
    public function confirmBooking() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $performanceId = $_POST['performance_id'];
            $seatNumber = $_POST['seat_number'];

            // 1. Double check: Is the seat still available?
            $takenSeats = $this->ticketModel->getTakenSeats($performanceId);
            if (in_array($seatNumber, $takenSeats)) {
                $_SESSION['error'] = 'Sorry, that seat was just taken! Please choose another.';
                header("Location: /usertickets/selectSeat/" . $performanceId);
                exit;
            }

            $bezoeker = $this->bezoekerModel->getByGebruikerId($_SESSION['user_id']);
            $performance = $this->model('Voorstelling')->getById($performanceId);

            $data = [
                'bezoeker_id'     => $bezoeker->id,
                'voorstelling_id' => $performanceId,
                'prijs_id'        => 1,
                'nummer'          => $seatNumber, // THE SELECTED SEAT
                'barcode'         => generateSecureBarcode(12),
                'datum'           => $performance->datum,
                'tijd'            => $performance->tijd,
                'status'          => 'booked'
            ];

            if ($this->ticketModel->create($data)) {
                $_SESSION['success'] = 'Seat ' . $seatNumber . ' booked successfully!';
                header("Location: /usertickets/mytickets");
            }
        }
    }
}