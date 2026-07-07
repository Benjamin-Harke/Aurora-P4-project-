<?php

class Admintickets extends BaseController {
    private $ticketModel;
    private $voorstellingModel;
    private $bezoekerModel;
    private $prijsModel;

    public function __construct() {
        parent::__construct();
        $this->ticketModel      = $this->model('Ticket');
        $this->voorstellingModel = $this->model('Voorstelling');
        $this->bezoekerModel    = $this->model('Bezoeker');
        $this->prijsModel       = $this->model('Prijs');
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
     * CREATE TICKET: Admin form to add a new ticket
     * URL: /admintickets/create
     * GET  → show the form
     * POST → validate + save
     */
    public function create() {
        // --- Auth guard ---
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

        $errors  = [];
        $postData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $voorstellingId = trim($_POST['voorstelling_id'] ?? '');
            $bezoekerId     = trim($_POST['bezoeker_id'] ?? '');
            $stoelnummer    = (int) trim($_POST['stoelnummer'] ?? 0);
            $prijsId        = trim($_POST['prijs_id'] ?? '');

            // Keep values so the form re-fills after an error
            $postData = [
                'voorstelling_id' => $voorstellingId,
                'bezoeker_id'     => $bezoekerId,
                'stoelnummer'     => $stoelnummer,
                'prijs_id'        => $prijsId,
            ];

            // --- Basic required-field validation ---
            if (empty($voorstellingId)) $errors[] = 'Selecteer een voorstelling.';
            if (empty($bezoekerId))     $errors[] = 'Selecteer een bezoeker.';
            if ($stoelnummer < 1)       $errors[] = 'Voer een geldig stoelnummer in (minimaal 1).';
            if (empty($prijsId))        $errors[] = 'Selecteer een tarief.';

            if (empty($errors)) {
                // --- Check seat capacity ---
                $performance = $this->voorstellingModel->getById($voorstellingId);
                if ($performance && $stoelnummer > (int)$performance->max_aantal_tickets) {
                    $errors[] = 'Stoelnummer ' . $stoelnummer . ' bestaat niet voor deze voorstelling (max: ' . $performance->max_aantal_tickets . ').';
                }
            }

            if (empty($errors)) {
                // --- UNHAPPY SCENARIO: seat already taken ---
                $takenSeats = $this->ticketModel->getTakenSeats($voorstellingId);
                if (in_array($stoelnummer, $takenSeats)) {
                    $errors[] = 'De geselecteerde stoel is al geboekt voor deze voorstelling. Kies een beschikbare stoel om het ticket toe te voegen.';
                }
            }

            if (empty($errors)) {
                // --- HAPPY SCENARIO: create the ticket ---
                $performance   = $this->voorstellingModel->getById($voorstellingId);
                $barcode       = 'TKT-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));

                $ticketData = [
                    'bezoeker_id'     => $bezoekerId,
                    'voorstelling_id' => $voorstellingId,
                    'prijs_id'        => $prijsId,
                    'nummer'          => $stoelnummer,
                    'barcode'         => $barcode,
                    'datum'           => $performance->datum,
                    'tijd'            => $performance->tijd,
                    'status'          => 'Gereserveerd',
                ];

                if ($this->ticketModel->create($ticketData)) {
                    $_SESSION['success'] = 'Ticket succesvol toegevoegd aan het systeem!';
                    header('Location: ' . URLROOT . '/admintickets/dashboard');
                    exit;
                } else {
                    $errors[] = 'Er is een fout opgetreden bij het opslaan van het ticket. Probeer opnieuw.';
                }
            }
        }

        $data = [
            'performances' => $this->voorstellingModel->getAll(),
            'bezoekers'    => $this->bezoekerModel->getAllWithNames(),
            'prijzen'      => $this->prijsModel->getAll(),
            'errors'       => $errors,
            'post'         => $postData,
        ];

        $this->view('admintickets/create', $data);
    }

    /**
     * EDIT TICKET: Admin form to edit an existing ticket
     * URL: /admintickets/edit/[id]
     * GET  → show the form
     * POST → validate + update
     */
    public function edit($id = null) {
        // --- Auth guard ---
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

        // --- Existence check (Unhappy flow) ---
        if (!$id) {
            $_SESSION['error'] = 'Het ticket is niet meer beschikbaar.';
            header('Location: ' . URLROOT . '/admintickets/dashboard');
            exit;
        }

        $ticket = $this->ticketModel->getById($id);
        if (!$ticket) {
            $_SESSION['error'] = 'Het ticket is niet meer beschikbaar.';
            header('Location: ' . URLROOT . '/admintickets/dashboard');
            exit;
        }

        $errors  = [];
        $postData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Re-check existence in case it was deleted by another admin in the meantime
            $currentTicket = $this->ticketModel->getById($id);
            if (!$currentTicket) {
                $_SESSION['error'] = 'Het ticket is niet meer beschikbaar.';
                header('Location: ' . URLROOT . '/admintickets/dashboard');
                exit;
            }

            $voorstellingId = trim($_POST['voorstelling_id'] ?? '');
            $bezoekerId     = trim($_POST['bezoeker_id'] ?? '');
            $stoelnummer    = (int) trim($_POST['stoelnummer'] ?? 0);
            $prijsId        = trim($_POST['prijs_id'] ?? '');
            $status         = trim($_POST['status'] ?? '');

            // Keep values so the form re-fills after an error
            $postData = [
                'voorstelling_id' => $voorstellingId,
                'bezoeker_id'     => $bezoekerId,
                'stoelnummer'     => $stoelnummer,
                'prijs_id'        => $prijsId,
                'status'          => $status,
            ];

            // --- Basic required-field validation ---
            if (empty($voorstellingId)) $errors[] = 'Selecteer een voorstelling.';
            if (empty($bezoekerId))     $errors[] = 'Selecteer een bezoeker.';
            if ($stoelnummer < 1)       $errors[] = 'Voer een geldig stoelnummer in (minimaal 1).';
            if (empty($prijsId))        $errors[] = 'Selecteer een tarief.';
            if (empty($status))         $errors[] = 'Selecteer een status.';

            if (empty($errors)) {
                // --- Check seat capacity ---
                $performance = $this->voorstellingModel->getById($voorstellingId);
                if ($performance && $stoelnummer > (int)$performance->max_aantal_tickets) {
                    $errors[] = 'Stoelnummer ' . $stoelnummer . ' bestaat niet voor deze voorstelling (max: ' . $performance->max_aantal_tickets . ').';
                }
            }

            if (empty($errors)) {
                // --- Seat already taken check ---
                $takenSeats = $this->ticketModel->getTakenSeats($voorstellingId);
                
                // If it is the same performance and seat number as the ticket being edited, it is allowed
                $isSameTicketSeat = ($voorstellingId == $ticket->voorstelling_id && $stoelnummer == $ticket->nummer);
                
                if (in_array($stoelnummer, $takenSeats) && !$isSameTicketSeat) {
                    $errors[] = 'De geselecteerde stoel is al geboekt voor deze voorstelling. Kies een beschikbare stoel om het ticket toe te voegen.';
                }
            }

            if (empty($errors)) {
                // --- HAPPY SCENARIO: update the ticket ---
                $performance = $this->voorstellingModel->getById($voorstellingId);

                $ticketData = [
                    'id'              => $id,
                    'bezoeker_id'     => $bezoekerId,
                    'voorstelling_id' => $voorstellingId,
                    'prijs_id'        => $prijsId,
                    'nummer'          => $stoelnummer,
                    'datum'           => $performance->datum,
                    'tijd'            => $performance->tijd,
                    'status'          => $status,
                ];

                if ($this->ticketModel->update($ticketData)) {
                    $_SESSION['success'] = 'Ticket succesvol gewijzigd!';
                    header('Location: ' . URLROOT . '/admintickets/dashboard');
                    exit;
                } else {
                    $errors[] = 'Er is een fout opgetreden bij het opslaan van het ticket. Probeer opnieuw.';
                }
            }
        } else {
            // Initial GET request: fill postData from database
            $postData = [
                'voorstelling_id' => $ticket->voorstelling_id,
                'bezoeker_id'     => $ticket->bezoeker_id,
                'stoelnummer'     => $ticket->nummer,
                'prijs_id'        => $ticket->prijs_id,
                'status'          => $ticket->status,
            ];
        }

        $data = [
            'ticket'       => $ticket,
            'performances' => $this->voorstellingModel->getAll(),
            'bezoekers'    => $this->bezoekerModel->getAllWithNames(),
            'prijzen'      => $this->prijsModel->getAll(),
            'errors'       => $errors,
            'post'         => $postData,
        ];

        $this->view('admintickets/edit', $data);
    }

    /**
     * JSON ENDPOINT: Return seat info for a performance
     * URL: /admintickets/getSeats/[id]
     * Used by the JavaScript on the create-ticket form
     */
    public function getSeats($id = null) {
        header('Content-Type: application/json');
        if (!$id) {
            echo json_encode(['error' => 'No performance ID provided']);
            exit;
        }
        $performance = $this->voorstellingModel->getById($id);
        if (!$performance) {
            echo json_encode(['error' => 'Performance not found']);
            exit;
        }
        $taken = $this->ticketModel->getTakenSeats($id);
        echo json_encode([
            'total' => (int) $performance->max_aantal_tickets,
            'taken' => $taken,
        ]);
        exit;
    }

    /**
     * DELETE: Permanently remove a ticket
     */
    public function delete($id = null)
    {
        // --- Auth guard ---
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

        // --- Existence check (Unhappy flow) ---
        if (!$id) {
            $_SESSION['error'] = 'Het ticket kan niet verwijderd worden omdat het al niet meer aanwezig is.';
            header('Location: ' . URLROOT . '/admintickets/dashboard');
            exit;
        }

        $ticket = $this->ticketModel->getById($id);
        if (!$ticket) {
            $_SESSION['error'] = 'Het ticket kan niet verwijderd worden omdat het al niet meer aanwezig is.';
            header('Location: ' . URLROOT . '/admintickets/dashboard');
            exit;
        }

        // --- HAPPY SCENARIO: delete ticket ---
        if ($this->ticketModel->delete($id)) {
            $_SESSION['success'] = 'Ticket succesvol verwijderd.';
        } else {
            $_SESSION['error'] = 'Er is een fout opgetreden bij het verwijderen van het ticket.';
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