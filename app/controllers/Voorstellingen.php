<?php

class Voorstellingen extends BaseController
{
    /**
     * Display all performances for guests to browse
     * User Story: Als gastgebruiker van de theaterwebsite
     * wil ik een overzicht kunnen zien van alle voorstellingen
     */
    public function index()
    {
        $voorstellingModel = $this->model('Voorstelling');

        // 1. Check if the user is a staff member (for the button)
        $isMedewerker = $this->isStaff();

        // 2. Get all performances (your original logic)
        $voorstellingen = $voorstellingModel->getAll();
        
        // 3. Prepare data - MAKE SURE THE NAMES MATCH YOUR VIEW
        $data = [
            'title' => 'Alle Voorstellingen',
            'voorstellingen' => $voorstellingen, // Keep this name so your cards work
            'is_medewerker' => $isMedewerker     // Add this for the button
        ];

        $this->view('voorstellingen/index', $data);
    }

    private function getCurrentMedewerker()
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        $medewerkerModel = $this->model('Medewerker');
        return $medewerkerModel->getByGebruikerId($_SESSION['user_id']);
    }

    private function isStaff()
    {
        $role = strtolower($_SESSION['rolle'] ?? 'bezoeker');
        if (in_array($role, ['admin', 'medewerker'])) {
            return true;
        }

        return (bool)$this->getCurrentMedewerker();
    }

    /**
     * Show create form for medewerkers
     */
    public function create()
    {
        // Only staff allowed
        if (empty($_SESSION['user_id']) || !$this->isStaff()) {
            $_SESSION['message'] = 'You do not have permission to create a voorstelling.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        $medewerker = $this->getCurrentMedewerker();
        if (!$medewerker) {
            $_SESSION['message'] = 'Je moet een medewerker-account hebben om een voorstelling aan te maken.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        $data = [
            'title' => 'Nieuwe Voorstelling',
            'naam' => '',
            'beschrijving' => '',
            'datum' => '',
            'tijd' => '',
            'max_aantal_tickets' => 100
        ];
        $this->view('voorstellingen/create', $data);
    }

    /**
     * Handle create POST
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        // permission
        if (empty($_SESSION['user_id']) || !$this->isStaff()) {
            $_SESSION['message'] = 'You do not have permission to create a voorstelling.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        // collect input
        $naam = trim($_POST['naam'] ?? '');
        $beschrijving = trim($_POST['beschrijving'] ?? '');
        $datum = trim($_POST['datum'] ?? '');
        $tijd = trim($_POST['tijd'] ?? '');
        $max = (int)($_POST['max_aantal_tickets'] ?? 0);

        $data = [
            'title' => 'Nieuwe Voorstelling',
            'naam' => $naam,
            'beschrijving' => $beschrijving,
            'datum' => $datum,
            'tijd' => $tijd,
            'max_aantal_tickets' => $max
        ];

        if (empty($naam) || empty($datum) || empty($tijd) || $max <= 0) {
            $_SESSION['message'] = 'Vul een titel, datum, tijd en het maximale aantal tickets in.';
            $_SESSION['message_type'] = 'danger';
            $this->view('voorstellingen/create', $data);
            return;
        }

        // date validation: no past dates
        $today = new DateTime('today');
        $given = DateTime::createFromFormat('Y-m-d', $datum);
        if (!$given || $given < $today) {
            $_SESSION['message'] = 'De opgegeven datum ligt in het verleden. Kies een geldige datum om de voorstelling toe te voegen.';
            $_SESSION['message_type'] = 'danger';
            $this->view('voorstellingen/create', $data);
            return;
        }

        $medewerker = $this->getCurrentMedewerker();
        if (!$medewerker) {
            $_SESSION['message'] = 'Je moet een medewerker-account hebben om een voorstelling aan te maken.';
            $_SESSION['message_type'] = 'danger';
            $this->view('voorstellingen/create', $data);
            return;
        }

        $voorstellingModel = $this->model('Voorstelling');
        $createData = [
            'medewerker_id' => $medewerker->id,
            'naam' => $naam,
            'beschrijving' => $beschrijving,
            'datum' => $datum,
            'tijd' => $tijd,
            'max_aantal_tickets' => $max
        ];

        if ($voorstellingModel->create($createData)) {
            $_SESSION['message'] = 'Voorstelling succesvol aangemaakt';
            $_SESSION['message_type'] = 'success';
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        $_SESSION['message'] = 'Er is iets misgegaan bij het aanmaken van de voorstelling';
        $_SESSION['message_type'] = 'danger';
        $this->view('voorstellingen/create', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id = null)
    {
        if ($id === null) {
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        if (empty($_SESSION['user_id']) || !$this->isStaff()) {
            $_SESSION['message'] = 'You do not have permission to edit this voorstelling.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        $voorstellingModel = $this->model('Voorstelling');
        $voorstelling = $voorstellingModel->getById($id);
        if (!$voorstelling) {
            $_SESSION['message'] = 'Voorstelling niet gevonden';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        $data = ['title' => 'Bewerk Voorstelling', 'voorstelling' => $voorstelling];
        $this->view('voorstellingen/edit', $data);
    }

    /**
     * Handle update POST
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        if (empty($_SESSION['user_id']) || !$this->isStaff()) {
            $_SESSION['message'] = 'You do not have permission to update this voorstelling.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        $naam = trim($_POST['naam'] ?? '');
        $beschrijving = trim($_POST['beschrijving'] ?? '');
        $datum = trim($_POST['datum'] ?? '');
        $tijd = trim($_POST['tijd'] ?? '');
        $max = (int)($_POST['max_aantal_tickets'] ?? 0);

        $data = [
            'title' => 'Bewerk Voorstelling',
            'voorstelling' => (object)[
                'id' => $id,
                'naam' => $naam,
                'beschrijving' => $beschrijving,
                'datum' => $datum,
                'tijd' => $tijd,
                'max_aantal_tickets' => $max
            ]
        ];

        if (empty($naam) || empty($datum) || empty($tijd) || $max <= 0) {
            $_SESSION['message'] = 'Vul een titel, datum, tijd en het maximale aantal tickets in.';
            $_SESSION['message_type'] = 'danger';
            $this->view('voorstellingen/edit', $data);
            return;
        }

        $today = new DateTime('today');
        $given = DateTime::createFromFormat('Y-m-d', $datum);
        if (!$given || $given < $today) {
            $_SESSION['message'] = 'De opgegeven datum ligt in het verleden. Kies een geldige datum om de voorstelling bij te werken.';
            $_SESSION['message_type'] = 'danger';
            $this->view('voorstellingen/edit', $data);
            return;
        }

        $medewerker = $this->getCurrentMedewerker();
        if (!$medewerker) {
            $_SESSION['message'] = 'Je moet een medewerker-account hebben om een voorstelling bij te werken.';
            $_SESSION['message_type'] = 'danger';
            $this->view('voorstellingen/edit', $data);
            return;
        }

        $voorstellingModel = $this->model('Voorstelling');
        $voorstellingModel->id = $id;
        $voorstellingModel->medewerker_id = $medewerker->id;
        $voorstellingModel->naam = $naam;
        $voorstellingModel->beschrijving = $beschrijving;
        $voorstellingModel->datum = $datum;
        $voorstellingModel->tijd = $tijd;
        $voorstellingModel->max_aantal_tickets = $max;
        $voorstellingModel->beschikbaarheid = 'Zichtbaar';
        $voorstellingModel->is_actief = 1;

        if ($voorstellingModel->update()) {
            $_SESSION['message'] = 'Voorstelling succesvol bijgewerkt';
            $_SESSION['message_type'] = 'success';
            header('Location: ' . URLROOT . '/voorstellingen/detail/' . $id);
            return;
        }

        $_SESSION['message'] = 'Er is iets misgegaan bij het bijwerken van de voorstelling';
        $_SESSION['message_type'] = 'danger';
        $this->view('voorstellingen/edit', $data);
    }

    /**
     * Delete voorstelling (POST)
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }
        if (empty($_SESSION['user_id']) || !$this->isStaff()) {
            $_SESSION['message'] = 'You do not have permission to delete this voorstelling.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        $voorstellingModel = $this->model('Voorstelling');
        if ($voorstellingModel->delete($id)) {
            $_SESSION['message'] = 'Voorstelling verwijderd';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Kon voorstelling niet verwijderen';
            $_SESSION['message_type'] = 'danger';
        }
        header('Location: ' . URLROOT . '/voorstellingen');
    }

    /**
     * Display details of a specific performance
     */
    public function detail($id = null)
    {
        if ($id === null) {
            header('Location: ' . URLROOT . '/voorstellingen');
            return;
        }

        $voorstellingModel = $this->model('Voorstelling');
        $voorstelling = $voorstellingModel->getById($id);

        if (!$voorstelling) {
            die('Voorstelling niet gevonden');
        }

        $isMedewerker = $this->isStaff();

        $data = [
            'title' => 'Voorstelling Details',
            'voorstelling' => $voorstelling,
            'is_medewerker' => $isMedewerker
        ];

        $this->view('voorstellingen/detail', $data);
    }
}
