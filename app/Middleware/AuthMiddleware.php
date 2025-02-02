<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the user is authenticated
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            header('Location: ' . base_url('login'));
            exit;
        }
    }
}