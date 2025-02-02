<?php

namespace App\Controllers\Admin;

use App\Helpers\Helper;
class DashboardController
{
    // Dashboard View
    public function index()
    {
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . base_url('login'));
            exit;
        }

        // Get the user details
        $userId = $_SESSION['user_id'];
        $name = $_SESSION['name'];
        $email = $_SESSION['email'];

        // Return the dashboard view
        return Helper::view('backend/dashboard.php', [
            'name' => $name,
            'email' => $email
        ]);
    }
}