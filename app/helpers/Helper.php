<?php

/**
 * Helper functions for the MVC Framework
 */

/**
 * Redirect to a specific route
 * 
 * @param string $route The route to redirect to (e.g., 'publictickets', 'homepages/index')
 */
function redirect($route = '') {
    header('location: ' . URLROOT . '/' . $route);
    exit;
}
