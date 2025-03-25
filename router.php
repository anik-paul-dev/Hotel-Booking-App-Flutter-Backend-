<?php
// backend/router.php

// Get the requested URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the request is for an existing file (e.g., a static asset), serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Let the built-in server handle it
}

// Include middleware to apply CORS headers before routing
require_once __DIR__ . '/middleware/auth.php';
addCorsHeaders();

// Route everything through index.php
require_once __DIR__ . '/index.php';