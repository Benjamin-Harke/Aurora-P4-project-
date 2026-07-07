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
        $medewerkerModel = $this->model('Medewerker'); // Load the employee model

        // 1. Check if the user is a staff member (for the button)
        //
        // We look for a medewerker record tied to the current gebruiker.
        // If one exists, the button to create/edit voorstellingen is shown.
        $isMedewerker = false;
        if (isset($_SESSION['user_id'])) {
            // Attempt to resolve the logged-in gebruiker to a Medewerker record.
            $medewerker = $medewerkerModel->getByGebruikerId($_SESSION['user_id']);
            if ($medewerker) {
                $isMedewerker = true;
            }
        }

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
}
