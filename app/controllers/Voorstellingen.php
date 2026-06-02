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
