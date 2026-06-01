<?php

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get user by ID
     */
    public function getById($id) {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * Get all users
     */
    public function getAll() {
        $this->db->query("SELECT id, firstname, infix, lastname, email, role, is_active, created_at FROM users ORDER BY lastname, firstname");
        return $this->db->resultSet();
    }

    /**
     * Get all admin users
     */
    public function getAdmins() {
        $this->db->query("SELECT * FROM users WHERE role = 'admin' AND is_active = TRUE");
        return $this->db->resultSet();
    }

    /**
     * Create new user
     */
    public function create($data) {
        $this->db->query("
            INSERT INTO users (firstname, infix, lastname, email, password, phone, role, is_active)
            VALUES (:firstname, :infix, :lastname, :email, :password, :phone, :role, :is_active)
        ");
        $this->db->bind(':firstname', $data['firstname']);
        $this->db->bind(':infix', $data['infix'] ?? null);
        $this->db->bind(':lastname', $data['lastname']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_BCRYPT));
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':role', $data['role'] ?? 'customer');
        $this->db->bind(':is_active', $data['is_active'] ?? true, PDO::PARAM_BOOL);
        return $this->db->execute();
    }

    /**
     * Update user
     */
    public function update($id, $data) {
        $this->db->query("
            UPDATE users 
            SET firstname = :firstname, infix = :infix, lastname = :lastname, 
                email = :email, phone = :phone
            WHERE id = :id
        ");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':firstname', $data['firstname']);
        $this->db->bind(':infix', $data['infix'] ?? null);
        $this->db->bind(':lastname', $data['lastname']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        return $this->db->execute();
    }

    /**
     * Update user role (admin only)
     */
    public function updateRole($id, $role) {
        $this->db->query("UPDATE users SET role = :role WHERE id = :id");
        $this->db->bind(':role', $role);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Update user active status
     */
    public function updateStatus($id, $isActive) {
        $this->db->query("UPDATE users SET is_active = :is_active WHERE id = :id");
        $this->db->bind(':is_active', $isActive, PDO::PARAM_BOOL);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin($id) {
        $user = $this->getById($id);
        return $user && $user->role === 'admin';
    }
}
