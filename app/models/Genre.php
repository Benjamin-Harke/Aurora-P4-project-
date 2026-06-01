<?php

class Genre {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all genres
     */
    public function getAll() {
        $this->db->query("SELECT * FROM genres ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * Get genre by ID
     */
    public function getById($id) {
        $this->db->query("SELECT * FROM genres WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Get genre by name
     */
    public function getByName($name) {
        $this->db->query("SELECT * FROM genres WHERE name = :name");
        $this->db->bind(':name', $name);
        return $this->db->single();
    }

    /**
     * Create new genre
     */
    public function create($data) {
        $this->db->query("INSERT INTO genres (name, description) VALUES (:name, :description)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);
        return $this->db->execute();
    }
}
