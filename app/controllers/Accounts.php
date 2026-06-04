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
            // Convert objects to arrays and parse roles
            return array_map(function($obj) {
                $arr = (array) $obj;
                // Parse comma-separated roles into an array
                $arr['roles'] = $arr['roles'] ? explode(',', $arr['roles']) : [];
                return $arr;
            }, $users);
        }
        
        return [];
    }
}
