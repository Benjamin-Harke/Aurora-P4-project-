<?php

class AdminTickets extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Admin dashboard with analytics, inventory, and sales data
     * Requires admin role
     */
    public function dashboard() {
        if (!$this->isAdmin()) {
            redirect('homepages');
        }

        $performanceModel = $this->model('Performance');
        $ticketModel = $this->model('Ticket');
        $showModel = $this->model('Show');

        // Get all upcoming performances
        $performances = $performanceModel->getUpcoming();

        // Build analytics data
        $analyticsData = [];
        $totalRevenue = 0;
        $totalSeats = 0;
        $totalBooked = 0;

        foreach ($performances as $perf) {
            $bookedCount = $ticketModel->getBookedCountByPerformanceId($perf->id);
            $availableCount = $ticketModel->getAvailableCountByPerformanceId($perf->id);
            
            $analyticsData[] = [
                'id' => $perf->id,
                'show_title' => $perf->show_title,
                'performance_date' => $perf->performance_date,
                'performance_time' => $perf->performance_time,
                'venue' => $perf->venue,
                'total_seats' => $perf->total_seats,
                'booked_seats' => $bookedCount,
                'available_seats' => $availableCount,
                'occupancy_rate' => $perf->total_seats > 0 ? round(($bookedCount / $perf->total_seats) * 100, 2) : 0,
                'capacity_status' => $this->getCapacityStatus($bookedCount, $perf->total_seats),
                'revenue' => $bookedCount * ($perf->price ?? 0)
            ];

            $totalRevenue += $analyticsData[count($analyticsData) - 1]['revenue'];
            $totalSeats += $perf->total_seats;
            $totalBooked += $bookedCount;
        }

        // Sort by date
        usort($analyticsData, function($a, $b) {
            return strtotime($a['performance_date']) <=> strtotime($b['performance_date']);
        });

        $data = [];
        $data['analytics'] = $analyticsData;
        $data['total_revenue'] = $totalRevenue;
        $data['total_seats'] = $totalSeats;
        $data['total_booked'] = $totalBooked;
        $data['occupancy_rate'] = $totalSeats > 0 ? round(($totalBooked / $totalSeats) * 100, 2) : 0;
        $data['total_shows'] = count(array_unique(array_column($analyticsData, 'show_title')));

        $this->view('admintickets/dashboard', $data);
    }

    /**
     * View inventory details for all performances
     */
    public function inventory() {
        if (!$this->isAdmin()) {
            redirect('homepages');
        }

        $performanceModel = $this->model('Performance');
        $ticketModel = $this->model('Ticket');

        $performances = $performanceModel->getAll();

        $inventoryData = [];
        foreach ($performances as $perf) {
            $bookedCount = $ticketModel->getBookedCountByPerformanceId($perf->id);
            $availableCount = $ticketModel->getAvailableCountByPerformanceId($perf->id);

            $inventoryData[] = [
                'performance' => $perf,
                'available_seats' => $availableCount,
                'booked_seats' => $bookedCount,
                'reserved_seats' => 0, // Can be expanded
                'capacity_percentage' => $perf->total_seats > 0 ? round(($bookedCount / $perf->total_seats) * 100, 2) : 0,
                'is_oversold' => $bookedCount > $perf->total_seats
            ];
        }

        $data = [];
        $data['inventory'] = $inventoryData;

        $this->view('admintickets/inventory', $data);
    }

    /**
     * View detailed ticket information for a specific performance
     */
    public function performanceDetails($performanceId = null) {
        if (!$this->isAdmin() || !$performanceId) {
            redirect('homepages');
        }

        $performanceModel = $this->model('Performance');
        $ticketModel = $this->model('Ticket');

        $performance = $performanceModel->getById($performanceId);
        if (!$performance) {
            redirect('admintickets/dashboard');
        }

        $tickets = $ticketModel->getByPerformanceId($performanceId);

        $data = [];
        $data['performance'] = $performance;
        $data['tickets'] = $tickets;
        $data['total_tickets'] = count($tickets);
        $data['booked_tickets'] = count(array_filter($tickets, fn($t) => $t->status === 'booked'));
        $data['available_tickets'] = count(array_filter($tickets, fn($t) => $t->status === 'available'));

        $this->view('admintickets/performance_details', $data);
    }

    /**
     * Search for a specific ticket by ID or user name
     */
    public function search() {
        if (!$this->isAdmin()) {
            redirect('homepages');
        }

        $ticketModel = $this->model('Ticket');
        $userModel = $this->model('User');

        $searchQuery = $_GET['q'] ?? '';
        $results = [];

        if (!empty($searchQuery)) {
            // Try to find by ticket ID or user info
            if (is_numeric($searchQuery)) {
                $ticket = $ticketModel->getById(intval($searchQuery));
                if ($ticket) {
                    $results[] = $ticket;
                }
            } else {
                // Search by user name (basic implementation)
                $users = $userModel->getAll();
                foreach ($users as $user) {
                    $fullName = $user->firstname . ' ' . $user->lastname;
                    if (stripos($fullName, $searchQuery) !== false) {
                        $userTickets = $ticketModel->getByUserId($user->id);
                        $results = array_merge($results, $userTickets);
                    }
                }
            }
        }

        $data = [];
        $data['search_query'] = $searchQuery;
        $data['results'] = $results;

        $this->view('admintickets/search', $data);
    }

    /**
     * Check if current user is admin
     */
    private function isAdmin() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $userModel = $this->model('User');
        return $userModel->isAdmin($_SESSION['user_id']);
    }

    /**
     * Get capacity status label
     */
    private function getCapacityStatus($booked, $total) {
        if ($total === 0) return 'unknown';
        $percentage = ($booked / $total) * 100;
        
        if ($percentage >= 100) return 'sold_out';
        if ($percentage >= 80) return 'nearly_full';
        if ($percentage >= 50) return 'half_full';
        return 'available';
    }
}
