<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../app/routes.php';

// Get the request URI and base path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);


// Check for matching route
$matchedRoute = false;
foreach ($routes as $route => $handler) {
    $matches = matchRoute($route, $requestUri);
    if ($matches !== false) {
        $matchedRoute = true;
        if (!empty($matches)) {
            // If there are parameters, pass them to the handler
            $handler(...$matches);
        } else {
            // If no parameters, just call the handler
            $handler();
        }
        break;
    }
}

// Function to match route patterns with parameters
function matchRoute($routePattern, $requestUri) {
    // Convert route pattern to regex
    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePattern);
    $pattern = '@^' . $pattern . '$@D';
    
    if (preg_match($pattern, $requestUri, $matches)) {
        array_shift($matches); // Remove the full match
        return $matches;
    }
    return false;
}

// If no route matched, show 404
if (!$matchedRoute) {
    http_response_code(404);
    echo 'Page not found';
}

