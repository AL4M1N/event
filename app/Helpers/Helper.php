<?php

namespace App\Helpers;

class Helper
{
    // Generate base URL for the application
    public static function baseUrl(string $path = ''): string
    {
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/public');
        return $basePath . '/' . trim($path, '/');
    }

    // Send JSON response
    public static function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Check if the request is POST
    public static function isPostRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    // Render a view file
    public static function view(string $path, array $data = []): void
    {
        $viewPath = realpath(dirname(__DIR__) . '/Views/' . $path);
        
        if (!is_readable($viewPath)) {
            throw new \RuntimeException("View file not found or not readable: {$path}");
        }
        
        extract($data);
        require_once $viewPath;
    }
}
