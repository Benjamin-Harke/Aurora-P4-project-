<?php
/**
 * Simple page redirection
 */
function redirect($page) {
    header('location: ' . URLROOT . '/' . $page);
}

function generateSecureBarcode($length = 10) {
    // Generates a random, hard-to-guess string
    return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
}