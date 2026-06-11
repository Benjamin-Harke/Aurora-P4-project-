<?php

class Meldingen extends BaseController
{
    private $meldingModel;

    public function __construct()
    {
        $this->meldingModel = $this->model('Melding');
    }

    /**
     * Overzicht van meldingen voor de ingelogde bezoeker.
     * Happy flow: gesorteerd op datum.
     * Unhappy flow: melding dat er geen meldingen zijn.
     */
    public function index()
    {
        // Niet ingelogd? Terug naar home
        if (!isset($_SESSION['accountId'])) {
            header('location:' . URLROOT);
            return;
        }

        $bezoeker_id = $_SESSION['bezoeker_id'] ?? null;

        $meldingen = [];
        if ($bezoeker_id) {
            $meldingen = $this->meldingModel->getByBezoekerId($bezoeker_id);

            // Sorteer op datum_aangemaakt, nieuwste eerst
            if (!empty($meldingen)) {
                usort($meldingen, function ($a, $b) {
                    return strtotime($b->datum_aangemaakt) - strtotime($a->datum_aangemaakt);
                });
            }
        }

        $data = [
            'title' => 'Mijn Meldingen',
            'meldingen' => $meldingen,
            'heeft_meldingen' => !empty($meldingen),
        ];

        $this->view('meldingen/overzicht', $data);
    }

    /**
     * Nieuwe melding opslaan (POST).
     * Happy flow:  melding opgeslagen → redirect naar overzicht.
     * Unhappy flow: db-fout → popup via sessie-flag.
     */
    public function opslaan()
    {
        // Niet ingelogd? Terug naar home
        if (!isset($_SESSION['accountId'])) {
            header('location:' . URLROOT);
            return;
        }

        // Alleen POST toegestaan
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('location:' . URLROOT . '/meldingen');
            return;
        }

        $bezoeker_id = $_SESSION['bezoeker_id'] ?? null;
        $type = trim($_POST['type'] ?? '');
        $bericht = trim($_POST['bericht'] ?? '');

        // Basisvalidatie
        $toegestane_types = ['info', 'waarschuwing', 'succes', 'fout'];
        if (
            !$bezoeker_id
            || !in_array($type, $toegestane_types)
            || empty($bericht)
            || mb_strlen($bericht) > 250
        ) {
            $_SESSION['melding_db_fout'] = true;
            header('location:' . URLROOT . '/meldingen');
            return;
        }

        // Uniek nummer genereren (tijdstempel-gebaseerd)
        $nummer = (int) (microtime(true) * 100) % 9000000 + 1000000;

        // Opslaan via model
        try {
            $result = $this->meldingModel->create([
                'bezoeker_id' => $bezoeker_id,
                'nummer' => $nummer,
                'type' => $type,
                'bericht' => $bericht,
                'is_actief' => 1,
            ]);

            if (!$result) {
                throw new Exception('Insert mislukt');
            }

            // Happy flow: terug naar overzicht
            header('location:' . URLROOT . '/meldingen');

        } catch (Exception $e) {
            // Unhappy flow: toon fout-popup
            $_SESSION['melding_db_fout'] = true;
            header('location:' . URLROOT . '/meldingen');
        }
    }
}