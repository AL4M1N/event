<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Helpers\Helper;

class LoginController
{
    private $userModel;
    
    // Pagination and Validation
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const REQUIRED_FIELDS = ['email', 'password', 'csrf_token'];

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    // Login View
    public function index()
    {
        if (Helper::isPostRequest()) {
            return $this->handleLogin();
        } else {
            return Helper::view('frontend/login.php');
        }
    }

    // Handle Login
    public function handleLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $formData = $this->sanitizeLoginData($_POST);
        $errors = $this->validateLoginData($formData);

        // If there are errors, return the form with errors
        if (!empty($errors)) {
            return Helper::view('frontend/login.php', [
                'errors' => $errors,
                'formData' => $formData
            ]);
        }

        // Try to authenticate the user
        try {
            
            // If the user is authenticated, set the user session and redirect to the dashboard
            if ($this->authenticateUser($formData)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $this->resetLoginAttempts();
                header('Location: /dashboard');
                exit;
            }
            
            $this->handleFailedLogin();
            
        } catch (\Exception $e) {
            // Handle Error
            return Helper::view('frontend/login.php', [
                'error' => 'An error occurred during login: ' . $e->getMessage(),
                'formData' => $formData 
            ]);
        }
    }

    // Logout
    public function logout()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }

    // Authenticate User
    private function authenticateUser(array $formData): bool
    {
        $user = $this->userModel->getUserByEmail($formData['email']);
        
        if ($user && password_verify($formData['password'], $user['password'])) {
            $this->setUserSession($user);
            return true;
        }
        
        return false;
    }

    // Handle Failed Login
    private function handleFailedLogin(): void
    {
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        
        $error = $_SESSION['login_attempts'] >= self::MAX_LOGIN_ATTEMPTS
            ? 'Too many failed attempts. Please try again later.'
            : 'Invalid email or password.';

        Helper::view('frontend/login.php', ['error' => $error]);
    }

    // Sanitize Login Data
    private function sanitizeLoginData(array $data): array
    {
        return [
            'email' => filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'password' => trim($data['password'] ?? ''),
            'csrf_token' => $data['csrf_token'] ?? ''
        ];
    }

    // Validate Login Data
    private function validateLoginData(array $data): array
    {
        $errors = [];

        // Check required fields
        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }

        // CSRF validation
        if (!verifyCsrfToken($data['csrf_token'])) {
            $errors['csrf'] = 'Invalid CSRF token. Please try again.';
        }

        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        return $errors;
    }

    // Set User Session
    private function setUserSession(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
    }

    // Reset Login Attempts
    private function resetLoginAttempts(): void
    {
        if (isset($_SESSION['login_attempts'])) {
            unset($_SESSION['login_attempts']);
        }
    }
} 