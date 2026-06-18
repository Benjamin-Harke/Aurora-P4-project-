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
     */
    public function mytickets() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please log in to view your tickets';
            header("Location: /test/index");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->gebruikerModel->getById($userId);
        
        if (!$user) {
            die("Error: User ID $userId exists in session but not in database.");
        }

        $user->firstname = $user->voornaam;
        $user->lastname = $user->achternaam;

        $bezoeker = $this->bezoekerModel->getByGebruikerId($userId);
        
        if (!$bezoeker) {
            $allTickets = [];
        } else {
            $allTickets = $this->ticketModel->getByBezoekerIdWithNames($bezoeker->id);
        }
        
        $upcomingTickets = [];
        $pastTickets = [];
        $today = date('Y-m-d');

        foreach ($allTickets as $ticket) {
            $ticket->show_title = $ticket->voorstelling_naam;
            $ticket->performance_date = $ticket->voorstelling_datum;
            $ticket->performance_time = $ticket->voorstelling_tijd;
            $ticket->price = $ticket->tarief;
            $ticket->seat_number = $ticket->nummer;
            $ticket->venue = "Main Hall";

            // Logic for expired/invalid tickets
            $isExpired = $ticket->voorstelling_datum < $today && $ticket->status !== 'Gescand';
            $ticket->is_invalid = $isExpired || ($ticket->status === 'invalid' || $ticket->status === 'cancelled');
            
            if ($ticket->is_invalid) {
                $pastTickets[] = $ticket;
            } elseif ($ticket->voorstelling_datum >= $today) {
                $upcomingTickets[] = $ticket;
            } else {
                $pastTickets[] = $ticket;
            }
        }

        $data = [
            'user' => $user,
            'tickets' => $allTickets,
            'upcomingTickets' => $upcomingTickets,
            'pastTickets' => $pastTickets,
            'has_tickets' => count($allTickets) > 0,
            'has_upcoming' => count($upcomingTickets) > 0,
            'has_past' => count($pastTickets) > 0
        ];

        $this->view('usertickets/mytickets', $data);
    }

    /**
     * VIEW TICKET: Detail view for a single ticket
     */
    public function viewTicket($id = null) {
        if (!$id || !isset($_SESSION['user_id'])) {
            redirect('usertickets/mytickets');
        }

        $ticket = $this->ticketModel->getById($id);
        $bezoeker = $this->bezoekerModel->getByGebruikerId($_SESSION['user_id']);

        if (!$ticket || $ticket->bezoeker_id != $bezoeker->id) {
            redirect('usertickets/mytickets');
        }

        $voorstellingModel = $this->model('Voorstelling');
        $voorstelling = $voorstellingModel->getById($ticket->voorstelling_id);

        $ticket->show_title = $voorstelling->naam ?? 'Unknown';
        $ticket->performance_date = $voorstelling->datum ?? '';
        $ticket->performance_time = $voorstelling->tijd ?? '';
        $ticket->genre_name = $voorstelling->genre ?? 'Theatre';
        $ticket->venue = "Main Hall";
        $ticket->price = $ticket->tarief ?? 0;
        $ticket->seat_number = $ticket->nummer ?? 'N/A';
        
        $this->view('usertickets/ticket_detail', ['ticket' => $ticket]);
    }

    /**
     * CANCEL TICKET: Allow a user to delete their own ticket
     */
    public function cancelTicket($id = null) {
        if (!$id || !isset($_SESSION['user_id'])) {
            redirect('usertickets/mytickets');
        }

        $ticket = $this->ticketModel->getById($id);
        $bezoeker = $this->bezoekerModel->getByGebruikerId($_SESSION['user_id']);

        if (!$ticket || $ticket->bezoeker_id != $bezoeker->id) {
            $_SESSION['error'] = 'You do not have permission to delete this ticket.';
            redirect('usertickets/mytickets');
        }

        if ($this->ticketModel->delete($id)) {
            $_SESSION['success'] = 'Ticket has been successfully cancelled.';
        } else {
            $_SESSION['error'] = 'Something went wrong.';
        }

        redirect('usertickets/mytickets');
    }

    /**
     * STEP 1: Show the seat selection page
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
     * STEP 2: Process the actual booking
     */
    public function confirmBooking() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $performanceId = $_POST['performance_id'];
            $seatNumber = $_POST['seat_number'];

            $takenSeats = $this->ticketModel->getTakenSeats($performanceId);
            if (in_array($seatNumber, $takenSeats)) {
                $_SESSION['error'] = 'Sorry, that seat was just taken!';
                redirect("usertickets/selectSeat/" . $performanceId);
                exit;
            }

            $bezoeker = $this->bezoekerModel->getByGebruikerId($_SESSION['user_id']);
            $performance = $this->model('Voorstelling')->getById($performanceId);

            $data = [
                'bezoeker_id'     => $bezoeker->id,
                'voorstelling_id' => $performanceId,
                'prijs_id'        => 1,
                'nummer'          => $seatNumber,
                'barcode'         => generateSecureBarcode(12),
                'datum'           => $performance->datum,
                'tijd'            => $performance->tijd,
                'status'          => 'booked'
            ];

            if ($this->ticketModel->create($data)) {
                $_SESSION['success'] = 'Seat ' . $seatNumber . ' booked successfully!';
                redirect("usertickets/mytickets");
            }
        }
    }

    /**
     * TEST SCENARIOS
     */
    public function testMarkInvalid($id) {
        $this->ticketModel->updateStatus($id, 'invalid');
        redirect('usertickets/mytickets');
    }

    public function testResetTicket($id) {
        $this->ticketModel->updateStatus($id, 'booked');
        redirect('usertickets/mytickets');
    }
}