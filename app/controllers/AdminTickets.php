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
        // 1. Security Check
        if (!isset($_SESSION['accountId'])) {
            $_SESSION['error'] = 'Please log in to access admin features';
            header('Location: ' . URLROOT);
            exit;
        }

        $userRole = $_SESSION['rolle'] ?? 'bezoeker';
        if (strtolower($userRole) !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to access admin features';
            header('Location: ' . URLROOT . '/dashboard');
            exit;
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
            if($ticket->status == 'Gescand' || $ticket->status == 'gescand') {
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
            'tickets'         => $allTickets,
            'total_tickets'   => count($allTickets),
            'total_revenue'   => $totalRevenue,
            'scanned_count'   => $scannedCount,
            'analytics'       => $analyticsData,
            'total_shows'     => count($performances)
        ];

        $this->view('admintickets/dashboard', $data);
    }

    /**
     * INVENTORY: Capacity management
     */
    public function inventory() {
        if (!isset($_SESSION['accountId']) || strtolower($_SESSION['rolle'] ?? '') !== 'admin') {
            header('Location: ' . URLROOT);
            exit;
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

    /**
     * DELETE: Permanently remove a ticket
     */
    public function delete($id = null)
    {
        if (!$id || !isset($_SESSION['accountId'])) {
            header('Location: ' . URLROOT . '/admintickets/dashboard');
            exit;
        }

        if ($this->ticketModel->delete($id)) {
            $_SESSION['success'] = 'Ticket #' . $id . ' has been deleted by admin.';
        } else {
            $_SESSION['error'] = 'Failed to delete ticket.';
        }

        header('Location: ' . URLROOT . '/admintickets/dashboard');
        exit;
    }

    /**
     * VALIDATE TICKET: Display the validation tool interface
     */
    public function validateTicket() {
        if (!isset($_SESSION['accountId']) || strtolower($_SESSION['rolle'] ?? '') !== 'admin') {
            header('Location: ' . URLROOT);
            exit;
        }

        $this->view('admintickets/validate_ticket');
    }

    /**
     * VALIDATE TICKET API: Check if a ticket barcode is valid
     */
    public function validateTicketAPI() {
        $barcode = $_POST['code'] ?? $_GET['code'] ?? null;

        if (!$barcode) {
            echo json_encode(['valid' => false, 'message' => 'No barcode provided']);
            exit;
        }

        // Search database by barcode column instead of ID
        $ticket = $this->ticketModel->getByBarcode($barcode);

        if (!$ticket) {
            echo json_encode(['valid' => false, 'message' => 'Ticket not found']);
            exit;
        }

        $isValid = ($ticket->status === 'booked' || $ticket->status === 'reserved');
        $isScanned = strtolower($ticket->status) === 'gescand';
        $message = '';

        if ($isScanned) {
            $message = 'Ticket has already been scanned';
        } elseif ($ticket->status === 'cancelled') {
            $message = 'Ticket has been cancelled';
        } elseif (!$isValid) {
            $message = 'Invalid status: ' . $ticket->status;
        } else {
            $message = 'Ticket is valid and ready to scan';
        }

        echo json_encode([
            'valid' => $isValid,
            'scanned' => $isScanned,
            'message' => $message,
            'ticket_id' => $ticket->id,
            'barcode' => $ticket->barcode,
            'seat_number' => $ticket->nummer ?? 'N/A',
            'performance_name' => $ticket->voorstelling_naam ?? 'Unknown',
            'status' => $ticket->status
        ]);
        exit;
    }
}