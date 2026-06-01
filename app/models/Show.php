<?php

class Show {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all shows with genre info
     */
    public function getAll() {
        $this->db->query("
            SELECT s.*, g.name as genre_name 
            FROM shows s 
            LEFT JOIN genres g ON s.genre_id = g.id 
            WHERE s.status != 'archived'
            ORDER BY s.created_at DESC
        ");
        return $this->db->resultSet();
    }

    /**
     * Get show by ID with genre
     */
    public function getById($id) {
        $this->db->query("
            SELECT s.*, g.name as genre_name 
            FROM shows s 
            LEFT JOIN genres g ON s.genre_id = g.id 
            WHERE s.id = :id
        ");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Search shows by title (case-insensitive)
     */
    public function searchByTitle($title) {
        $this->db->query("
            SELECT s.*, g.name as genre_name 
            FROM shows s 
            LEFT JOIN genres g ON s.genre_id = g.id 
            WHERE s.title LIKE :title AND s.status != 'archived'
            ORDER BY s.title ASC
        ");
        $this->db->bind(':title', '%' . $title . '%');
        return $this->db->resultSet();
    }

    /**
     * Get shows by genre
     */
    public function getByGenreId($genreId) {
        $this->db->query("
            SELECT s.*, g.name as genre_name 
            FROM shows s 
            LEFT JOIN genres g ON s.genre_id = g.id 
            WHERE s.genre_id = :genre_id AND s.status != 'archived'
            ORDER BY s.title ASC
        ");
        $this->db->bind(':genre_id', $genreId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Get shows with upcoming performances
     */
    public function getWithUpcomingPerformances() {
        $this->db->query("
            SELECT DISTINCT s.*, g.name as genre_name
            FROM shows s
            LEFT JOIN genres g ON s.genre_id = g.id
            INNER JOIN performances p ON s.id = p.show_id
            WHERE s.status != 'archived' 
              AND p.performance_date >= CURDATE()
            ORDER BY s.title ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Create new show
     */
    public function create($data) {
        $this->db->query("
            INSERT INTO shows (title, description, genre_id, status, image_url) 
            VALUES (:title, :description, :genre_id, :status, :image_url)
        ");
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':genre_id', $data['genre_id'], PDO::PARAM_INT);
        $this->db->bind(':status', $data['status'] ?? 'on_sale');
        $this->db->bind(':image_url', $data['image_url'] ?? null);
        return $this->db->execute();
    }

    /**
     * Update show status
     */
    public function updateStatus($id, $status) {
        $this->db->query("UPDATE shows SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}
