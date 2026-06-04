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

        $availableCount = $ticketModel->getAvailableCountByPerformanceId($performanceId);

        $data = [];
        $data['performance'] = $performance;
        $data['available_seats'] = $availableCount;
        $data['is_sold_out'] = $availableCount === 0;

        $this->view('publictickets/performance', $data);
    }

    /**
     * Sort performances by specified criteria
     */
    private function sortPerformances($performances, $sortBy) {
        usort($performances, function($a, $b) use ($sortBy) {
            switch ($sortBy) {
                case 'price_asc':
                    return floatval($a->price ?? 0) <=> floatval($b->price ?? 0);
                case 'price_desc':
                    return floatval($b->price ?? 0) <=> floatval($a->price ?? 0);
                case 'popularity': // Could be enhanced with view counts
                    return 0; // Placeholder
                case 'date':
                default:
                    $dateA = strtotime($a->performance_date . ' ' . $a->performance_time);
                    $dateB = strtotime($b->performance_date . ' ' . $b->performance_time);
                    return $dateA <=> $dateB;
            }
        });
        return $performances;
    }
}
