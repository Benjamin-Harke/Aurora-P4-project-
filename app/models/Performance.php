<?php

class Performance {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all performances
     */
    public function getAll() {
        $this->db->query("
            SELECT p.*, s.title as show_title, g.name as genre_name
            FROM performances p
            JOIN shows s ON p.show_id = s.id
            LEFT JOIN genres g ON s.genre_id = g.id
            WHERE p.status != 'cancelled'
            ORDER BY p.performance_date ASC, p.performance_time ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Get performance by ID
     */
    public function getById($id) {
        $this->db->query("
            SELECT p.*, s.title as show_title, s.description as show_description, g.name as genre_name
            FROM performances p
            JOIN shows s ON p.show_id = s.id
            LEFT JOIN genres g ON s.genre_id = g.id
            WHERE p.id = :id
        ");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Get performances by show ID
     */
    public function getByShowId($showId) {
        $this->db->query("
            SELECT p.*, s.title as show_title
            FROM performances p
            JOIN shows s ON p.show_id = s.id
            WHERE p.show_id = :show_id AND p.status != 'cancelled'
            ORDER BY p.performance_date ASC, p.performance_time ASC
        ");
        $this->db->bind(':show_id', $showId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Get upcoming performances (from today onwards)
     */
    public function getUpcoming() {
        $this->db->query("
            SELECT p.*, s.title as show_title, g.name as genre_name
            FROM performances p
            JOIN shows s ON p.show_id = s.id
            LEFT JOIN genres g ON s.genre_id = g.id
            WHERE p.performance_date >= CURDATE() 
              AND p.status != 'cancelled'
              AND s.status != 'archived'
            ORDER BY p.performance_date ASC, p.performance_time ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Get performances by date range
     */
    public function getByDateRange($startDate, $endDate) {
        $this->db->query("
            SELECT p.*, s.title as show_title, g.name as genre_name
            FROM performances p
            JOIN shows s ON p.show_id = s.id
            LEFT JOIN genres g ON s.genre_id = g.id
            WHERE p.performance_date BETWEEN :start_date AND :end_date
              AND p.status != 'cancelled'
              AND s.status != 'archived'
            ORDER BY p.performance_date ASC, p.performance_time ASC
        ");
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->resultSet();
    }

    /**
     * Get performances by genre with upcoming shows
     */
    public function getByGenreId($genreId) {
        $this->db->query("
            SELECT p.*, s.title as show_title, g.name as genre_name
            FROM performances p
            JOIN shows s ON p.show_id = s.id
            LEFT JOIN genres g ON s.genre_id = g.id
            WHERE s.genre_id = :genre_id 
              AND p.performance_date >= CURDATE()
              AND p.status != 'cancelled'
              AND s.status != 'archived'
            ORDER BY p.performance_date ASC, p.performance_time ASC
        ");
        $this->db->bind(':genre_id', $genreId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Create new performance
     */
    public function create($data) {
        $this->db->query("
            INSERT INTO performances 
            (show_id, venue, performance_date, performance_time, total_seats, available_seats, price, status) 
            VALUES (:show_id, :venue, :date, :time, :total_seats, :available_seats, :price, :status)
        ");
        $this->db->bind(':show_id', $data['show_id'], PDO::PARAM_INT);
        $this->db->bind(':venue', $data['venue']);
        $this->db->bind(':date', $data['performance_date']);
        $this->db->bind(':time', $data['performance_time']);
        $this->db->bind(':total_seats', $data['total_seats'], PDO::PARAM_INT);
        $this->db->bind(':available_seats', $data['available_seats'], PDO::PARAM_INT);
        $this->db->bind(':price', $data['price'] ?? 0);
        $this->db->bind(':status', $data['status'] ?? 'on_sale');
        return $this->db->execute();
    }

    /**
     * Update available seats count
     */
    public function updateAvailableSeats($id, $count) {
        $this->db->query("UPDATE performances SET available_seats = :count WHERE id = :id");
        $this->db->bind(':count', $count, PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Update performance status
     */
    public function updateStatus($id, $status) {
        $this->db->query("UPDATE performances SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Update performance price
     */
    public function updatePrice($id, $price) {
        $this->db->query("UPDATE performances SET price = :price WHERE id = :id");
        $this->db->bind(':price', $price);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}
