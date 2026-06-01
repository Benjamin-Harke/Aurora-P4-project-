<?php

class PublicTickets extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Browse all available shows and performances
     * Supports filtering by date range, genre, search, and sorting
     */
    public function index() {
        $showModel = $this->model('Show');
        $performanceModel = $this->model('Performance');
        $genreModel = $this->model('Genre');

        $data = [];
        $data['genres'] = $genreModel->getAll();
        
        // Get all upcoming performances with filter/search support
        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d', strtotime('+3 months'));
        $genreId = $_GET['genre_id'] ?? null;
        $searchQuery = $_GET['search'] ?? null;
        $sortBy = $_GET['sort'] ?? 'date'; // date, price_asc, price_desc

        // Determine which performances to fetch
        if (!empty($searchQuery)) {
            // Search by show title
            $shows = $showModel->searchByTitle($searchQuery);
            $performances = [];
            foreach ($shows as $show) {
                $perfs = $performanceModel->getByShowId($show->id);
                $performances = array_merge($performances, $perfs);
            }
        } elseif ($genreId) {
            // Filter by genre
            $performances = $performanceModel->getByGenreId($genreId);
        } else {
            // Get all upcoming performances in date range
            $performances = $performanceModel->getByDateRange($startDate, $endDate);
        }

        // Filter by date range if no search
        if (empty($searchQuery)) {
            $performances = array_filter($performances, function($perf) use ($startDate, $endDate) {
                return $perf->performance_date >= $startDate && $perf->performance_date <= $endDate;
            });
        }

        // Apply sorting
        $performances = $this->sortPerformances($performances, $sortBy);

        $data['performances'] = array_values($performances);
        $data['search_query'] = $searchQuery;
        $data['selected_genre'] = $genreId;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['sort_by'] = $sortBy;

        $this->view('publictickets/index', $data);
    }

    /**
     * View details of a specific performance
     */
    public function performance($performanceId = null) {
        if (!$performanceId) {
            redirect('publictickets');
        }

        $performanceModel = $this->model('Performance');
        $ticketModel = $this->model('Ticket');

        $performance = $performanceModel->getById($performanceId);
        
        if (!$performance) {
            redirect('publictickets');
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
