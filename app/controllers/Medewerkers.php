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
}
