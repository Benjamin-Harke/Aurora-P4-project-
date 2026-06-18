<?php

class Meldingen extends BaseController
{
    // Model gebruiken voor alle database acties van meldingen
    private $meldingModel;

    public function __construct()
    {
        $this->meldingModel = $this->model('Melding');
    }

    // Toont het overzicht van meldingen
    public function index()
    {
        // Als gebruiker niet is ingelogd, terug naar home
        if (!isset($_SESSION['accountId'])) {
            header('location:' . URLROOT);
            exit;
        }

        // Standaard staat de flow op happy
        if (!isset($_SESSION['melding_flow'])) {
            $_SESSION['melding_flow'] = 'happy';
        }

        // Id van de ingelogde gebruiker
        $gebruiker_id = $_SESSION['accountId'];

        // Rollen van de gebruiker ophalen
        $rollen = $this->meldingModel->getRollenByGebruikerId($gebruiker_id);

        // Standaard is gebruiker geen admin
        $is_admin = false;

        // Controleren of gebruiker admin is
        foreach ($rollen as $rol) {
            $rolNaam = strtolower(trim($rol->naam));

            if ($rolNaam === 'administrator' || $rolNaam === 'admin') {
                $is_admin = true;
                break;
            }
        }

        // Hier komen alle meldingen in
        $meldingen = [];

        // Bezoeker en medewerker ophalen via gebruiker_id
        $bezoeker = $this->meldingModel->getBezoekerByGebruikerId($gebruiker_id);
        $medewerker = $this->meldingModel->getMedewerkerByGebruikerId($gebruiker_id);

        // Meldingen ophalen als gebruiker een bezoeker is
        if ($bezoeker) {
            $meldingen = array_merge(
                $meldingen,
                $this->meldingModel->getByBezoekerId($bezoeker->id)
            );
        }

        // Meldingen ophalen als gebruiker een medewerker is
        if ($medewerker) {
            $meldingen = array_merge(
                $meldingen,
                $this->meldingModel->getByMedewerkerId($medewerker->id)
            );
        }

        // Meldingen sorteren op nieuwste datum
        if (!empty($meldingen)) {
            usort($meldingen, function ($a, $b) {
                return strtotime($b->datum_aangemaakt) - strtotime($a->datum_aangemaakt);
            });
        }

        // Data naar de view sturen
        $data = [
            'title' => 'Mijn Meldingen',
            'meldingen' => $meldingen,
            'heeft_meldingen' => !empty($meldingen),
            'melding_flow' => $_SESSION['melding_flow'],
            'is_admin' => $is_admin
        ];

        $this->view('meldingen/overzicht', $data);
    }

    // Zet de pagina in happy flow
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

    // Nieuwe melding opslaan
    public function opslaan()
    {
        // Controleren of gebruiker is ingelogd
        if (!isset($_SESSION['accountId'])) {
            header('location:' . URLROOT);
            exit;
        }

        // Alleen POST requests toestaan
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('location:' . URLROOT . '/meldingen');
            exit;
        }

        // Bij unhappy scenario proberen we bewust op te slaan in een lege testdatabase
        if (isset($_SESSION['melding_flow']) && $_SESSION['melding_flow'] === 'unhappy') {

            if (!$this->meldingModel->createUnhappy()) {
                $_SESSION['melding_db_fout'] = 'Geen connectie met database gevonden.';

                header('location:' . URLROOT . '/meldingen');
                exit;
            }
        }

        // Eigen bezoeker id gebruiken als de admin naar zichzelf stuurt
        $gebruiker_id = $_SESSION['accountId'];
        $eigenBezoeker = $this->meldingModel->getBezoekerByGebruikerId($gebruiker_id);
        $mijn_bezoeker_id = $eigenBezoeker ? $eigenBezoeker->id : null;

        // Gegevens uit het formulier halen
        $doelgroep = trim($_POST['doelgroep'] ?? '');
        $ontvanger_id = trim($_POST['ontvanger_id'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $bericht = trim($_POST['bericht'] ?? '');
        $opmerking = trim($_POST['opmerking'] ?? '') ?: null;
        $is_actief = isset($_POST['is_actief']) ? (int) $_POST['is_actief'] : 1;

        // Alleen deze types mogen gebruikt worden
        $toegestane_types = ['notificatie', 'klacht', 'review'];

        // Simpele validatie van de verplichte velden
        if (
            !in_array($type, $toegestane_types) ||
            $bericht === '' ||
            mb_strlen($bericht) > 250
        ) {
            $_SESSION['melding_db_fout'] = true;
            header('location:' . URLROOT . '/meldingen');
            exit;
        }

        // Er moet een doelgroep of ontvanger id zijn
        if ($doelgroep === '' && $ontvanger_id === '') {
            $_SESSION['melding_db_fout'] = true;
            header('location:' . URLROOT . '/meldingen');
            exit;
        }

        // Hier komen alle ontvangers in
        $ontvangers = [];

        // Melding naar 1 specifieke bezoeker sturen
        if ($ontvanger_id !== '') {
            $ontvangers[] = [
                'bezoeker_id' => (int) $ontvanger_id,
                'medewerker_id' => null
            ];
        }

        // Melding alleen naar jezelf sturen
        if ($ontvanger_id === '' && $doelgroep === 'bezoeker' && $mijn_bezoeker_id) {
            $ontvangers[] = [
                'bezoeker_id' => $mijn_bezoeker_id,
                'medewerker_id' => null
            ];
        }

        // Melding naar alle bezoekers sturen
        if ($ontvanger_id === '' && ($doelgroep === 'alle_bezoekers' || $doelgroep === 'iedereen')) {
            foreach ($this->meldingModel->getAllBezoekers() as $bezoeker) {
                $ontvangers[] = [
                    'bezoeker_id' => $bezoeker->id,
                    'medewerker_id' => null
                ];
            }
        }

        // Melding naar alle medewerkers sturen
        if ($ontvanger_id === '' && ($doelgroep === 'alle_medewerkers' || $doelgroep === 'iedereen')) {
            foreach ($this->meldingModel->getAllMedewerkers() as $medewerker) {
                $ontvangers[] = [
                    'bezoeker_id' => null,
                    'medewerker_id' => $medewerker->id
                ];
            }
        }

        // Als er geen ontvangers zijn gevonden, fout tonen
        if (empty($ontvangers)) {
            $_SESSION['melding_db_fout'] = true;
            header('location:' . URLROOT . '/meldingen');
            exit;
        }

        // Voor elke ontvanger een melding opslaan
        foreach ($ontvangers as $ontvanger) {

            // Uniek nummer maken
            do {
                $nummer = random_int(100000, 999999);
                $bestaat = $this->meldingModel->getByNummer($nummer);
            } while ($bestaat);

            // Melding opslaan in database
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

        // Foutmelding verwijderen als alles goed ging
        unset($_SESSION['melding_db_fout']);

        // Terug naar meldingen overzicht
        header('location:' . URLROOT . '/meldingen');
        exit;
    }
}