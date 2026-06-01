<?php

class Ticket {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all tickets for a performance
     */
    public function getByPerformanceId($performanceId) {
        $this->db->query("
            SELECT t.*, u.firstname, u.lastname, u.infix
            FROM tickets t
            LEFT JOIN users u ON t.user_id = u.id
            WHERE t.performance_id = :performance_id
            ORDER BY t.seat_number ASC
        ");
        $this->db->bind(':performance_id', $performanceId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Get available tickets for a performance
     */
    public function getAvailableByPerformanceId($performanceId) {
        $this->db->query("
            SELECT t.*
            FROM tickets t
            WHERE t.performance_id = :performance_id AND t.status = 'available'
            ORDER BY t.seat_number ASC
        ");
        $this->db->bind(':performance_id', $performanceId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Get single ticket by ID
     */
    public function getById($id) {
        $this->db->query("
            SELECT t.*, u.firstname, u.lastname, u.infix,
                   s.title as show_title, p.venue, p.performance_date, p.performance_time
            FROM tickets t
            LEFT JOIN users u ON t.user_id = u.id
            JOIN performances p ON t.performance_id = p.id
            JOIN shows s ON p.show_id = s.id
            WHERE t.id = :id
        ");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Get tickets by user ID (purchased tickets)
     */
    public function getByUserId($userId) {
        $this->db->query("
            SELECT t.*, s.title as show_title, p.venue, p.performance_date, p.performance_time,
                   g.name as genre_name
            FROM tickets t
            JOIN performances p ON t.performance_id = p.id
            JOIN shows s ON p.show_id = s.id
            LEFT JOIN genres g ON s.genre_id = g.id
            WHERE t.user_id = :user_id AND t.status IN ('booked', 'reserved')
            ORDER BY p.performance_date DESC
        ");
        $this->db->bind(':user_id', $userId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Get count of available tickets by performance
     */
    public function getAvailableCountByPerformanceId($performanceId) {
        $this->db->query("
            SELECT COUNT(*) as count
            FROM tickets
            WHERE performance_id = :performance_id AND status = 'available'
        ");
        $this->db->bind(':performance_id', $performanceId, PDO::PARAM_INT);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    /**
     * Get count of booked tickets by performance
     */
    public function getBookedCountByPerformanceId($performanceId) {
        $this->db->query("
            SELECT COUNT(*) as count
            FROM tickets
            WHERE performance_id = :performance_id AND status = 'booked'
        ");
        $this->db->bind(':performance_id', $performanceId, PDO::PARAM_INT);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    /**
     * Create tickets for a performance (bulk insert for all seats)
     */
    public function createForPerformance($performanceId, $seatCount, $pricePerSeat) {
        $this->db->query("
            INSERT INTO tickets (performance_id, seat_number, price, status)
            VALUES (:performance_id, :seat_number, :price, 'available')
        ");

        for ($i = 1; $i <= $seatCount; $i++) {
            $this->db->bind(':performance_id', $performanceId, PDO::PARAM_INT);
            $this->db->bind(':seat_number', 'A' . $i);
            $this->db->bind(':price', $pricePerSeat);
            if (!$this->db->execute()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Book a ticket for a user
     */
    public function book($ticketId, $userId) {
        $this->db->query("
            UPDATE tickets 
            SET status = 'booked', user_id = :user_id, booking_date = NOW()
            WHERE id = :ticket_id AND status = 'available'
        ");
        $this->db->bind(':ticket_id', $ticketId, PDO::PARAM_INT);
        $this->db->bind(':user_id', $userId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Cancel a ticket booking
     */
    public function cancel($ticketId) {
        $this->db->query("
            UPDATE tickets 
            SET status = 'cancelled', user_id = NULL, booking_date = NULL
            WHERE id = :ticket_id
        ");
        $this->db->bind(':ticket_id', $ticketId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Get ticket by QR code
     */
    public function getByQRCode($qrCode) {
        $this->db->query("
            SELECT t.*, s.title as show_title, p.venue, p.performance_date, p.performance_time,
                   u.firstname, u.lastname
            FROM tickets t
            JOIN performances p ON t.performance_id = p.id
            JOIN shows s ON p.show_id = s.id
            LEFT JOIN users u ON t.user_id = u.id
            WHERE t.qr_code = :qr_code
        ");
        $this->db->bind(':qr_code', $qrCode);
        return $this->db->single();
    }
}
