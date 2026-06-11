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
            'title'          => 'Mijn Meldingen',
            'meldingen'      => $meldingen,
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
        $type        = trim($_POST['type']      ?? '');
        $bericht     = trim($_POST['bericht']   ?? '');
        $opmerking   = trim($_POST['opmerking'] ?? '') ?: null;
        $is_actief   = isset($_POST['is_actief']) ? (int)$_POST['is_actief'] : 1;

        // Basisvalidatie
        $toegestane_types = ['notificatie', 'klacht', 'review'];
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

        // Uniek nummer genereren
        do {
            $nummer = random_int(100000, 999999);
            $bestaat = $this->meldingModel->getByNummer($nummer);
        } while ($bestaat);

        // Opslaan via model
        try {
            $result = $this->meldingModel->create([
                'bezoeker_id' => $bezoeker_id,
                'nummer'      => $nummer,
                'type'        => $type,
                'bericht'     => $bericht,
                'opmerking'   => $opmerking,
                'is_actief'   => $is_actief,
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