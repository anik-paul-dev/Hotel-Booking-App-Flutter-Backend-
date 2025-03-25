<?php
require_once __DIR__ . '/../models/User.php'; // Include User model for database access

function addCorsHeaders() {
    // Define allowed origins
    $allowedOrigins = [
        'http://localhost:64944', // Development (Flutter web)
        // 'https://yourapp.com',  // Add production URL as needed
    ];

    // Check if the request origin is allowed
    $requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedOrigin = in_array($requestOrigin, $allowedOrigins) ? $requestOrigin : $allowedOrigins[0];

    header("Access-Control-Allow-Origin: $allowedOrigin");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

function authenticate($adminOnly = false) {
    global $db; // Assumes $db is available; adjust if needed
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    
    if (preg_match('/Bearer\s(\S+)/', $token, $matches)) {
        $jwt = $matches[1];
        // Split JWT into header, payload, signature
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token format']);
            exit;
        }
        // Decode payload
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        if (!isset($payload['sub'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Token missing sub claim']);
            exit;
        }
        $firebaseUid = $payload['sub'];
        
        // Fetch user role from database
        $userModel = new User($db);
        $user = $userModel->getByFirebaseUid($firebaseUid);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            exit;
        }
        $role = $user['role'] ?? 'user';
        
        $userData = [
            'firebase_uid' => $firebaseUid,
            'role' => $role,
        ];
        
        if ($adminOnly && $userData['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Admin access required']);
            exit;
        }
        return $userData;
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Authorization header missing or invalid']);
        exit;
    }
}

addCorsHeaders();