<?php

class AdminPerformances extends BaseController {
    private $voorstellingModel;
    private $medewerkerModel;

    public function __construct() {
        $this->voorstellingModel = $this->model('Voorstelling');
        $this->medewerkerModel = $this->model('Medewerker');
        
        // Basic Security Check: Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/test/index');
            exit;
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Get the Medewerker ID of the logged-in admin
            $medewerker = $this->medewerkerModel->getByGebruikerId($_SESSION['user_id']);

            if (!$medewerker) {
                $_SESSION['error'] = 'Access Denied: You must be logged in as an Employee to create shows.';
                header('Location: ' . URLROOT . '/publictickets');
                exit;
            }
            
            $data = [
                'medewerker_id' => $medewerker->id,
                'naam' => trim($_POST['naam'] ?? ''),
                'beschrijving' => trim($_POST['beschrijving'] ?? ''),
                'datum' => trim($_POST['datum'] ?? ''),
                'tijd' => trim($_POST['tijd'] ?? ''),
                'max_aantal_tickets' => trim($_POST['max_aantal_tickets'] ?? ''),
            ];

            // Simple Validation
            if (empty($data['naam']) || empty($data['datum'])) {
                $_SESSION['error'] = 'Vul alstublieft de naam en datum in.';
                $this->view('admin/performances/create', $data);
                return;
            }

            if ($data['datum'] < date('Y-m-d')) {
                $_SESSION['error'] = 'De datum van de voorstelling is al gepasseerd. Kies een toekomstige datum.';
                $this->view('admin/performances/create', $data);
                return;
            }

            if ($this->voorstellingModel->create($data)) {
                $_SESSION['success'] = 'New show created successfully!';
                header('Location: ' . URLROOT . '/publictickets');
                exit;
            } else {
                $_SESSION['error'] = 'Er is iets misgegaan bij het opslaan van de voorstelling.';
                $this->view('admin/performances/create', $data);
                return;
            }
        } else {
            $data = [
                'naam' => '',
                'beschrijving' => '',
                'datum' => '',
                'tijd' => '',
                'max_aantal_tickets' => '80'
            ];
            $this->view('admin/performances/create', $data);
        }
    }
}