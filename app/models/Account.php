<?php

class Account
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Register a new account with a role
     * @param string $email Email/username
     * @param string $password Password
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param string $role Role name (default: 'bezoeker')
     * @return bool|object Returns user object on success, false on failure
     */
    public function register($email, $password, $firstName, $lastName, $role = 'bezoeker')
    {
        // Check if username already exists
        $this->db->query('SELECT * FROM gebruiker WHERE gebruikersnaam = :username');
        $this->db->bind(':username', $email, PDO::PARAM_STR);
        
        if ($this->db->single()) {
            return false; // Username already exists
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new gebruiker
        $this->db->query('INSERT INTO gebruiker (voornaam, achternaam, gebruikersnaam, wachtwoord, is_actief, is_ingelogd) 
                         VALUES (:voornaam, :achternaam, :gebruikersnaam, :wachtwoord, 1, 0)');
        $this->db->bind(':voornaam', $firstName, PDO::PARAM_STR);
        $this->db->bind(':achternaam', $lastName, PDO::PARAM_STR);
        $this->db->bind(':gebruikersnaam', $email, PDO::PARAM_STR);
        $this->db->bind(':wachtwoord', $hashedPassword, PDO::PARAM_STR);

        if (!$this->db->execute()) {
            return false;
        }

        // Get the newly created user ID
        $this->db->query('SELECT * FROM gebruiker WHERE gebruikersnaam = :username');
        $this->db->bind(':username', $email, PDO::PARAM_STR);
        $user = $this->db->single();

        if (!$user) {
            return false;
        }

        // Add role
        $this->db->query('INSERT INTO rol (gebruiker_id, naam, is_actief) VALUES (:gebruiker_id, :naam, 1)');
        $this->db->bind(':gebruiker_id', $user->id, PDO::PARAM_INT);
        $this->db->bind(':naam', $role, PDO::PARAM_STR);

        if (!$this->db->execute()) {
            return false;
        }

        // If role is 'bezoeker', create bezoeker record
        if ($role === 'bezoeker') {
            // Get highest relatienummer
            $this->db->query('SELECT MAX(relatienummer) as max_num FROM bezoeker');
            $result = $this->db->single();
            $nextNum = ($result && $result->max_num) ? $result->max_num + 1 : 1;

            $this->db->query('INSERT INTO bezoeker (gebruiker_id, relatienummer, is_actief) VALUES (:gebruiker_id, :relatienummer, 1)');
            $this->db->bind(':gebruiker_id', $user->id, PDO::PARAM_INT);
            $this->db->bind(':relatienummer', $nextNum, PDO::PARAM_INT);
            $this->db->execute();
        }

        return $user;
    }

    /**
     * Login with email/username and password
     */
    public function login($email, $password)
    {
        // Make sure we trim the email
        $email = strtolower(trim($email));
        
        $this->db->query('SELECT * FROM gebruiker WHERE LOWER(gebruikersnaam) = :username AND is_actief = 1');
        $this->db->bind(':username', $email, PDO::PARAM_STR);
        
        $user = $this->db->single();

        if ($user) {
            // Verify password
            if (password_verify($password, $user->wachtwoord)) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Get user by ID with roles
     */
    public function getById($id)
    {
        $this->db->query('SELECT * FROM gebruiker WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->single();
    }

    /**
     * Get user by username/email
     */
    public function getByEmail($email)
    {
        $this->db->query('SELECT * FROM gebruiker WHERE gebruikersnaam = :username');
        $this->db->bind(':username', $email, PDO::PARAM_STR);
        
        return $this->db->single();
    }

    /**
     * Check if username exists
     */
    public function emailExists($email)
    {
        $this->db->query('SELECT * FROM gebruiker WHERE gebruikersnaam = :username');
        $this->db->bind(':username', $email, PDO::PARAM_STR);
        
        return $this->db->single() ? true : false;
    }

    /**
     * Get user with roles
     */
    public function getWithRoles($id)
    {
        $this->db->query('SELECT g.*, GROUP_CONCAT(r.naam) as roles FROM gebruiker g 
                         LEFT JOIN rol r ON g.id = r.gebruiker_id 
                         WHERE g.id = :id GROUP BY g.id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->single();
    }

    /**
     * Get all users with their roles
     */
    public function getAllWithRoles()
    {
        $this->db->query('SELECT g.id, g.voornaam, g.achternaam, g.gebruikersnaam, 
                         g.is_actief, g.datum_aangemaakt, GROUP_CONCAT(r.naam) as roles 
                         FROM gebruiker g 
                         LEFT JOIN rol r ON g.id = r.gebruiker_id 
                         GROUP BY g.id 
                         ORDER BY g.datum_aangemaakt DESC');
        
        return $this->db->resultSet();
    }

    /**
     * Get primary role for user
     */
    public function getPrimaryRole($id)
    {
        $this->db->query('SELECT naam FROM rol WHERE gebruiker_id = :id AND is_actief = 1 LIMIT 1');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        $role = $this->db->single();
        return $role ? $role->naam : 'bezoeker';
    }
}
