<?php

class UserTickets extends BaseController {
    private $ticketModel;
    private $gebruikerModel;
    private $bezoekerModel;

    public function __construct() {
        parent::__construct();
        $this->ticketModel = $this->model('Ticket');
        $this->gebruikerModel = $this->model('Gebruiker');
        $this->bezoekerModel = $this->model('Bezoeker');
    }

    public function mytickets() {
        // 1. Session Check
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please log in to view your tickets';
            redirect('test/index');
        }

        $userId = $_SESSION['user_id'];
        
        // 2. Get the User and map to English keys for the View
        $user = $this->gebruikerModel->getById($userId);
        if ($user) {
            $user->firstname = $user->voornaam; // Map voornaam -> firstname
            $user->lastname = $user->achternaam; // Map achternaam -> lastname
        }

        // 3. Get the Visitor record
        $bezoeker = $this->bezoekerModel->getByGebruikerId($userId);
        
        // 4. Fetch and Sort Tickets
        $allTickets = $this->ticketModel->getByBezoekerIdWithNames($bezoeker->id);
        
        $upcomingTickets = [];
        $pastTickets = [];
        $today = date('Y-m-d');

        foreach ($allTickets as $ticket) {
            // Map the names for the View template
            $ticket->show_title = $ticket->voorstelling_naam;
            $ticket->performance_date = $ticket->voorstelling_datum;
            $ticket->performance_time = $ticket->voorstelling_tijd;
            $ticket->price = $ticket->tarief;

            // Sort into Upcoming or Past
            if ($ticket->voorstelling_datum >= $today) {
                $upcomingTickets[] = $ticket;
            } else {
                $pastTickets[] = $ticket;
            }
        }

        // 5. Fill ALL the keys your View is looking for
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
}