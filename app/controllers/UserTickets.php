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
            $_SESSION['error'] = 'Please log in to view your tickets';
            redirect('homepages');
        }

        $ticketModel = $this->model('Ticket');
        $userModel = $this->model('User');
        
        $userId = $_SESSION['user_id'];
        $user = $userModel->getById($userId);
        
        // Get upcoming and past tickets separately
        $upcomingTickets = $ticketModel->getUpcomingByUserId($userId);
        $pastTickets = $ticketModel->getPastByUserId($userId);
        
        // Combine for display (upcoming first)
        $tickets = array_merge($upcomingTickets, $pastTickets);

        $data = [];
        $data['user'] = $user;
        $data['tickets'] = $tickets;
        $data['upcomingTickets'] = $upcomingTickets;
        $data['pastTickets'] = $pastTickets;
        $data['has_tickets'] = count($tickets) > 0;
        $data['has_upcoming'] = count($upcomingTickets) > 0;
        $data['has_past'] = count($pastTickets) > 0;

        $this->view('usertickets/mytickets', $data);
    }

    /**
     * View details of a specific ticket
     */
    public function viewTicket($ticketId = null) {
        if (!isset($_SESSION['user_id']) || !$ticketId) {
            $_SESSION['error'] = 'Invalid ticket request';
            redirect('homepages');
        }

        // Validate ticket ID is numeric
        if (!is_numeric($ticketId)) {
            $_SESSION['error'] = 'Invalid ticket ID format';
            redirect('usertickets/mytickets');
        }

        $ticketModel = $this->model('Ticket');
        $ticket = $ticketModel->getById(intval($ticketId));

        // Verify ticket exists and belongs to logged-in user
        if (!$ticket || $ticket->user_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Ticket not found or access denied';
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
            $_SESSION['error'] = 'Invalid ticket request';
            redirect('homepages');
        }

        // Validate ticket ID is numeric
        if (!is_numeric($ticketId)) {
            $_SESSION['error'] = 'Invalid ticket ID format';
            redirect('usertickets/mytickets');
        }

        $ticketModel = $this->model('Ticket');
        $ticket = $ticketModel->getById(intval($ticketId));

        // Verify ticket belongs to logged-in user
        if (!$ticket || $ticket->user_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Ticket not found or access denied';
            redirect('usertickets/mytickets');
        }

        // TODO: Implement PDF generation or download logic
        // For now, redirect back with message
        $_SESSION['info'] = 'PDF download feature coming soon. You can print your ticket using the Print button.';
        redirect('usertickets/viewTicket/' . $ticketId);
    }

    /**
     * Cancel a booking
     */
    public function cancelTicket($ticketId = null) {
        if (!isset($_SESSION['user_id']) || !$ticketId) {
            $_SESSION['error'] = 'Invalid ticket request';
            redirect('homepages');
        }

        // Validate ticket ID is numeric
        if (!is_numeric($ticketId)) {
            $_SESSION['error'] = 'Invalid ticket ID format';
            redirect('usertickets/mytickets');
        }

        $ticketModel = $this->model('Ticket');
        $ticket = $ticketModel->getById(intval($ticketId));

        // Verify ticket belongs to logged-in user
        if (!$ticket || $ticket->user_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Ticket not found or access denied';
            redirect('usertickets/mytickets');
        }

        // Only allow cancellation if performance is in the future and ticket is booked
        $performanceDateTime = strtotime($ticket->performance_date . ' ' . $ticket->performance_time);
        if ($performanceDateTime <= time()) {
            $_SESSION['error'] = 'Cannot cancel tickets for past performances';
            redirect('usertickets/mytickets');
        }

        if ($ticket->status !== 'booked') {
            $_SESSION['error'] = 'This ticket cannot be cancelled. Only booked tickets can be cancelled.';
            redirect('usertickets/mytickets');
        }

        // Cancel the ticket
        if ($ticketModel->cancel(intval($ticketId))) {
            $_SESSION['success'] = 'Ticket cancelled successfully';
        } else {
            $_SESSION['error'] = 'Failed to cancel ticket. Please try again.';
        }

        redirect('usertickets/mytickets');
    }
}
