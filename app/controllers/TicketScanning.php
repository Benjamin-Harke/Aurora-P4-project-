<?php

class Ticketscanning extends BaseController
{
    private $ticketModel;

    public function __construct()
    {
        // Load the Ticket Model
        $this->ticketModel = $this->model('Ticket');
    }

    /**
     * Loads the main scanner page
     */
    public function index()
    {
        $performances = $this->ticketModel->getPerformances();

        $data = [
            'performances' => $performances
        ];

        $this->view('ticketscanning/index', $data);
    }

    /**
     * AJAX Endpoint: Get stats for a performance
     */
    public function stats()
    {
        $json = file_get_contents('php://input');
        $request = json_decode($json);

        if (isset($request->performance_id)) {
            $stats = $this->ticketModel->getStats($request->performance_id);
            
            echo json_encode([
                'success' => true,
                'total_tickets' => $stats->total,
                'scanned_tickets' => $stats->scanned,
                'remaining_tickets' => ($stats->total - $stats->scanned)
            ]);
        }
    }

    /**
     * AJAX Endpoint: Validate and scan a barcode
     */
    public function validate()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if (isset($data->barcode) && isset($data->performance_id)) {
            $result = $this->ticketModel->verifyAndScan($data->barcode, $data->performance_id);

            if ($result === 'ALREADY_SCANNED') {
                echo json_encode(['success' => false, 'message' => 'Ticket has already been used!']);
            } elseif ($result === 'INVALID') {
                echo json_encode(['success' => false, 'message' => 'Invalid barcode for this performance']);
            } else {
                // $result contains the ticket/user info
                echo json_encode([
                    'success' => true,
                    'message' => 'Access Granted',
                    'ticket_owner' => $result->naam, // This matches the $ticket->naam we set in the model
                    'ticket_number' => $result->barcode,
                    'ticket_price' => $result->prijs
                ]);
            }
        }
    }
}