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












-- Create the `settings` table
CREATE TABLE `settings` (
    `id` INT PRIMARY KEY DEFAULT 1, -- Fixed ID of 1 as per settings.php logic
    `website_title` VARCHAR(255) NOT NULL DEFAULT '',
    `about_us_description` TEXT NOT NULL DEFAULT '',
    `shutdown_mode` TINYINT NOT NULL DEFAULT 0, -- 0 or 1 for boolean-like flag
    `contacts` TEXT NOT NULL DEFAULT '[]', -- JSON-encoded array of contact objects
    `social_media_links` TEXT NOT NULL DEFAULT '[]', -- JSON-encoded array of social media objects
    `team_members` TEXT NOT NULL DEFAULT '[]', -- JSON-encoded array of team member objects
    `website_name` VARCHAR(255) NOT NULL DEFAULT '',
    `phone_number` VARCHAR(50) NOT NULL DEFAULT '',
    `email` VARCHAR(255) NOT NULL DEFAULT '',
    `address` TEXT NOT NULL DEFAULT '',
    `facebook` VARCHAR(255) NOT NULL DEFAULT '',
    `twitter` VARCHAR(255) NOT NULL DEFAULT '',
    `instagram` VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a default row if needed (optional, since settings.php handles defaults)
INSERT INTO `settings` (`id`) VALUES (1)
ON DUPLICATE KEY UPDATE `id` = 1;