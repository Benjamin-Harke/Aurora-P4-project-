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
        $this->db->query('SELECT g.id, g.voornaam, g.tussenvoegsel, g.achternaam, g.gebruikersnaam, 
                         g.is_actief, g.datum_aangemaakt, GROUP_CONCAT(r.naam) as roles, c.email 
                         FROM gebruiker g 
                         LEFT JOIN rol r ON g.id = r.gebruiker_id 
                         LEFT JOIN contact c ON g.id = c.gebruiker_id
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

    /**
     * Check if an email address is already in use (as gebruikersnaam or in contact table).
     * Uses two distinct placeholders because PDO native prepared statements do not allow
     * the same named parameter to appear more than once.
     */
    public function checkEmailInUse($email)
    {
        $email = strtolower(trim($email));
        $this->db->query(
            'SELECT g.id FROM gebruiker g
             LEFT JOIN contact c ON g.id = c.gebruiker_id
             WHERE LOWER(g.gebruikersnaam) = :email1 OR LOWER(c.email) = :email2'
        );
        $this->db->bind(':email1', $email, PDO::PARAM_STR);
        $this->db->bind(':email2', $email, PDO::PARAM_STR);
        return $this->db->single() ? true : false;
    }

    /**
     * Check if a username already exists in the gebruiker table.
     */
    public function usernameExists($username)
    {
        $username = strtolower(trim($username));
        $this->db->query('SELECT id FROM gebruiker WHERE LOWER(gebruikersnaam) = :username');
        $this->db->bind(':username', $username, PDO::PARAM_STR);
        return $this->db->single() ? true : false;
    }

    /**
     * Create a full user account transactionally:
     * gebruiker → rol → contact → bezoeker|medewerker
     */
    public function createAccount($data)
    {
        try {
            $this->db->query('START TRANSACTION');
            $this->db->execute();

            // 1. Hash password and insert gebruiker
            $hashedPassword = password_hash($data['wachtwoord'], PASSWORD_DEFAULT);
            $this->db->query(
                'INSERT INTO gebruiker (voornaam, tussenvoegsel, achternaam, gebruikersnaam, wachtwoord, is_actief, is_ingelogd)
                 VALUES (:voornaam, :tussenvoegsel, :achternaam, :gebruikersnaam, :wachtwoord, 1, 0)'
            );
            $this->db->bind(':voornaam',      $data['voornaam'],      PDO::PARAM_STR);
            $this->db->bind(':tussenvoegsel', !empty($data['tussenvoegsel']) ? $data['tussenvoegsel'] : null, PDO::PARAM_STR);
            $this->db->bind(':achternaam',    $data['achternaam'],    PDO::PARAM_STR);
            $this->db->bind(':gebruikersnaam',$data['gebruikersnaam'],PDO::PARAM_STR);
            $this->db->bind(':wachtwoord',    $hashedPassword,        PDO::PARAM_STR);

            if (!$this->db->execute()) {
                $this->db->query('ROLLBACK');
                $this->db->execute();
                return false;
            }

            // Get new gebruiker ID
            $this->db->query('SELECT LAST_INSERT_ID() as id');
            $gebruikerId = $this->db->single()->id;

            // 2. Insert rol
            $this->db->query('INSERT INTO rol (gebruiker_id, naam, is_actief) VALUES (:gebruiker_id, :naam, 1)');
            $this->db->bind(':gebruiker_id', $gebruikerId,    PDO::PARAM_INT);
            $this->db->bind(':naam',         $data['rol'],    PDO::PARAM_STR);

            if (!$this->db->execute()) {
                $this->db->query('ROLLBACK');
                $this->db->execute();
                return false;
            }

            // 3. Insert contact
            $mobiel = !empty($data['mobiel']) ? $data['mobiel'] : '';
            $this->db->query(
                'INSERT INTO contact (gebruiker_id, email, mobiel, is_actief)
                 VALUES (:gebruiker_id, :email, :mobiel, 1)'
            );
            $this->db->bind(':gebruiker_id', $gebruikerId,    PDO::PARAM_INT);
            $this->db->bind(':email',        $data['email'],  PDO::PARAM_STR);
            $this->db->bind(':mobiel',       $mobiel,         PDO::PARAM_STR);

            if (!$this->db->execute()) {
                $this->db->query('ROLLBACK');
                $this->db->execute();
                return false;
            }

            // 4. Role-specific sub-table
            $roleLower = strtolower($data['rol']);
            if ($roleLower === 'bezoeker') {
                $this->db->query('SELECT MAX(relatienummer) as max_num FROM bezoeker');
                $result  = $this->db->single();
                $nextNum = ($result && $result->max_num) ? $result->max_num + 1 : 50001;

                $this->db->query(
                    'INSERT INTO bezoeker (gebruiker_id, relatienummer, is_actief)
                     VALUES (:gebruiker_id, :relatienummer, 1)'
                );
                $this->db->bind(':gebruiker_id',  $gebruikerId, PDO::PARAM_INT);
                $this->db->bind(':relatienummer', $nextNum,     PDO::PARAM_INT);

                if (!$this->db->execute()) {
                    $this->db->query('ROLLBACK');
                    $this->db->execute();
                    return false;
                }
            } else {
                // Admin, Medewerker, Receptie
                $this->db->query('SELECT MAX(nummer) as max_num FROM medewerker');
                $result  = $this->db->single();
                $nextNum = ($result && $result->max_num) ? $result->max_num + 1 : 101;

                $medewerkersoort = 'Medewerker';
                if ($roleLower === 'admin' || $roleLower === 'administrator') {
                    $medewerkersoort = 'Beheerder';
                } elseif ($roleLower === 'receptie') {
                    $medewerkersoort = 'Receptie';
                }

                $this->db->query(
                    'INSERT INTO medewerker (gebruiker_id, nummer, medewerkersoort, is_actief)
                     VALUES (:gebruiker_id, :nummer, :medewerkersoort, 1)'
                );
                $this->db->bind(':gebruiker_id',    $gebruikerId,    PDO::PARAM_INT);
                $this->db->bind(':nummer',          $nextNum,        PDO::PARAM_INT);
                $this->db->bind(':medewerkersoort', $medewerkersoort,PDO::PARAM_STR);

                if (!$this->db->execute()) {
                    $this->db->query('ROLLBACK');
                    $this->db->execute();
                    return false;
                }
            }

            $this->db->query('COMMIT');
            $this->db->execute();
            return true;

        } catch (Exception $e) {
            $this->db->query('ROLLBACK');
            $this->db->execute();
            return false;
        }
    }
}
