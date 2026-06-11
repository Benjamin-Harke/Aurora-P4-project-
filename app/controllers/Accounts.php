<?php

class Accounts extends BaseController
{
    private $accountModel;
    private $rolModel;

    public function __construct()
    {
        $this->accountModel = $this->model('Account');
        $this->rolModel = $this->model('Rol');
    }

    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['accountId'])) {
            header('Location: ' . URLROOT . '/auth');
            exit;
        }

        // Check if user has admin role (case-insensitive)
        $userRole = strtolower($_SESSION['rolle'] ?? 'bezoeker');
        if ($userRole !== 'admin' && $userRole !== 'medewerker') {
            $_SESSION['error'] = 'You do not have permission to view this page';
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        // Get all users with their roles
        $users = $this->getAllUsersWithRoles();

        $data = [
            'title' => 'Account Overview',
            'users' => $users
        ];

        $this->view('accounts/index', $data);
    }

    private function getAllUsersWithRoles()
    {
        $users = $this->accountModel->getAllWithRoles();

        if ($users && count($users) > 0) {
            return array_map(function ($obj) {
                $arr = (array) $obj;
                $arr['roles'] = $arr['roles'] ? explode(',', $arr['roles']) : [];
                return $arr;
            }, $users);
        }

        return [];
    }

    public function create()
    {
        // Must be logged in
        if (!isset($_SESSION['accountId'])) {
            header('Location: ' . URLROOT . '/auth');
            exit;
        }

        // Only admin / medewerker may create accounts
        $userRole = strtolower($_SESSION['rolle'] ?? 'bezoeker');
        if ($userRole !== 'admin' && $userRole !== 'medewerker') {
            $_SESSION['error'] = 'You do not have permission to view this page';
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collect and sanitise input
            $data = [
                'voornaam'               => trim($_POST['voornaam']              ?? ''),
                'tussenvoegsel'          => trim($_POST['tussenvoegsel']         ?? ''),
                'achternaam'             => trim($_POST['achternaam']            ?? ''),
                'email'                  => trim($_POST['email']                 ?? ''),
                'gebruikersnaam'         => trim($_POST['gebruikersnaam']        ?? ''),
                'rol'                    => trim($_POST['rol']                   ?? ''),
                'wachtwoord'             => trim($_POST['wachtwoord']            ?? ''),
                'wachtwoord_bevestigen'  => trim($_POST['wachtwoord_bevestigen'] ?? ''),
                'mobiel'                 => trim($_POST['mobiel']                ?? ''),
                // Error buckets
                'voornaam_err'              => '',
                'achternaam_err'            => '',
                'email_err'                 => '',
                'gebruikersnaam_err'        => '',
                'rol_err'                   => '',
                'wachtwoord_err'            => '',
                'wachtwoord_bevestigen_err' => '',
            ];

            // --- Validation ---
            if (empty($data['voornaam'])) {
                $data['voornaam_err'] = 'Voornaam is verplicht';
            }
            if (empty($data['achternaam'])) {
                $data['achternaam_err'] = 'Achternaam is verplicht';
            }
            if (empty($data['email'])) {
                $data['email_err'] = 'E-mailadres is verplicht';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Ongeldig e-mailadres';
            }
            if (empty($data['gebruikersnaam'])) {
                $data['gebruikersnaam_err'] = 'Gebruikersnaam is verplicht';
            }
            if (empty($data['rol'])) {
                $data['rol_err'] = 'Rol is verplicht';
            }
            if (empty($data['wachtwoord'])) {
                $data['wachtwoord_err'] = 'Wachtwoord is verplicht';
            } elseif (strlen($data['wachtwoord']) < 6) {
                $data['wachtwoord_err'] = 'Wachtwoord moet minimaal 6 tekens bevatten';
            }
            if (empty($data['wachtwoord_bevestigen'])) {
                $data['wachtwoord_bevestigen_err'] = 'Wachtwoord bevestigen is verplicht';
            } elseif ($data['wachtwoord'] !== $data['wachtwoord_bevestigen']) {
                $data['wachtwoord_bevestigen_err'] = 'Wachtwoorden komen niet overeen';
            }

            // Duplicate e-mail check (acceptance criterion: "Email is al in gebruik")
            if (empty($data['email_err']) && $this->accountModel->checkEmailInUse($data['email'])) {
                $data['email_err'] = 'Email is al in gebruik';
            }

            // Duplicate username check
            if (empty($data['gebruikersnaam_err']) && $this->accountModel->usernameExists($data['gebruikersnaam'])) {
                $data['gebruikersnaam_err'] = 'Gebruikersnaam is al in gebruik';
            }

            // --- Save if no errors ---
            $hasErrors =
                $data['voornaam_err'] || $data['achternaam_err'] ||
                $data['email_err']    || $data['gebruikersnaam_err'] ||
                $data['rol_err']      || $data['wachtwoord_err'] ||
                $data['wachtwoord_bevestigen_err'];

            if (!$hasErrors) {
                if ($this->accountModel->createAccount($data)) {
                    $_SESSION['success'] = 'Account succesvol aangemaakt';
                    header('Location: ' . URLROOT . '/accounts');
                    exit;
                } else {
                    $_SESSION['error'] = 'Er is iets misgegaan bij het opslaan van het account';
                }
            }

            // Re-render form with error feedback and preserved input
            $data['title'] = 'Nieuw Account Toevoegen';
            $this->view('accounts/create', $data);

        } else {
            // GET – show empty form
            $data = [
                'title'                     => 'Nieuw Account Toevoegen',
                'voornaam'                  => '',
                'tussenvoegsel'             => '',
                'achternaam'                => '',
                'email'                     => '',
                'gebruikersnaam'            => '',
                'rol'                       => '',
                'mobiel'                    => '',
                'voornaam_err'              => '',
                'achternaam_err'            => '',
                'email_err'                 => '',
                'gebruikersnaam_err'        => '',
                'rol_err'                   => '',
                'wachtwoord_err'            => '',
                'wachtwoord_bevestigen_err' => '',
            ];
            $this->view('accounts/create', $data);
        }
    }
}

