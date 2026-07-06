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
                if (strtolower($data['rol']) !== 'bezoeker') {
                    $data['email_err'] = 'Het opgegeven e-mailadres is al gekoppeld aan een andere medewerker. Gebruik een ander e-mailadres.';
                } else {
                    $data['email_err'] = 'Email is al in gebruik';
                }
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
                    $roleLower = strtolower($data['rol']);
                    if ($roleLower === 'admin' || $roleLower === 'medewerker' || $roleLower === 'receptie') {
                        header('Location: ' . URLROOT . '/medewerkers');
                    } else {
                        header('Location: ' . URLROOT . '/accounts');
                    }
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

    public function edit($id = null)
    {
        if (!isset($_SESSION['accountId'])) {
            header('Location: ' . URLROOT . '/auth');
            exit;
        }

        $userRole = strtolower($_SESSION['rolle'] ?? 'bezoeker');
        if ($userRole !== 'admin' && $userRole !== 'medewerker') {
            $_SESSION['error'] = 'You do not have permission to view this page';
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        if ($id === null) {
            header('Location: ' . URLROOT . '/accounts');
            exit;
        }

        $account = $this->accountModel->getAccountDetails($id);
        if (!$account) {
            $_SESSION['error'] = 'Account niet gevonden';
            header('Location: ' . URLROOT . '/accounts');
            exit;
        }

        // Role hierarchy check
        $loggedInId = (int)($_SESSION['accountId'] ?? 0);
        $targetId = (int)$id;
        $loggedInRole = $_SESSION['rolle'] ?? 'bezoeker';
        $targetRole = !empty($account->roles) ? explode(',', $account->roles)[0] : 'bezoeker';

        $loggedInRank = $this->getRoleRank($loggedInRole);
        $targetRank = $this->getRoleRank($targetRole);

        if ($loggedInId !== $targetId) {
            if ($loggedInRank < 3 && $targetRank >= $loggedInRank) {
                $_SESSION['error'] = 'Je hebt geen rechten om dit account te bewerken';
                header('Location: ' . URLROOT . '/accounts');
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'voornaam'      => trim($_POST['voornaam'] ?? ''),
                'tussenvoegsel' => trim($_POST['tussenvoegsel'] ?? ''),
                'achternaam'    => trim($_POST['achternaam'] ?? ''),
                'email'         => trim($_POST['email'] ?? ''),
                'gebruikersnaam'=> trim($_POST['gebruikersnaam'] ?? ''),
                'rol'           => trim($_POST['rol'] ?? ''),
                'mobiel'        => trim($_POST['mobiel'] ?? ''),
                'is_actief'     => isset($_POST['is_actief']) ? (int) $_POST['is_actief'] : (int) ($account->is_actief ?? 1),
                'wachtwoord'    => trim($_POST['wachtwoord'] ?? ''),
                'wachtwoord_bevestigen' => trim($_POST['wachtwoord_bevestigen'] ?? ''),
                'voornaam_err'      => '',
                'achternaam_err'    => '',
                'email_err'         => '',
                'gebruikersnaam_err'=> '',
                'rol_err'           => '',
                'wachtwoord_err'    => '',
                'wachtwoord_bevestigen_err' => '',
            ];

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

            // Optional password validation
            if (!empty($data['wachtwoord']) || !empty($data['wachtwoord_bevestigen'])) {
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
            }

            if (empty($data['email_err']) && $this->accountModel->checkEmailInUseForEdit($data['email'], $id)) {
                $data['email_err'] = 'Email is al in gebruik';
            }

            if (empty($data['gebruikersnaam_err']) && $this->accountModel->usernameExistsForEdit($data['gebruikersnaam'], $id)) {
                $data['gebruikersnaam_err'] = 'Gebruikersnaam is al in gebruik';
            }

            $hasErrors =
                $data['voornaam_err'] || $data['achternaam_err'] ||
                $data['email_err'] || $data['gebruikersnaam_err'] ||
                $data['rol_err'] || $data['wachtwoord_err'] ||
                $data['wachtwoord_bevestigen_err'];

            if (!$hasErrors) {
                if ($this->accountModel->updateAccount($id, $data)) {
                    // Update password if provided
                    if (!empty($data['wachtwoord'])) {
                        $this->accountModel->updatePassword($id, $data['wachtwoord']);
                    }

                    $_SESSION['success'] = 'Account succesvol bijgewerkt';

                    if ((int) $_SESSION['accountId'] === (int) $id) {
                        $_SESSION['firstName'] = $data['voornaam'];
                        $_SESSION['lastName'] = $data['achternaam'];
                        $_SESSION['email'] = $data['gebruikersnaam'];
                        $_SESSION['rolle'] = $data['rol'];
                    }

                    header('Location: ' . URLROOT . '/accounts');
                    exit;
                }

                $_SESSION['error'] = 'Er is iets misgegaan bij het bijwerken van het account';
                header('Location: ' . URLROOT . '/accounts');
                exit;
            }

            $data['title'] = 'Account Bewerken';
            $data['id'] = $id;
            $this->view('accounts/edit', $data);
            return;
        }

        $data = [
            'title' => 'Account Bewerken',
            'id' => $id,
            'voornaam' => $account->voornaam ?? '',
            'tussenvoegsel' => $account->tussenvoegsel ?? '',
            'achternaam' => $account->achternaam ?? '',
            'email' => $account->email ?? ($account->gebruikersnaam ?? ''),
            'gebruikersnaam' => $account->gebruikersnaam ?? '',
            'rol' => !empty($account->roles) ? explode(',', $account->roles)[0] : '',
            'mobiel' => $account->mobiel ?? '',
            'is_actief' => (int) ($account->is_actief ?? 1),
            'wachtwoord' => '',
            'wachtwoord_bevestigen' => '',
            'voornaam_err' => '',
            'achternaam_err' => '',
            'email_err' => '',
            'gebruikersnaam_err' => '',
            'rol_err' => '',
            'wachtwoord_err' => '',
            'wachtwoord_bevestigen_err' => '',
        ];

        $this->view('accounts/edit', $data);
    }

    public function delete($id = null)
    {
        if (!isset($_SESSION['accountId'])) {
            header('Location: ' . URLROOT . '/auth');
            exit;
        }

        $userRole = strtolower($_SESSION['rolle'] ?? 'bezoeker');
        if ($userRole !== 'admin' && $userRole !== 'medewerker') {
            $_SESSION['error'] = 'You do not have permission to view this page';
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        if ($id === null) {
            header('Location: ' . URLROOT . '/accounts');
            exit;
        }

        if ((int) $_SESSION['accountId'] === (int) $id) {
            $_SESSION['error'] = 'Je kunt je eigen account niet verwijderen';
            header('Location: ' . URLROOT . '/accounts');
            exit;
        }

        $account = $this->accountModel->getAccountDetails($id);
        if (!$account) {
            $_SESSION['error'] = 'Account niet gevonden';
            header('Location: ' . URLROOT . '/accounts');
            exit;
        }

        $loggedInRole = $_SESSION['rolle'] ?? 'bezoeker';
        $targetRole = !empty($account->roles) ? explode(',', $account->roles)[0] : 'bezoeker';

        $loggedInRank = $this->getRoleRank($loggedInRole);
        $targetRank = $this->getRoleRank($targetRole);

        if ($loggedInRank < 3 && $targetRank >= $loggedInRank) {
            $_SESSION['error'] = 'Je hebt geen rechten om dit account te verwijderen';
            header('Location: ' . URLROOT . '/accounts');
            exit;
        }

        if ($this->accountModel->deleteAccount($id)) {
            $_SESSION['success'] = 'Account succesvol verwijderd';
        } else {
            $_SESSION['error'] = 'Account kon niet worden verwijderd';
        }

        header('Location: ' . URLROOT . '/accounts');
        exit;
    }

    private function getRoleRank($role)
    {
        $role = strtolower(trim($role));
        if ($role === 'admin' || $role === 'administrator') {
            return 3;
        }
        if ($role === 'medewerker') {
            return 2;
        }
        if ($role === 'receptie') {
            return 1;
        }
        return 0; // Bezoeker or any other role
    }
}

