<?php

class Test extends BaseController {
    private $gebruikerModel;

    public function __construct() {
        parent::__construct();
        // Load the model to test database connection
        $this->gebruikerModel = $this->model('Gebruiker');
    }

    /**
     * The default method: /test
     */
    public function index() {
        echo "<h1>Test Controller & Role Switcher</h1>";
        echo "<ul>";
        echo "<li><a href='/test/session/admin'>1. Login as ADMIN (User ID 1)</a></li>";
        echo "<li><a href='/test/session/customer'>2. Login as CUSTOMER (User ID 3)</a></li>";
        echo "<li><a href='/test/sessionStatus'>3. Check Session Status</a></li>";
        echo "<li><a href='/test/logout'>4. Logout / Clear Session</a></li>";
        echo "</ul>";
        
        echo "<hr><h3>Quick Links:</h3>";
        echo "<a href='/admintickets'>Go to Admin Dashboard</a> | ";
        echo "<a href='/usertickets/mytickets'>Go to My User Tickets</a>";

        echo "<hr><h3>Database Connection Test:</h3>";
        try {
            $users = $this->gebruikerModel->getAll();
            echo "Successfully connected! Users in DB: " . count($users);
        } catch (Exception $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }

    /**
     * Unified Session Creator
     * URL: /test/session/admin OR /test/session/customer
     */
    public function session($role = 'customer') {
        if ($role === 'admin') {
            $_SESSION['user_id'] = 1; // John Doe from our SQL script
            $_SESSION['user_email'] = 'admin@test.com';
            $_SESSION['user_role'] = 'admin';
            $msg = "Logged in as ADMIN (User 1)";
        } else {
            $_SESSION['user_id'] = 3; // Jane Doe from our SQL script
            $_SESSION['user_email'] = 'customer@test.com';
            $_SESSION['user_role'] = 'customer';
            $msg = "Logged in as CUSTOMER (User 3)";
        }
        
        echo "<h2>$msg</h2>";
        echo "<a href='/test/index'>Back to Test Index</a>";
    }

    public function logout() {
        session_destroy();
        session_start();
        $_SESSION['info'] = 'Session cleared';
        header("Location: /test/index");
        exit;
    }

    public function sessionStatus() {
        echo '<pre><h2>Current Session Data:</h2>';
        if(isset($_SESSION)) {
            print_r($_SESSION);
        } else {
            echo "No session active.";
        }
        echo '<hr><a href="/test/index">Back to Index</a></pre>';
    }
}