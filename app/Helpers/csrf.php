<?php

// Generate CSRF Token
function generateCsrfToken()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Check if the CSRF token is empty
    if (empty($_SESSION['csrf_token'])) {
        // Generate a new CSRF token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF Token
function verifyCsrfToken($token)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if the CSRF token is set and if it matches the token being verified
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}