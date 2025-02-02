<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Helpers\Helper;

class RegisterController
{
    private User $userModel;

    // Pagination and Validation
    private const MINIMUM_NAME_LENGTH = 3;
    private const MAXIMUM_NAME_LENGTH = 50;
    private const MINIMUM_PASSWORD_LENGTH = 6;
    private const REQUIRED_FIELDS = ['name', 'email', 'password', 'confirm_password'];

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    // Register View
    public function index()
    {
        if (Helper::isPostRequest()) {
            return $this->handleRegistration();
        } else {
            return Helper::view('frontend/register.php');
        }
    }

    // Handle Registration
    private function handleRegistration()
    {
        // Sanitize the form data
        $formData = $this->sanitizeRegistrationData($_POST);
        // Validate the form data
        $errors = $this->validateRegistrationData($formData);

        // If there are errors, return the form with errors
        if (!empty($errors)) {
            return Helper::view('frontend/register.php', [
                'errors' => $errors,
                'formData' => $formData
            ]);
        }

        // Try to create the user
        try {
            // Check if the email already exists
            if ($this->userModel->emailExists($formData['email'])) {
                return Helper::view('frontend/register.php', [
                    'errors' => ['email' => 'Email already exists.'],
                    'formData' => $formData
                ]);
            }

            // If the user is created successfully, return the form with success message
            if ($this->createUser($formData)) {
                return Helper::view('frontend/register.php', [
                    'success' => 'Registration successful! You can now <a href="' . Helper::baseUrl('login') . '">log in</a>.',
                    'formData' => $formData
                ]);
            }

            // If the user is not created, throw an exception
            throw new \Exception('Failed to create user account.');

        } catch (\Exception $e) {
            // If there is an error, return the form with error message
            return Helper::view('frontend/register.php', [
                'error' => 'An error occurred while creating your account.',
                'formData' => $formData
            ]);
        }
    }

    // Create User
    private function createUser(array $formData): bool
    {
        // Hash the password
        $hashedPassword = password_hash($formData['password'], PASSWORD_BCRYPT);
        // Create the user
        return $this->userModel->createUser(
            $formData['name'],
            $formData['email'],
            $hashedPassword
        );
    }

    // Sanitize Registration Data
    private function sanitizeRegistrationData(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'email' => filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'password' => $data['password'] ?? '',
            'confirm_password' => $data['confirm_password'] ?? ''
        ];
    }

    // Validate Registration Data
    private function validateRegistrationData(array $data): array
    {
        $errors = [];

        // Check required fields
        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }

        // Name validation
        if (strlen($data['name']) < self::MINIMUM_NAME_LENGTH || 
            strlen($data['name']) > self::MAXIMUM_NAME_LENGTH) {
            $errors['name'] = sprintf(
                'Name must be between %d and %d characters.',
                self::MINIMUM_NAME_LENGTH,
                self::MAXIMUM_NAME_LENGTH
            );
        }

        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        // Password validation
        if (!empty($data['password'])) {
            if (strlen($data['password']) < self::MINIMUM_PASSWORD_LENGTH) {
                $errors['password'] = sprintf(
                    'Password must be at least %d characters long.',
                    self::MINIMUM_PASSWORD_LENGTH
                );
            }

            if ($data['password'] !== $data['confirm_password']) {
                $errors['confirm_password'] = 'Passwords do not match.';
            }
        }

        return $errors;
    }
}
