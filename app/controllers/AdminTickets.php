<?php

class Admintickets extends BaseController {
    private $ticketModel;
    private $voorstellingModel;

    public function __construct() {
        parent::__construct();
        // Load the Dutch ERD Models
        $this->ticketModel = $this->model('Ticket');
        $this->voorstellingModel = $this->model('Voorstelling');
    }

    /**
     * Redirects /admintickets to /admintickets/dashboard
     */
    public function index() {
        $this->dashboard();
    }

    /**
     * DASHBOARD: The main "Ticket Overzicht"
     */
    public function dashboard() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['accountId'])) {
            $_SESSION['error'] = 'Please log in to access admin features';
            header('Location: ' . URLROOT);
            return;
        }

        $userRole = $_SESSION['rolle'] ?? 'bezoeker';
        if (strtolower($userRole) !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to access admin features';
            header('Location: ' . URLROOT . '/dashboard');
            return;
        }

        $performances = $this->voorstellingModel->getAll();
        $allTickets = $this->ticketModel->getAll(); // Uses the JOIN query in model

        $analyticsData = [];
        $totalRevenue = 0;
        $totalBooked = 0;
        $totalCapacity = 0;

        foreach ($performances as $perf) {
            $bookedForThis = 0;
            $revenueForThis = 0;
            
            foreach($allTickets as $ticket) {
                if($ticket->voorstelling_id == $perf->id) {
                    $bookedForThis++;
                    $revenueForThis += $ticket->tarief;
                }
            }

            $totalSeats = $perf->max_aantal_tickets;
            $availableSeats = $totalSeats - $bookedForThis;
            $rowOccupancy = ($totalSeats > 0) ? round(($bookedForThis / $totalSeats) * 100, 2) : 0;

            // MAP DUTCH DB -> ENGLISH VIEW KEYS
            $analyticsData[] = [
                'id' => $perf->id,
                'show_title' => $perf->naam,
                'performance_date' => $perf->datum,
                'performance_time' => $perf->tijd,
                'venue' => 'Main Stage',
                'total_seats' => $totalSeats,
                'booked_seats' => $bookedForThis,
                'available_seats' => $availableSeats,
                'occupancy_rate' => $rowOccupancy,
                'revenue' => $revenueForThis
            ];

            $totalRevenue += $revenueForThis;
            $totalBooked += $bookedForThis;
            $totalCapacity += $totalSeats;
        }

        $data = [
            'analytics' => $analyticsData,
            'total_revenue' => $totalRevenue,
            'total_booked' => $totalBooked,
            'occupancy_rate' => $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100, 2) : 0,
            'total_shows' => count($performances)
        ];

        $this->view('admintickets/dashboard', $data);
    }

    /**
     * INVENTORY: Capacity management
     */
    public function inventory() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['accountId'])) {
            $_SESSION['error'] = 'Please log in to access admin features';
            header('Location: ' . URLROOT);
            return;
        }

        $userRole = $_SESSION['rolle'] ?? 'bezoeker';
        if (strtolower($userRole) !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to access admin features';
            header('Location: ' . URLROOT . '/dashboard');
            return;
        }

        $performances = $this->voorstellingModel->getAll();
        $allTickets = $this->ticketModel->getAll();

        $inventoryData = [];
        foreach ($performances as $perf) {
            $bookedCount = 0;
            foreach($allTickets as $t) {
                if($t->voorstelling_id == $perf->id) $bookedCount++;
            }

            $totalSeats = $perf->max_aantal_tickets;
            $availableSeats = $totalSeats - $bookedCount;
            $percentage = ($totalSeats > 0) ? ($bookedCount / $totalSeats) * 100 : 0;

            // Cast as object so the view's -> syntax works
            $inventoryData[] = (object) [
                'performance' => $perf,
                'available_seats' => $availableSeats,
                'booked_seats' => $bookedCount,
                'capacity_percentage' => $percentage,
                'is_oversold' => $bookedCount > $totalSeats
            ];
        }

        $data = ['inventory' => $inventoryData];
        $this->view('admintickets/inventory', $data);
    }

   public function performanceDetails($id = null) {
        if (!$id) {
            redirect('admintickets/dashboard');
        }

        $performance = $this->voorstellingModel->getById($id);
        if (!$performance) {
            redirect('admintickets/dashboard');
        }

        $tickets = $this->ticketModel->getByVoorstellingIdWithNames($id);

        /**
         * 1. MAP PERFORMANCE HEADER
         */
        $performance->show_title = $performance->naam;
        $performance->performance_date = $performance->datum;
        $performance->performance_time = $performance->tijd;
        $performance->venue = "Main Hall"; 
        $performance->genre = "Musical";   
        $performance->status = $performance->beschikbaarheid;
        $performance->total_seats = $performance->max_aantal_tickets;

        /**
         * 2. MAP EVERY TICKET FOR THE TABLE
         */
        foreach ($tickets as $ticket) {
            $ticket->seat_number = $ticket->nummer;        
            $ticket->price = $ticket->tarief;              
            
            // Map the name fields
            $ticket->firstname = $ticket->voornaam;
            $ticket->infix = $ticket->tussenvoegsel;
            $ticket->lastname = $ticket->achternaam;
            
            // FIX: Map the user_id property that line 116 is looking for
            // We use the bezoeker_id or gebruiker_id here
            $ticket->user_id = $ticket->bezoeker_id; 
            
            $ticket->qr_code = $ticket->barcode;
            $ticket->booking_date = $ticket->datum_aangemaakt; 
        }

        /**
         * 3. CALCULATE STATS
         */
        $bookedCount = count($tickets);
        $totalSeats = (int) $performance->max_aantal_tickets;
        if ($totalSeats < 1) { $totalSeats = 1; } 
        $availableSeats = $totalSeats - $bookedCount;

        $data = [
            'performance' => $performance,
            'tickets' => $tickets,
            'total_tickets' => $totalSeats,
            'booked_tickets' => $bookedCount,
            'available_tickets' => $availableSeats
        ];

        $this->view('admintickets/performance_details', $data);
    }

    /**
     * VALIDATE TICKET FORM: Display the validation tool interface
     * URL: /admintickets/validateTicket (GET - display form)
     */
    public function validateTicket() {
        // Check admin role
        if (!isset($_SESSION['accountId']) || strtolower($_SESSION['rolle'] ?? 'bezoeker') !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to access this feature';
            header('Location: ' . URLROOT . '/dashboard');
            return;
        }

        // If this is a POST request, handle the validation and return JSON
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateTicketAPI();
            return;
        }

        // Display the validation tool form
        $this->view('admintickets/validate_ticket');
    }

    /**
     * VALIDATE TICKET API: Check if a ticket code is valid
     * URL: /admintickets/validateTicket (POST)
     * POST parameter: code (e.g., "00001")
     */
    private function validateTicketAPI() {
        // Get validation code from POST or GET
        $code = $_POST['code'] ?? $_GET['code'] ?? null;

        if (!$code) {
            echo json_encode(['valid' => false, 'message' => 'No code provided']);
            exit;
        }

        // Extract ticket ID from validation code (remove leading zeros)
        $ticketId = (int) $code;

        // Get ticket by ID
        $ticket = $this->ticketModel->getById($ticketId);

        if (!$ticket) {
            echo json_encode(['valid' => false, 'message' => 'Ticket not found']);
            exit;
        }

        // Check ticket status
        $isValid = ($ticket->status === 'booked' || $ticket->status === 'reserved');
        $isScanned = $ticket->status === 'Gescand' || $ticket->status === 'gescand';
        $message = '';

        if ($isScanned) {
            $message = 'Ticket has already been scanned';
        } elseif ($ticket->status === 'cancelled') {
            $message = 'Ticket has been cancelled';
        } elseif ($ticket->status === 'invalid') {
            $message = 'Ticket is invalid';
        } elseif (!$isValid) {
            $message = 'Ticket status: ' . ucfirst($ticket->status);
        } else {
            $message = 'Ticket is valid and ready to scan';
        }

        // Get performance info
        $performance = $this->voorstellingModel->getById($ticket->voorstelling_id);
        $performanceDate = $performance->datum ?? 'N/A';
        $performanceName = $performance->naam ?? 'Unknown';

        echo json_encode([
            'valid' => $isValid,
            'scanned' => $isScanned,
            'message' => $message,
            'ticket_id' => $ticket->id,
            'validation_code' => str_pad($ticket->id, 5, '0', STR_PAD_LEFT),
            'seat_number' => $ticket->nummer ?? 'N/A',
            'performance_name' => $performanceName,
            'performance_date' => $performanceDate,
            'status' => $ticket->status
        ]);
        exit;
    }
}