<!-- RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L] -->


<!-- <?php
require_once 'middleware/auth.php';

// Apply CORS headers from middleware (removes redundancy)
addCorsHeaders();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

$routes = [
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

if (array_key_exists($uri, $routes)) {
    require_once __DIR__ . '/' . $routes[$uri];
} else {
    http_response_code(404);
    header('Content-Type: application/json'); // Ensure JSON response
    echo json_encode(['message' => 'Not Found']);
}
?> -->