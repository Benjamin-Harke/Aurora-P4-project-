<?php

class Auth extends BaseController
{
    private $accountModel;

    public function __construct()
    {
        $this->accountModel = $this->model('Account');
    }

    /**
     * Show login/register page (or redirect if already logged in)
     */
    public function index()
    {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['accountId'])) {
            header('location:' . URLROOT . '/dashboard');
            return;
        }

        // Redirect to home - login/register is available via modals in header
        header('location:' . URLROOT);
    }

    /**
     * Handle login form submission
     */
    public function login()
    {
        // Only handle POST requests
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->index();
            return;
        }

        // Get POST data
        $email = trim($_POST['login_email'] ?? '');
        $password = trim($_POST['login_password'] ?? '');

        // Validate input
        if (empty($email)) {
            $_SESSION['error'] = 'Please enter your email';
            header('location:' . URLROOT . '/auth');
            return;
        }

        if (empty($password)) {
            $_SESSION['error'] = 'Please enter your password';
            header('location:' . URLROOT . '/auth');
            return;
        }

        // Attempt login
        $account = $this->accountModel->login($email, $password);

        if ($account) {
            // Set session
            $_SESSION['accountId'] = $account->Id;
            $_SESSION['email'] = $account->Email;
            $_SESSION['firstName'] = $account->FirstName;
            $_SESSION['lastName'] = $account->LastName;

            header('location:' . URLROOT . '/dashboard');
        } else {
            $_SESSION['error'] = 'Invalid email or password';
            header('location:' . URLROOT . '/auth');
        }
    }

    /**
     * Handle register form submission
     */
    public function register()
    {
        // Only handle POST requests
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->index();
            return;
        }

        // Get POST data
        $email = trim($_POST['register_email'] ?? '');
        $password = trim($_POST['register_password'] ?? '');
        $passwordConfirm = trim($_POST['register_password_confirm'] ?? '');
        $firstName = trim($_POST['register_firstname'] ?? '');
        $lastName = trim($_POST['register_lastname'] ?? '');

        // Validate input
        if (empty($email)) {
            $_SESSION['error'] = 'Please enter your email';
            header('location:' . URLROOT . '/auth');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email';
            header('location:' . URLROOT . '/auth');
            return;
        }

        if (empty($password)) {
            $_SESSION['error'] = 'Please enter a password';
            header('location:' . URLROOT . '/auth');
            return;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters';
            header('location:' . URLROOT . '/auth');
            return;
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = 'Passwords do not match';
            header('location:' . URLROOT . '/auth');
            return;
        }

        if (empty($firstName)) {
            $_SESSION['error'] = 'Please enter your first name';
            header('location:' . URLROOT . '/auth');
            return;
        }

        if (empty($lastName)) {
            $_SESSION['error'] = 'Please enter your last name';
            header('location:' . URLROOT . '/auth');
            return;
        }

        // Check if email already exists
        if ($this->accountModel->emailExists($email)) {
            $_SESSION['error'] = 'Email is already registered';
            header('location:' . URLROOT . '/auth');
            return;
        }

        // Attempt to register
        if ($this->accountModel->register($email, $password, $firstName, $lastName)) {
            // Log the user in after registration
            $account = $this->accountModel->login($email, $password);

            if ($account) {
                // Set session
                $_SESSION['accountId'] = $account->Id;
                $_SESSION['email'] = $account->Email;
                $_SESSION['firstName'] = $account->FirstName;
                $_SESSION['lastName'] = $account->LastName;

                header('location:' . URLROOT . '/dashboard');
            }
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            header('location:' . URLROOT . '/auth');
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        // Destroy session
        session_destroy();
        header('location:' . URLROOT);
    }
}
