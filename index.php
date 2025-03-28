<?php
require_once 'middleware/auth.php';

addCorsHeaders();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

$routes = [
    'api/upload-image' => 'api/upload-image.php',
    'api/auth' => 'api/auth.php',
    'api/rooms' => 'api/rooms.php',
    'api/facilities' => 'api/facilities.php',
    'api/bookings' => 'api/bookings.php',
    'api/users' => 'api/users.php',
    'api/reviews' => 'api/reviews.php',
    'api/features' => 'api/features.php',
    'api/carousel' => 'api/carousel.php',
    'api/settings' => 'api/settings.php',
];

$pathParts = explode('/', $uri);
$baseRoute = count($pathParts) >= 2 ? implode('/', array_slice($pathParts, 0, 2)) : $uri;

if (array_key_exists($baseRoute, $routes)) {
    if (!isset($_SERVER['PATH_INFO'])) {
        $_SERVER['PATH_INFO'] = '/' . $uri; // e.g., "/api/rooms/4"
    }
    require_once __DIR__ . '/' . $routes[$baseRoute];
} else {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Not Found']);
}
?>