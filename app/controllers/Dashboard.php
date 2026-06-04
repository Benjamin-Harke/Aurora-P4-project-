<?php

class Dashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['accountId'])) {
            $_SESSION['showLoginModal'] = true;
            header('Location: ' . URLROOT);
            return;
        }

        $data = [
            'title' => 'Dashboard',
            'firstName' => $_SESSION['firstName'],
            'lastName' => $_SESSION['lastName'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['rolle'] ?? 'bezoeker'
        ];

        $this->view('dashboard/index', $data);
    }
}
