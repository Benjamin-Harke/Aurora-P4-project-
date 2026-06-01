<?php

class UserTickets extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * View user's purchased tickets
     * Requires user to be logged in
     */
    public function myTickets() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            redirect('homepages');
        }

        $ticketModel = $this->model('Ticket');
        $userModel = $this->model('User');
        
        $userId = $_SESSION['user_id'];
        $user = $userModel->getById($userId);
        $tickets = $ticketModel->getByUserId($userId);

        $data = [];
        $data['user'] = $user;
        $data['tickets'] = $tickets;
        $data['has_tickets'] = count($tickets) > 0;

        $this->view('usertickets/mytickets', $data);
    }

    /**
     * View details of a specific ticket
     */
    public function viewTicket($ticketId = null) {
        if (!isset($_SESSION['user_id']) || !$ticketId) {
            redirect('homepages');
        }

        $ticketModel = $this->model('Ticket');
        $ticket = $ticketModel->getById($ticketId);

        // Verify ticket belongs to logged-in user
        if (!$ticket || $ticket->user_id != $_SESSION['user_id']) {
            redirect('usertickets/mytickets');
        }

        $data = [];
        $data['ticket'] = $ticket;

        $this->view('usertickets/ticket_detail', $data);
    }

    /**
     * Download or print ticket (PDF generation can be added later)
     */
    public function downloadTicket($ticketId = null) {
        if (!isset($_SESSION['user_id']) || !$ticketId) {
            redirect('homepages');
        }

        $ticketModel = $this->model('Ticket');
        $ticket = $ticketModel->getById($ticketId);

        // Verify ticket belongs to logged-in user
        if (!$ticket || $ticket->user_id != $_SESSION['user_id']) {
            redirect('usertickets/mytickets');
        }

        // TODO: Implement PDF generation or download logic
        // For now, redirect back with message
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'PDF download feature coming soon']);
    }

    /**
     * Cancel a booking
     */
    public function cancelTicket($ticketId = null) {
        if (!isset($_SESSION['user_id']) || !$ticketId) {
            redirect('homepages');
        }

        $ticketModel = $this->model('Ticket');
        $ticket = $ticketModel->getById($ticketId);

        // Verify ticket belongs to logged-in user
        if (!$ticket || $ticket->user_id != $_SESSION['user_id']) {
            redirect('usertickets/mytickets');
        }

        // Only allow cancellation if performance is in the future
        if (strtotime($ticket->performance_date . ' ' . $ticket->performance_time) <= time()) {
            $_SESSION['error'] = 'Cannot cancel tickets for past performances';
            redirect('usertickets/mytickets');
        }

        // Cancel the ticket
        if ($ticketModel->cancel($ticketId)) {
            $_SESSION['success'] = 'Ticket cancelled successfully';
        } else {
            $_SESSION['error'] = 'Failed to cancel ticket';
        }

        redirect('usertickets/mytickets');
    }
}
