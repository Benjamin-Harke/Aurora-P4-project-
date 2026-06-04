<?php

class PublicTickets extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Browse all available shows and performances
     */
    public function index() {
        $voorstellingModel = $this->model('Voorstelling');

        // Get all active performances
        $performances = $voorstellingModel->getAll() ?? [];

        // Filter by search query if provided
        $searchQuery = $_GET['search'] ?? '';
        if (!empty($searchQuery)) {
            $performances = array_filter($performances, function($perf) use ($searchQuery) {
                return stripos($perf->naam, $searchQuery) !== false;
            });
        }

        // Filter by date range
        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d', strtotime('+3 months'));
        $performances = array_filter($performances, function($perf) use ($startDate, $endDate) {
            return $perf->datum >= $startDate && $perf->datum <= $endDate;
        });

        // Sort by date (earliest first)
        usort($performances, function($a, $b) {
            return strcmp($a->datum, $b->datum);
        });

        $data = [
            'performances' => array_values($performances),
            'search_query' => $searchQuery,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'genres' => []
        ];

        $this->view('publictickets/index', $data);
    }

    /**
     * View details of a specific performance
     */
    public function performance($performanceId = null) {
        if (!$performanceId) {
            header('Location: ' . URLROOT . '/publictickets');
            return;
        }

        $voorstellingModel = $this->model('Voorstelling');
        $ticketModel = $this->model('Ticket');

        $performance = $voorstellingModel->getById($performanceId);
        
        if (!$performance) {
            header('Location: ' . URLROOT . '/publictickets');
            return;
        }

        // Get ticket stats for this performance
        $allTickets = $ticketModel->getByVoorstellingIdWithNames($performanceId) ?? [];
        $bookedCount = count($allTickets);
        $availableSeats = $performance->max_aantal_tickets - $bookedCount;

        $data = [
            'performance' => $performance,
            'available_seats' => $availableSeats,
            'is_sold_out' => $availableSeats <= 0,
            'total_seats' => $performance->max_aantal_tickets,
            'booked_seats' => $bookedCount
        ];

        $this->view('publictickets/performance', $data);
    }
}
