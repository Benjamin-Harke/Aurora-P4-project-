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
        
        // Get all performances
        $voorstellingen = $voorstellingModel->getAll();
        
        $data = [
            'title' => 'Alle Voorstellingen',
            'voorstellingen' => $voorstellingen
        ];

        $this->view('voorstellingen/index', $data);
    }

    /**
     * Show form and handle submission for creating a new performance
     */
    public function create()
    {
        if (!$this->isAdmin()) {
            redirect('homepages');
        }

        // Performance-specific form data for creating a new voorstelling
        $data = [
            'title' => 'Nieuwe Voorstelling',
            'naam' => '',
            'beschrijving' => '',
            'datum' => '',
            'tijd' => '',
            'locatie' => '',
            'max_aantal_tickets' => '100',
            'beschikbaarheid' => 'Ingepland',
            'errors' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['naam'] = trim($_POST['naam'] ?? '');
            $data['beschrijving'] = trim($_POST['beschrijving'] ?? '');
            $data['datum'] = trim($_POST['datum'] ?? '');
            $data['tijd'] = trim($_POST['tijd'] ?? '');
            $data['locatie'] = trim($_POST['locatie'] ?? '');
            $data['max_aantal_tickets'] = trim($_POST['max_aantal_tickets'] ?? '100');
            $data['beschikbaarheid'] = trim($_POST['beschikbaarheid'] ?? 'Ingepland');

            // Validate form inputs
            if (empty($data['naam'])) {
                $data['errors']['naam'] = 'Vul een titel in voor de voorstelling.';
            }

            // Validate date for the voorstelling to ensure it's a future performance
            if (empty($data['datum'])) {
                $data['errors']['datum'] = 'Vul een datum in voor de voorstelling.';
            } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['datum']) || !strtotime($data['datum'])) {
                $data['errors']['datum'] = 'Voer een geldige datum in.';
            } elseif ($data['datum'] < date('Y-m-d')) {
                $data['errors']['datum'] = 'De opgegeven datum ligt in het verleden. Kies een geldige datum om de voorstelling toe te voegen.';
                $_SESSION['error'] = 'De opgegeven datum ligt in het verleden. Kies een geldige datum om de voorstelling toe te voegen.';
            }

            if (empty($data['tijd'])) {
                $data['errors']['tijd'] = 'Vul een tijd in voor de voorstelling.';
            } elseif (!preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $data['tijd'])) {
                $data['errors']['tijd'] = 'Voer een geldige tijd in (HH:MM of HH:MM:SS).';
            }

            if (empty($data['locatie'])) {
                $data['errors']['locatie'] = 'Vul een locatie in voor de voorstelling.';
            }

            if (!ctype_digit($data['max_aantal_tickets']) || intval($data['max_aantal_tickets']) <= 0) {
                $data['errors']['max_aantal_tickets'] = 'Vul een geldig aantal beschikbare kaarten in.';
            }

            if (empty($data['errors'])) {
                $voorstellingModel = $this->model('Voorstelling');
                $medewerkerModel = $this->model('Medewerker');
                $medewerker = $medewerkerModel->getByGebruikerId($_SESSION['user_id'] ?? 0);

                if (!$medewerker) {
                    $_SESSION['error'] = 'Kan geen medewerker koppelen aan uw account. Log opnieuw in als beheerder.';
                    redirect('voorstellingen/create');
                }

                $voorstellingModel->medewerker_id = $medewerker->id;
                $voorstellingModel->naam = $data['naam'];
                $voorstellingModel->beschrijving = $data['beschrijving'];
                // Map performance-specific fields into the Voorstelling model
                $voorstellingModel->datum = $data['datum'];
                $voorstellingModel->tijd = $data['tijd'];
                $voorstellingModel->max_aantal_tickets = intval($data['max_aantal_tickets']);
                $voorstellingModel->beschikbaarheid = $data['beschikbaarheid'];
                $voorstellingModel->is_actief = 1;
                // Location is stored in opmerking because the Voorstelling model currently does not have a dedicated locatie column
                $voorstellingModel->opmerking = 'Locatie: ' . $data['locatie'];

                if ($voorstellingModel->create()) {
                    $_SESSION['success'] = 'De nieuwe voorstelling is succesvol toegevoegd.';
                    redirect('voorstellingen');
                }

                $_SESSION['error'] = 'Er is iets misgegaan bij het opslaan van de voorstelling. Probeer het opnieuw.';
            }
        }

        $this->view('voorstellingen/create', $data);
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

        $data = [
            'title' => 'Voorstelling Details',
            'voorstelling' => $voorstelling
        ];

        $this->view('voorstellingen/detail', $data);
    }

    private function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}
