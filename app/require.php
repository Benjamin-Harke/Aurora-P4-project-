<?php
    /**
     * Start session for authentication
     */
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    require_once 'config/config.php';
    
    // ADD THIS LINE BELOW:
    require_once 'helpers/url_helper.php'; 
    
    require_once 'libraries/Core.php';
    require_once 'libraries/BaseController.php';
    require_once 'libraries/Database.php';

    $init = new Core();