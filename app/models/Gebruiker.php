<?php

class Gebruiker {
    private $db;

    public $id;
    public $voornaam;
    public $tussenvoegsel;
    public $achternaam;
    public $gebruikersnaam;
    public $wachtwoord;
    public $is_ingelogd;
    public $ingelogd_datum;
    public $uitgelogd_datum;
    public $is_actief;
    public $opmerking;
    public $datum_aangemaakt;
    public $datum_gewijzigd;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Create a new Gebruiker record.
     * @return bool True on success, false on failure.
     */
    public function create() {
        $this->db->query('INSERT INTO gebruiker (voornaam, tussenvoegsel, achternaam, gebruikersnaam, wachtwoord, is_ingelogd, ingelogd_datum, uitgelogd_datum, is_actief, opmerking) VALUES (:voornaam, :tussenvoegsel, :achternaam, :gebruikersnaam, :wachtwoord, :is_ingelogd, :ingelogd_datum, :uitgelogd_datum, :is_actief, :opmerking)');
        $this->db->bind(':voornaam', $this->voornaam);
        $this->db->bind(':tussenvoegsel', $this->tussenvoegsel);
        $this->db->bind(':achternaam', $this->achternaam);
        $this->db->bind(':gebruikersnaam', $this->gebruikersnaam);
        $this->db->bind(':wachtwoord', $this->wachtwoord);
        $this->db->bind(':is_ingelogd', $this->is_ingelogd);
        $this->db->bind(':ingelogd_datum', $this->ingelogd_datum);
        $this->db->bind(':uitgelogd_datum', $this->uitgelogd_datum);
        $this->db->bind(':is_actief', $this->is_actief);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Get all Gebruiker records.
     * @return array An array of Gebruiker objects.
     */
    public function getAll() {
        $this->db->query('SELECT * FROM gebruiker');
        return $this->db->resultSet();
    }

    /**
     * Get a single Gebruiker record by ID.
     * @param int $id The ID of the gebruiker.
     * @return object|null The Gebruiker object or null if not found.
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM gebruiker WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Update an existing Gebruiker record.
     * @return bool True on success, false on failure.
     */
    public function update() {
        $this->db->query('UPDATE gebruiker SET voornaam = :voornaam, tussenvoegsel = :tussenvoegsel, achternaam = :achternaam, gebruikersnaam = :gebruikersnaam, wachtwoord = :wachtwoord, is_ingelogd = :is_ingelogd, ingelogd_datum = :ingelogd_datum, uitgelogd_datum = :uitgelogd_datum, is_actief = :is_actief, opmerking = :opmerking WHERE id = :id');

        // Bind with explicit types for bit/int fields and handle nullable dates
        $this->db->bind(':id', $this->id, PDO::PARAM_INT);
        $this->db->bind(':voornaam', $this->voornaam);
        $this->db->bind(':tussenvoegsel', $this->tussenvoegsel);
        $this->db->bind(':achternaam', $this->achternaam);
        $this->db->bind(':gebruikersnaam', $this->gebruikersnaam);
        $this->db->bind(':wachtwoord', $this->wachtwoord);

        // BIT columns: ensure integers 0 or 1
        $isIngelogd = ($this->is_ingelogd) ? 1 : 0;
        $this->db->bind(':is_ingelogd', $isIngelogd, PDO::PARAM_INT);

        if (empty($this->ingelogd_datum)) {
            $this->db->bind(':ingelogd_datum', null, PDO::PARAM_NULL);
        } else {
            $this->db->bind(':ingelogd_datum', $this->ingelogd_datum);
        }

        if (empty($this->uitgelogd_datum)) {
            $this->db->bind(':uitgelogd_datum', null, PDO::PARAM_NULL);
        } else {
            $this->db->bind(':uitgelogd_datum', $this->uitgelogd_datum);
        }

        $isActief = ($this->is_actief) ? 1 : 0;
        $this->db->bind(':is_actief', $isActief, PDO::PARAM_INT);
        $this->db->bind(':opmerking', $this->opmerking);

        return $this->db->execute();
    }

    /**
     * Delete a Gebruiker record by ID.
     * @param int $id The ID of the gebruiker to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM gebruiker WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Relationship methods
    public function getRoles() {
        $rolModel = new Rol();
        return $rolModel->getByGebruikerId($this->id);
    }

    public function getContact() {
        $contactModel = new Contact();
        return $contactModel->getByGebruikerId($this->id);
    }

    public function getMedewerker() {
        $medewerkerModel = new Medewerker();
        return $medewerkerModel->getByGebruikerId($this->id);
    }

    public function getBezoeker() {
        $bezoekerModel = new Bezoeker();
        return $bezoekerModel->getByGebruikerId($this->id);
    }
}