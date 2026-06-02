<?php

class Account
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Register a new account
     */
    public function register($email, $password, $firstName, $lastName)
    {
        // Check if email already exists
        $this->db->query('SELECT * FROM Accounts WHERE Email = :email');
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        
        if ($this->db->single()) {
            return false; // Email already exists
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new account
        $this->db->query('INSERT INTO Accounts (Email, Password, FirstName, LastName) VALUES (:email, :password, :firstName, :lastName)');
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        $this->db->bind(':password', $hashedPassword, PDO::PARAM_STR);
        $this->db->bind(':firstName', $firstName, PDO::PARAM_STR);
        $this->db->bind(':lastName', $lastName, PDO::PARAM_STR);

        return $this->db->execute();
    }

    /**
     * Login with email and password
     */
    public function login($email, $password)
    {
        // Make sure we trim the email
        $email = strtolower(trim($email));
        
        $this->db->query('SELECT * FROM Accounts WHERE LOWER(Email) = :email AND IsActive = 1');
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        
        $account = $this->db->single();

        if ($account) {
            // Verify password
            if (password_verify($password, $account->Password)) {
                return $account;
            }
        }

        return false;
    }

    /**
     * Get account by ID
     */
    public function getById($id)
    {
        $this->db->query('SELECT * FROM Accounts WHERE Id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->single();
    }

    /**
     * Get account by email
     */
    public function getByEmail($email)
    {
        $this->db->query('SELECT * FROM Accounts WHERE Email = :email');
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        
        return $this->db->single();
    }

    /**
     * Check if email exists
     */
    public function emailExists($email)
    {
        $this->db->query('SELECT * FROM Accounts WHERE Email = :email');
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        
        return $this->db->single() ? true : false;
    }
}
