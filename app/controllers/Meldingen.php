<?php

class Meldingen extends BaseController
{
    private $meldingModel;

    public function __construct()
    {
        $this->meldingModel = $this->model('Melding');
    }

    public function index()
    {
        if (!isset($_SESSION['accountId'])) {
            header('location:' . URLROOT);
            exit;
        }

        if (!isset($_SESSION['melding_flow'])) {
            $_SESSION['melding_flow'] = 'happy';
        }

        $bezoeker_id = $_SESSION['bezoeker_id'] ?? $_SESSION['accountId'];

        $meldingen = $this->meldingModel->getByBezoekerId($bezoeker_id);

        if (!empty($meldingen)) {
            usort($meldingen, function ($a, $b) {
                return strtotime($b->datum_aangemaakt) - strtotime($a->datum_aangemaakt);
            });
        }

        $data = [
            'title' => 'Mijn Meldingen',
            'meldingen' => $meldingen,
            'heeft_meldingen' => !empty($meldingen),
            'melding_flow' => $_SESSION['melding_flow']
        ];

        $this->view('meldingen/overzicht', $data);
    }

    public function happy()
    {
        $_SESSION['melding_flow'] = 'happy';
        unset($_SESSION['melding_db_fout']);

        header('location:' . URLROOT . '/meldingen');
        exit;
    }

    public function unhappy()
    {
        $_SESSION['melding_flow'] = 'unhappy';
        unset($_SESSION['melding_db_fout']);

        header('location:' . URLROOT . '/meldingen');
        exit;
    }

    public function opslaan()
    {
        if (!isset($_SESSION['accountId'])) {
            header('location:' . URLROOT);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('location:' . URLROOT . '/meldingen');
            exit;
        }

        if ($_SESSION['melding_flow'] === 'unhappy') {
            $_SESSION['melding_db_fout'] = true;
            header('location:' . URLROOT . '/meldingen');
            exit;
        }

        $mijn_bezoeker_id = $_SESSION['bezoeker_id'] ?? $_SESSION['accountId'];

        $doelgroep = trim($_POST['doelgroep'] ?? '');
        $ontvanger_id = trim($_POST['ontvanger_id'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $bericht = trim($_POST['bericht'] ?? '');
        $opmerking = trim($_POST['opmerking'] ?? '') ?: null;
        $is_actief = isset($_POST['is_actief']) ? (int) $_POST['is_actief'] : 1;

        $toegestane_types = ['notificatie', 'klacht', 'review'];

        if (
            !in_array($type, $toegestane_types) ||
            $bericht === '' ||
            mb_strlen($bericht) > 250
        ) {
            $_SESSION['melding_db_fout'] = true;
            header('location:' . URLROOT . '/meldingen');
            exit;
        }

        if ($doelgroep === '' && $ontvanger_id === '') {
            $_SESSION['melding_db_fout'] = true;
            header('location:' . URLROOT . '/meldingen');
            exit;
        }

        $ontvangers = [];

        if ($ontvanger_id !== '') {
            $ontvangers[] = [
                'bezoeker_id' => (int) $ontvanger_id,
                'medewerker_id' => null
            ];
        }

        if ($ontvanger_id === '' && $doelgroep === 'bezoeker') {
            $ontvangers[] = [
                'bezoeker_id' => $mijn_bezoeker_id,
                'medewerker_id' => null
            ];
        }

        if ($ontvanger_id === '' && ($doelgroep === 'alle_bezoekers' || $doelgroep === 'iedereen')) {
            foreach ($this->meldingModel->getAllBezoekers() as $bezoeker) {
                $ontvangers[] = [
                    'bezoeker_id' => $bezoeker->id,
                    'medewerker_id' => null
                ];
            }
        }

        if ($ontvanger_id === '' && ($doelgroep === 'alle_medewerkers' || $doelgroep === 'iedereen')) {
            foreach ($this->meldingModel->getAllMedewerkers() as $medewerker) {
                $ontvangers[] = [
                    'bezoeker_id' => null,
                    'medewerker_id' => $medewerker->id
                ];
            }
        }

        if (empty($ontvangers)) {
            $_SESSION['melding_db_fout'] = true;
            header('location:' . URLROOT . '/meldingen');
            exit;
        }

        foreach ($ontvangers as $ontvanger) {
            do {
                $nummer = random_int(100000, 999999);
                $bestaat = $this->meldingModel->getByNummer($nummer);
            } while ($bestaat);

            $this->meldingModel->create([
                'bezoeker_id' => $ontvanger['bezoeker_id'],
                'medewerker_id' => $ontvanger['medewerker_id'],
                'nummer' => $nummer,
                'type' => $type,
                'bericht' => $bericht,
                'is_actief' => $is_actief,
                'opmerking' => $opmerking
            ]);
        }

        unset($_SESSION['melding_db_fout']);

        header('location:' . URLROOT . '/meldingen');
        exit;
    }
}