<?php
/**
 * De database verbindingsgegevens
 * We use getenv() to pull settings from Docker
 */
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'mvc_project');
define('DB_USER', getenv('DB_USER') ?: 'dev_user');
define('DB_PASS', getenv('DB_PASS') ?: 'dev_password');

/**
 * De naam van de virtualhost
 * Locally this is localhost. On Unraid/Production, 
 * you can change this in your .env file later.
 */
define('URLROOT', getenv('URLROOT') ?: 'http://localhost');

/**
 * Het pad naar de folder app
 */
define('APPROOT', dirname(dirname(__FILE__)));