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

        $bezoeker_id = $_SESSION['bezoeker_id'] ?? $_SESSION['accountId'];
        $type = trim($_POST['type'] ?? '');
        $bericht = trim($_POST['bericht'] ?? '');
        $opmerking = trim($_POST['opmerking'] ?? '') ?: null;
        $is_actief = isset($_POST['is_actief']) ? (int)$_POST['is_actief'] : 1;

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

        do {
            $nummer = random_int(100000, 999999);
            $bestaat = $this->meldingModel->getByNummer($nummer);
        } while ($bestaat);

        $result = $this->meldingModel->create([
            'bezoeker_id' => $bezoeker_id,
            'medewerker_id' => null,
            'nummer' => $nummer,
            'type' => $type,
            'bericht' => $bericht,
            'opmerking' => $opmerking,
            'is_actief' => $is_actief
        ]);

        if (!$result) {
            $_SESSION['melding_db_fout'] = true;
            header('location:' . URLROOT . '/meldingen');
            exit;
        }

        unset($_SESSION['melding_db_fout']);

        header('location:' . URLROOT . '/meldingen');
        exit;
    }
}