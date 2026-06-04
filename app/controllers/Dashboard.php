<?php

class Dashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['accountId'])) {
            header('location:' . URLROOT . '/auth');
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
