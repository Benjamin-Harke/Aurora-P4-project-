<?php

class Medewerkers extends BaseController
{
    /**
     * Display all employees for staff members to view
     * User Story: Als medewerker van de theaterwebsite
     * wil ik een overzicht kunnen zien van alle medewerkers van het theater
     * zodat ik weet wie er werkzaam zijn binnen het theater en contactpersonen eenvoudig kan vinden.
     */
    public function index()
    {
        // Check permission: only admin and medewerkers can view
        $userRole = strtolower($_SESSION['rolle'] ?? 'bezoeker');
        if (!in_array($userRole, ['admin', 'medewerker'])) {
            $_SESSION['message'] = 'You do not have permission to view this page.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/dashboard');
            return;
        }

        $medewerkerModel = $this->model('Medewerker');
        $gebruikerModel = $this->model('Gebruiker');
        $contactModel = $this->model('Contact');

        // Get all employees
        $medewerkers = $medewerkerModel->getAll();

        // Enrich employee data with user and contact information
        $medewerkersList = [];
        foreach ($medewerkers as $medewerker) {
            $gebruiker = $gebruikerModel->getById($medewerker->gebruiker_id);
            $contact = $contactModel->getByGebruikerId($medewerker->gebruiker_id);
            
            $medewerkersList[] = (object)[
                'id' => $medewerker->id,
                'nummer' => $medewerker->nummer,
                'medewerkersoort' => $medewerker->medewerkersoort,
                'voornaam' => $gebruiker->voornaam ?? '',
                'tussenvoegsel' => $gebruiker->tussenvoegsel ?? '',
                'achternaam' => $gebruiker->achternaam ?? '',
                'email' => $contact->email ?? 'N/A',
                'mobiel' => $contact->mobiel ?? 'N/A',
                'is_actief' => $medewerker->is_actief
            ];
        }

        $data = [
            'title' => 'Medewerkers Overzicht',
            'medewerkers' => $medewerkersList
        ];

        $this->view('medewerkers/index', $data);
    }

    /**
     * Display details of a specific employee
     */
    public function detail($id = null)
    {
        // Check permission: only admin and medewerkers can view
        $userRole = strtolower($_SESSION['rolle'] ?? 'bezoeker');
        if (!in_array($userRole, ['admin', 'medewerker'])) {
            $_SESSION['message'] = 'You do not have permission to view this page.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/dashboard');
            return;
        }

        if ($id === null) {
            header('Location: ' . URLROOT . '/medewerkers');
            return;
        }

        $medewerkerModel = $this->model('Medewerker');
        $gebruikerModel = $this->model('Gebruiker');
        $contactModel = $this->model('Contact');

        $medewerker = $medewerkerModel->getById($id);

        if (!$medewerker) {
            die('Medewerker niet gevonden');
        }

        $gebruiker = $gebruikerModel->getById($medewerker->gebruiker_id);
        $contact = $contactModel->getByGebruikerId($medewerker->gebruiker_id);

        $medewerkerData = (object)[
            'id' => $medewerker->id,
            'nummer' => $medewerker->nummer,
            'medewerkersoort' => $medewerker->medewerkersoort,
            'voornaam' => $gebruiker->voornaam ?? '',
            'tussenvoegsel' => $gebruiker->tussenvoegsel ?? '',
            'achternaam' => $gebruiker->achternaam ?? '',
            'email' => $contact->email ?? 'N/A',
            'mobiel' => $contact->mobiel ?? 'N/A',
            'is_actief' => $medewerker->is_actief,
            'opmerking' => $medewerker->opmerking ?? ''
        ];

        $data = [
            'title' => 'Medewerker Details',
            'medewerker' => $medewerkerData
        ];

        $this->view('medewerkers/detail', $data);
    }

    /**
     * Show edit form for a medewerker
     */
    public function edit($id = null)
    {
        // Check permission
        $userRole = strtolower($_SESSION['rolle'] ?? 'bezoeker');
        if (!in_array($userRole, ['admin', 'medewerker'])) {
            $_SESSION['message'] = 'You do not have permission to edit this medewerker.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/dashboard');
            return;
        }

        if ($id === null) {
            header('Location: ' . URLROOT . '/medewerkers');
            return;
        }

        $medewerkerModel = $this->model('Medewerker');
        $gebruikerModel = $this->model('Gebruiker');
        $contactModel = $this->model('Contact');

        $medewerker = $medewerkerModel->getById($id);
        if (!$medewerker) {
            $_SESSION['message'] = 'Medewerker niet beschikbaar';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/medewerkers');
            return;
        }

        $gebruiker = $gebruikerModel->getById($medewerker->gebruiker_id);
        $contact = $contactModel->getByGebruikerId($medewerker->gebruiker_id);

        $medewerkerData = (object)[
            'id' => $medewerker->id,
            'gebruiker_id' => $medewerker->gebruiker_id,
            'nummer' => $medewerker->nummer,
            'medewerkersoort' => $medewerker->medewerkersoort,
            'voornaam' => $gebruiker->voornaam ?? '',
            'tussenvoegsel' => $gebruiker->tussenvoegsel ?? '',
            'achternaam' => $gebruiker->achternaam ?? '',
            'email' => $contact->email ?? '',
            'mobiel' => $contact->mobiel ?? '',
            'is_actief' => $medewerker->is_actief,
            'opmerking' => $medewerker->opmerking ?? ''
        ];

        $data = [
            'title' => 'Bewerk Medewerker',
            'medewerker' => $medewerkerData
        ];

        $this->view('medewerkers/edit', $data);
    }

    /**
     * Handle update POST for medewerker
     */
    public function update()
    {
        // Only allow POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/medewerkers');
            return;
        }

        // Check permission
        $userRole = strtolower($_SESSION['rolle'] ?? 'bezoeker');
        if (!in_array($userRole, ['admin', 'medewerker'])) {
            $_SESSION['message'] = 'You do not have permission to update this medewerker.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/dashboard');
            return;
        }

        $medewerkerId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$medewerkerId) {
            $_SESSION['message'] = 'Ongeldig medewerker ID';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/medewerkers');
            return;
        }

        $medewerkerModel = $this->model('Medewerker');
        $gebruikerModel = $this->model('Gebruiker');
        $contactModel = $this->model('Contact');

        $medewerker = $medewerkerModel->getById($medewerkerId);
        if (!$medewerker) {
            $_SESSION['message'] = 'Medewerker niet beschikbaar';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/medewerkers');
            return;
        }

        $gebruiker = $gebruikerModel->getById($medewerker->gebruiker_id);
        $contact = $contactModel->getByGebruikerId($medewerker->gebruiker_id);

        // Gather and sanitize input
        $voornaam = trim($_POST['voornaam'] ?? '');
        $tussenvoegsel = trim($_POST['tussenvoegsel'] ?? '');
        $achternaam = trim($_POST['achternaam'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mobiel = trim($_POST['mobiel'] ?? '');
        $nummer = trim($_POST['nummer'] ?? '');
        $medewerkersoort = trim($_POST['medewerkersoort'] ?? '');
        $is_actief = isset($_POST['is_actief']) ? 1 : 0;
        $opmerking = trim($_POST['opmerking'] ?? '');

        // Update gebruiker
        $gebruiker->id = $medewerker->gebruiker_id;
        $gebruiker->voornaam = $voornaam;
        $gebruiker->tussenvoegsel = $tussenvoegsel;
        $gebruiker->achternaam = $achternaam;
        // Keep existing password if none provided
        $gebruiker->wachtwoord = !empty($_POST['wachtwoord']) ? password_hash($_POST['wachtwoord'], PASSWORD_DEFAULT) : $gebruiker->wachtwoord;
        $gebruiker->gebruikersnaam = $gebruiker->gebruikersnaam; // username unchanged
        $gebruiker->is_ingelogd = $gebruiker->is_ingelogd ?? 0;
        $gebruiker->ingelogd_datum = $gebruiker->ingelogd_datum ?? null;
        $gebruiker->uitgelogd_datum = $gebruiker->uitgelogd_datum ?? null;
        $gebruiker->is_actief = $is_actief;
        $gebruiker->opmerking = $opmerking;

        $contactExists = $contact ? true : false;
        if ($contactExists) {
            $contact->id = $contact->id;
            $contact->gebruiker_id = $gebruiker->id;
            $contact->email = $email;
            $contact->mobiel = $mobiel;
            $contact->is_actief = $is_actief;
            $contact->opmerking = $opmerking;
        } else {
            // create new contact object
            $contact = new Contact();
            $contact->gebruiker_id = $gebruiker->id;
            $contact->email = $email;
            $contact->mobiel = $mobiel;
            $contact->is_actief = $is_actief;
            $contact->opmerking = $opmerking;
        }

        // Update medewerker
        $medewerker->id = $medewerkerId;
        $medewerker->nummer = $nummer;
        $medewerker->medewerkersoort = $medewerkersoort;
        $medewerker->is_actief = $is_actief;
        $medewerker->opmerking = $opmerking;

        // Persist changes. Prefer updating gebruiker first, then contact, then medewerker.
        $ok = true;
        if (method_exists($gebruikerModel, 'update')) {
            $gebruikerModel->id = $gebruiker->id;
            $gebruikerModel->voornaam = $gebruiker->voornaam;
            $gebruikerModel->tussenvoegsel = $gebruiker->tussenvoegsel;
            $gebruikerModel->achternaam = $gebruiker->achternaam;
            $gebruikerModel->gebruikersnaam = $gebruiker->gebruikersnaam;
            $gebruikerModel->wachtwoord = $gebruiker->wachtwoord;
            $gebruikerModel->is_ingelogd = $gebruiker->is_ingelogd;
            $gebruikerModel->ingelogd_datum = $gebruiker->ingelogd_datum;
            $gebruikerModel->uitgelogd_datum = $gebruiker->uitgelogd_datum;
            $gebruikerModel->is_actief = $gebruiker->is_actief;
            $gebruikerModel->opmerking = $gebruiker->opmerking;
            $ok = $ok && $gebruikerModel->update();
        }

        if ($contactExists) {
            $contactModel->id = $contact->id;
            $contactModel->gebruiker_id = $contact->gebruiker_id;
            $contactModel->email = $contact->email;
            $contactModel->mobiel = $contact->mobiel;
            $contactModel->is_actief = $contact->is_actief;
            $contactModel->opmerking = $contact->opmerking;
            $ok = $ok && $contactModel->update();
        } else {
            $ok = $ok && $contactModel->create();
        }

        $medewerkerModel->id = $medewerker->id;
        $medewerkerModel->gebruiker_id = $medewerker->gebruiker_id;
        $medewerkerModel->nummer = $medewerker->nummer;
        $medewerkerModel->medewerkersoort = $medewerker->medewerkersoort;
        $medewerkerModel->is_actief = $medewerker->is_actief;
        $medewerkerModel->opmerking = $medewerker->opmerking;
        $ok = $ok && $medewerkerModel->update();

        if ($ok) {
            $_SESSION['message'] = 'Medewerker succesvol bijgewerkt';
            $_SESSION['message_type'] = 'success';
            header('Location: ' . URLROOT . '/medewerkers/detail/' . $medewerkerId);
            return;
        } else {
            $_SESSION['message'] = 'Er is iets misgegaan bij het opslaan van de wijzigingen';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/medewerkers/edit/' . $medewerkerId);
            return;
        }
    }

    /**
     * Delete a medewerker (POST)
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/medewerkers');
            return;
        }

        // permission check
        $userRole = strtolower($_SESSION['rolle'] ?? 'bezoeker');
        if (!in_array($userRole, ['admin'])) {
            $_SESSION['message'] = 'You do not have permission to delete this medewerker.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/medewerkers');
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            $_SESSION['message'] = 'Ongeldig medewerker ID';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/medewerkers');
            return;
        }

        $medewerkerModel = $this->model('Medewerker');
        $medewerker = $medewerkerModel->getById($id);
        if (!$medewerker) {
            $_SESSION['message'] = 'Medewerker niet gevonden';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/medewerkers');
            return;
        }

        // perform delete
        if ($medewerkerModel->delete($id)) {
            $_SESSION['message'] = 'Medewerker verwijderd';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Kon medewerker niet verwijderen';
            $_SESSION['message_type'] = 'danger';
        }

        header('Location: ' . URLROOT . '/medewerkers');
    }
}
