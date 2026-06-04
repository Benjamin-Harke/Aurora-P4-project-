<?php

class TicketScanning extends BaseController
{
    private $ticketModel;
    private $voorstellingModel;

    public function __construct()
    {
        parent::__construct();
        $this->ticketModel = $this->model('Ticket');
        $this->voorstellingModel = $this->model('Voorstelling');
    }

    /**
     * Display the ticket scanner interface
     */
    public function index()
    {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['accountId'])) {
            $_SESSION['error'] = 'Please log in to access scanning';
            header('Location: ' . URLROOT);
            return;
        }

        $userRole = $_SESSION['rolle'] ?? 'bezoeker';
        if (strtolower($userRole) !== 'admin') {
            $_SESSION['error'] = 'Only admins can access the ticket scanner';
            header('Location: ' . URLROOT . '/dashboard');
            return;
        }

        // Get all performances for selection
        $performances = $this->voorstellingModel->getAll() ?? [];

        $data = [
            'performances' => $performances,
            'selected_performance' => $_GET['performance_id'] ?? null
        ];

        $this->view('ticketscanning/index', $data);
    }

    /**
     * API endpoint to validate and scan a ticket
     */
    public function validate()
    {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['accountId'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $userRole = $_SESSION['rolle'] ?? 'bezoeker';
        if (strtolower($userRole) !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Only admins can scan tickets']);
            return;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $barcode = trim($input['barcode'] ?? '');
        $performanceId = intval($input['performance_id'] ?? 0);

        if (empty($barcode)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Barcode is required']);
            return;
        }

        if ($performanceId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Performance must be selected']);
            return;
        }

        // Find ticket by barcode
        $ticket = $this->ticketModel->getByBarcode($barcode);

        if (!$ticket) {
            echo json_encode([
                'success' => false,
                'message' => 'Ticket not found',
                'barcode' => $barcode
            ]);
            return;
        }

        // Verify ticket is for the selected performance
        if ($ticket->voorstelling_id != $performanceId) {
            echo json_encode([
                'success' => false,
                'message' => 'This ticket is not for the selected performance',
                'ticket_show' => $ticket->voorstelling_naam,
                'barcode' => $barcode
            ]);
            return;
        }

        // Check if ticket has already been scanned
        if (strtolower($ticket->status) === 'scanned' || strtolower($ticket->status) === 'used') {
            echo json_encode([
                'success' => false,
                'message' => 'Ticket has already been scanned',
                'ticket_owner' => $ticket->voornaam . ' ' . $ticket->achternaam,
                'barcode' => $barcode
            ]);
            return;
        }

        // Mark ticket as scanned
        $updateResult = $this->ticketModel->markAsScanned($ticket->id);

        if (!$updateResult) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update ticket status']);
            return;
        }

        // Success response
        echo json_encode([
            'success' => true,
            'message' => 'Ticket validated successfully',
            'ticket_id' => $ticket->id,
            'ticket_owner' => $ticket->voornaam . ' ' . $ticket->achternaam,
            'ticket_number' => $ticket->nummer,
            'ticket_price' => $ticket->tarief,
            'barcode' => $barcode
        ]);
    }

    /**
     * Get scanner statistics for a performance
     */
    public function stats()
    {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['accountId'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $userRole = $_SESSION['rolle'] ?? 'bezoeker';
        if (strtolower($userRole) !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Only admins can access stats']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $performanceId = intval($input['performance_id'] ?? 0);

        if ($performanceId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Performance ID is required']);
            return;
        }

        // Get performance details
        $performance = $this->voorstellingModel->getById($performanceId);
        if (!$performance) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Performance not found']);
            return;
        }

        // Get ticket stats for this performance
        $stats = $this->ticketModel->getStatsByPerformance($performanceId);

        echo json_encode([
            'success' => true,
            'performance_name' => $performance->naam,
            'performance_date' => $performance->datum,
            'performance_time' => $performance->tijd,
            'total_tickets' => $stats['total'] ?? 0,
            'scanned_tickets' => $stats['scanned'] ?? 0,
            'remaining_tickets' => ($stats['total'] ?? 0) - ($stats['scanned'] ?? 0)
        ]);
    }
}
