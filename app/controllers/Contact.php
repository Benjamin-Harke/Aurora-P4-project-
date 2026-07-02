<?php

class Contact extends BaseController
{
    private $feedbackModel;

    public function __construct()
    {
        $this->feedbackModel = $this->model('Feedback');
    }

    public function index()
    {
        if (!isset($_SESSION['feedback_flow'])) {
            $_SESSION['feedback_flow'] = 'happy';
        }

        $data = [
            'title' => 'Contact',
            'feedback_flow' => $_SESSION['feedback_flow']
        ];

        $this->view('includes/contact', $data);
    }

    public function happy()
    {
        $_SESSION['feedback_flow'] = 'happy';
        unset($_SESSION['feedback_fout']);
        unset($_SESSION['feedback_succes']);

        header('location:' . URLROOT . '/contact');
        exit;
    }

    public function unhappy()
    {
        $_SESSION['feedback_flow'] = 'unhappy';
        unset($_SESSION['feedback_fout']);
        unset($_SESSION['feedback_succes']);

        header('location:' . URLROOT . '/contact');
        exit;
    }

    public function opslaan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('location:' . URLROOT . '/contact');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $onderwerp = trim($_POST['onderwerp'] ?? '');
        $bericht = trim($_POST['bericht'] ?? '');

        if ($email === '' || $onderwerp === '' || $bericht === '') {
            $_SESSION['feedback_fout'] = 'Vul alle verplichte velden in.';
            header('location:' . URLROOT . '/contact');
            exit;
        }

        if ($_SESSION['feedback_flow'] === 'unhappy') {
            try {
                $pdo = new PDO(
                    'mysql:host=' . DB_HOST . ';dbname=unhappymeldingen',
                    DB_USER,
                    DB_PASS
                );

                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->prepare("
            INSERT INTO feedback (email, onderwerp, bericht)
            VALUES (:email, :onderwerp, :bericht)
        ");

                $stmt->execute([
                    ':email' => $email,
                    ':onderwerp' => $onderwerp,
                    ':bericht' => $bericht
                ]);

            } catch (PDOException $e) {
                $_SESSION['feedback_fout'] = 'Momenteel niet beschikbaar. Geen verbinding met de database gevonden.';
                header('location:' . URLROOT . '/contact');
                exit;
            }

            $_SESSION['feedback_fout'] = 'Momenteel niet beschikbaar. Geen verbinding met de database gevonden.';
            header('location:' . URLROOT . '/contact');
            exit;
        }

        $this->feedbackModel->create([
            'email' => $email,
            'onderwerp' => $onderwerp,
            'bericht' => $bericht
        ]);

        $_SESSION['feedback_succes'] = 'Je feedback is succesvol verzonden.';
        header('location:' . URLROOT . '/contact');
        exit;
    }
}