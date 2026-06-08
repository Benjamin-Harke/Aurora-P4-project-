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
    /**
     * DASHBOARD: The main "Ticket Overzicht"
     */
    public function dashboard() {
        // 1. Security Check
        if (!isset($_SESSION['accountId'])) {
            $_SESSION['error'] = 'Please log in to access admin features';
            header('Location: ' . URLROOT);
            return;
        }

        // 2. Fetch Data from Models
        $performances = $this->voorstellingModel->getAll();
        $allTickets = $this->ticketModel->getAll(); 

        // 3. Calculate Global Stats for the Top Cards
        $totalRevenue = 0;
        $scannedCount = 0;
        foreach($allTickets as $ticket) {
            $totalRevenue += $ticket->tarief;
            // Check for Dutch status from your DB
            if($ticket->status == 'Gescand') {
                $scannedCount++;
            }
        }

        // 4. Calculate Analytics (per show) for your internal logic
        $analyticsData = [];
        foreach ($performances as $perf) {
            $bookedForThis = 0;
            foreach($allTickets as $ticket) {
                if($ticket->voorstelling_id == $perf->id) $bookedForThis++;
            }
            $analyticsData[] = [
                'id' => $perf->id,
                'show_title' => $perf->naam,
                'booked_seats' => $bookedForThis,
                'total_seats' => $perf->max_aantal_tickets
            ];
        }

        // 5. Prepare Data - Names here MUST match the View
        $data = [
            'tickets'         => $allTickets,             // Matches line 62 in View
            'total_tickets'   => count($allTickets),      // Matches line 19 in View
            'total_revenue'   => $totalRevenue,           // Matches View
            'scanned_count'   => $scannedCount,           // Matches line 35 in View
            'analytics'       => $analyticsData,
            'total_shows'     => count($performances)
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
     * Allow an admin to delete any ticket from the dashboard
     */
    public function delete($id = null)
    {
        // Simple security: check if logged in (you could add a role check here)
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/test/index');
            exit;
        }

        if ($this->ticketModel->delete($id)) {
            $_SESSION['success'] = 'Ticket #' . $id . ' has been deleted by admin.';
        } else {
            $_SESSION['error'] = 'Failed to delete ticket.';
        }

        // Redirect back to the admin dashboard
        header('Location: ' . URLROOT . '/admintickets/dashboard');
        exit;
    }
}