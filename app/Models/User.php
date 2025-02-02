<?php

namespace App\Models;

use PDO;
use Exception;
use PDOException;

class User extends BaseModel
{
    // Select Fields
    private const SELECT_FIELDS = 'id, name, email, created_at';

    // Check if email exists
    public function emailExists(string $email): bool
    {
        try {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
            $stmt->execute([':email' => $email]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error checking email existence: " . $e->getMessage());
            throw new Exception('Failed to check email existence');
        }
    }

    // Create User
    public function createUser(string $name, string $email, string $password): bool
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO users (name, email, password, created_at) 
                VALUES (:name, :email, :password, :created_at)
            ');
            
            return $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $password,
                ':created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            error_log("Create User Error: " . $e->getMessage());
            throw new Exception('Failed to create user');
        }
    }

    // Get User by Email
    public function getUserByEmail(string $email): ?array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT ' . self::SELECT_FIELDS . ', password 
                FROM users 
                WHERE email = :email
            ');
            
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            throw new Exception('Failed to fetch user');
        }
    }
}
