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

            // Sort into Upcoming or Past categories
            if ($ticket->voorstelling_datum >= $today) {
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

        // Map data for the detailed view
        $voorstelling = $this->model('Voorstelling')->getById($ticket->voorstelling_id);
        $ticket->show_title = $voorstelling->naam;
        $ticket->qr_code = $ticket->barcode;

        $this->view('usertickets/ticket_detail', ['ticket' => $ticket]);
    }

    /**
     * Allow a user to cancel/delete their own ticket
     */
    public function cancelTicket($id = null)
    {
        if (!$id || !isset($_SESSION['user_id'])) {
            redirect('usertickets/mytickets');
        }

        // 1. Get the ticket and the current visitor ID
        $ticket = $this->ticketModel->getById($id);
        $bezoeker = $this->bezoekerModel->getByGebruikerId($_SESSION['user_id']);

        // 2. SECURITY: Check if the ticket actually belongs to the logged-in user
        if (!$ticket || $ticket->bezoeker_id != $bezoeker->id) {
            $_SESSION['error'] = 'You do not have permission to delete this ticket.';
            redirect('usertickets/mytickets');
        }

        // 3. Delete the ticket
        if ($this->ticketModel->delete($id)) {
            $_SESSION['success'] = 'Ticket has been successfully cancelled.';
        } else {
            $_SESSION['error'] = 'Something went wrong. Please try again.';
        }

        redirect('usertickets/mytickets');
    }
}