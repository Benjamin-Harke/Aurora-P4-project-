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

        // Haal bezoeker_id op via accountId -> bezoekertabel
        // De melding tabel werkt met bezoeker_id (niet accountId)
        $bezoeker_id = $_SESSION['bezoeker_id'] ?? null;

        $meldingen = [];
        if ($bezoeker_id) {
            $meldingen = $this->meldingModel->getByBezoekerId($bezoeker_id);

            // Sorteer op datum_aangemaakt, nieuwste eerst (happy flow eis)
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
}
