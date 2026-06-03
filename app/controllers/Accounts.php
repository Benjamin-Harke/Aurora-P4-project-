<?php

class Accounts extends BaseController
{
    private $accountModel;

    public function __construct()
    {
        $this->accountModel = $this->model('Account');
    }

    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['accountId'])) {
            header('Location: ' . URLROOT . '/auth');
            exit;
        }

        // Get all accounts from database
        $accounts = $this->getAllAccounts();

        $data = [
            'title' => 'Account Overview',
            'accounts' => $accounts
        ];

        $this->view('accounts/index', $data);
    }

    private function getAllAccounts()
    {
        $db = new Database();
        
        $query = "SELECT Id, Email, FirstName, LastName, PhoneNumber, CreatedAt, IsActive 
                  FROM Accounts 
                  ORDER BY CreatedAt DESC";
        
        $db->query($query);
        $results = $db->resultSet();
        
        if ($results && count($results) > 0) {
            // Convert objects to arrays
            return array_map(function($obj) {
                return (array) $obj;
            }, $results);
        }
        
        return [];
    }
}
