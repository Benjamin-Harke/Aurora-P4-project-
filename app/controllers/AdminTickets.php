<?php

class AdminTickets extends BaseController {
    private $ticketModel;
    private $voorstellingModel;

    public function __construct() {
        parent::__construct();
        $this->ticketModel = $this->model('Ticket');
        $this->voorstellingModel = $this->model('Voorstelling');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $performances = $this->voorstellingModel->getAll();
        $allTickets = $this->ticketModel->getAll();

        $analyticsData = [];
        $totalRevenue = 0;
        $totalBooked = 0;
        $totalCapacity = 0;

        foreach ($performances as $perf) {
            $bookedForThis = 0;
            $revenueForThis = 0;
            
            foreach($allTickets as $ticket) {
                if($ticket->voorstelling_id == $perf->id) {
                    $bookedForThis++;
                    $revenueForThis += $ticket->tarief;
                }
            }

            // Calculate row-specific values
            $totalSeats = $perf->max_aantal_tickets;
            $availableSeats = $totalSeats - $bookedForThis;
            $rowOccupancy = ($totalSeats > 0) ? round(($bookedForThis / $totalSeats) * 100, 2) : 0;

            // MAP DATABSE TO VIEW (Including the missing keys)
            $analyticsData[] = [
                'id' => $perf->id,
                'show_title' => $perf->naam,
                'performance_date' => $perf->datum,
                'performance_time' => $perf->tijd,
                'venue' => 'Main Stage',
                'total_seats' => $totalSeats,
                'booked_seats' => $bookedForThis,
                'available_seats' => $availableSeats, // Added this
                'occupancy_rate' => $rowOccupancy,     // Added this
                'revenue' => $revenueForThis
            ];

            $totalRevenue += $revenueForThis;
            $totalBooked += $bookedForThis;
            $totalCapacity += $totalSeats;
        }

        $data = [
            'analytics' => $analyticsData,
            'total_revenue' => $totalRevenue,
            'total_booked' => $totalBooked,
            'occupancy_rate' => $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100, 2) : 0,
            'total_shows' => count($performances)
        ];

        $this->view('admintickets/dashboard', $data);
    }

    public function inventory() {
        $data = ['inventory' => $this->voorstellingModel->getAll()];
        $this->view('admintickets/inventory', $data);
    }
}