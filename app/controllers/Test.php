<?php

class Test extends BaseController {
    // Declaring the property fixes the "Deprecated" error
    private $gebruikerModel;

    public function __construct() {
        parent::__construct();
        // This will test if your Gebruiker model is working
        $this->gebruikerModel = $this->model('Gebruiker');
    }

    public function index() {
        echo "<h1>Test Controller is Active</h1>";
        echo "<ul>";
        echo "<li><a href='/test/session'>1. Create Test Session</a></li>";
        echo "<li><a href='/test/sessionStatus'>2. Check Session Data</a></li>";
        echo "<li><a href='/test/logout'>3. Logout</a></li>";
        echo "</ul>";
        
        echo "<hr><h3>Database Connection Test (ERD Check):</h3>";
        try {
            // This is the line that triggers the "Table not found" error
            $users = $this->gebruikerModel->getAll();
            
            echo "<b style='color:green'>Success! Table 'gebruiker' exists and is reachable.</b><br>";
            echo "Found " . count($users) . " users.<br>";
            echo "<pre>";
            print_r($users);
            echo "</pre>";
        } catch (PDOException $e) {
            echo "<b style='color:red'>Database Error:</b> " . $e->getMessage();
            echo "<br><br><i>Tip: Make sure you ran the SQL script in your database manager for the 'mvc_project' database.</i>";
        }
    }

    public function session() {
        $_SESSION['user_id'] = 3; 
        $_SESSION['user_email'] = 'customer@test.com';
        $_SESSION['user_role'] = 'customer';
        echo "Test session created! <a href='/test/sessionStatus'>Check it here</a>";
    }

    public function logout() {
        session_destroy();
        header("Location: /test/index");
        exit;
    }

    public function sessionStatus() {
        echo '<pre><h2>Current Session Data:</h2>';
        print_r($_SESSION);
        echo '<hr><a href="/test/index">Back to Index</a></pre>';
    }
}