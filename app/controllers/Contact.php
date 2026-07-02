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

        if ($_SESSION['feedback_flow'] === 'unhappy') {
            $_SESSION['feedback_fout'] = 'Geen connectie met database gevonden.';
            header('location:' . URLROOT . '/contact');
            exit;
        }

        $naam = trim($_POST['naam'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $onderwerp = trim($_POST['onderwerp'] ?? '');
        $bericht = trim($_POST['bericht'] ?? '');
        $opmerking = trim($_POST['opmerking'] ?? '') ?: null;

        if ($naam === '' || $email === '' || $onderwerp === '' || $bericht === '') {
            $_SESSION['feedback_fout'] = 'Vul alle verplichte velden in.';
            header('location:' . URLROOT . '/contact');
            exit;
        }

        $this->feedbackModel->create([
            'naam' => $naam,
            'email' => $email,
            'onderwerp' => $onderwerp,
            'bericht' => $bericht,
            'is_actief' => 1,
            'opmerking' => $opmerking
        ]);

        $_SESSION['feedback_succes'] = 'Je feedback is succesvol verzonden.';
        header('location:' . URLROOT . '/contact');
        exit;
    }
}