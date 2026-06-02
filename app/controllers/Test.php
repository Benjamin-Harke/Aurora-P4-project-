<?php

class Test extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Set up a test session for development/testing
     * This allows testing the ticket features before login is implemented
     * 
     * IMPORTANT: Remove this controller before production!
     */
    public function session() {
        // Create a test session with customer user ID 3 (created in sample-data.php)
        // This user has test tickets assigned to them
        $_SESSION['user_id'] = 3;  // Customer user from sample data (John Doe)
        $_SESSION['user_email'] = 'customer@test.com';
        $_SESSION['user_role'] = 'customer';
        
        $_SESSION['success'] = 'Test session created! You can now access ticket features.';
        redirect('usertickets/mytickets');
    }

    /**
     * Clear the test session
     */
    public function logout() {
        session_destroy();
        $_SESSION['info'] = 'Session cleared';
        redirect('homepages');
    }

    /**
     * Show current session data (for debugging)
     */
    public function sessionStatus() {
        echo '<pre>';
        echo '<h2>Current Session Data:</h2>';
        print_r($_SESSION);
        echo '<hr>';
        echo '<h3>Actions:</h3>';
        echo '<a href="/test/session">Create Test Session</a> | ';
        echo '<a href="/test/logout">Logout</a> | ';
        echo '<a href="/usertickets/mytickets">View My Tickets</a>';
        echo '</pre>';
    }
}
