<?php

class Test extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        echo "<h1>Test Role Switcher</h1>";
        echo "<ul>";
        echo "<li><a href='/test/session/admin'>1. Login as ADMIN & Go to Dashboard</a></li>";
        echo "<li><a href='/test/session/customer'>2. Login as CUSTOMER & Go to My Tickets</a></li>";
        echo "<li><a href='/test/logout'>3. Logout / Clear</a></li>";
        echo "</ul>";
    }

    public function session($role = 'customer') {
        // Clear old session data first
        session_unset();

        if ($role === 'admin') {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_role'] = 'admin';
            // Automatically redirect to the admin page
            header("Location: /admintickets/index");
        } else {
            $_SESSION['user_id'] = 3;
            $_SESSION['user_role'] = 'customer';
            // Automatically redirect to the user page
            header("Location: /usertickets/mytickets");
        }
        exit;
    }

    public function logout() {
        session_destroy();
        header("Location: /test/index");
        exit;
    }
}