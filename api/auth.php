<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;

$db = (new Database())->connect();
$userModel = new User($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['firebase_uid']) && isset($data['name']) && isset($data['email']) && isset($data['phone_number'])) {
        $existingUser = $userModel->getByFirebaseUid($data['firebase_uid']);
        if (!$existingUser) {
            $userModel->create($data);
        }
        $payload = [
            'user_id' => $data['firebase_uid'],
            'is_admin' => false, // Set true for admin users
            'exp' => time() + 3600 // 1 hour expiry
        ];
        $jwt = JWT::encode($payload, 'your-secret-key', 'HS256'); // Replace with secure key
        echo json_encode(['token' => $jwt]);
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid data']);
    }
}

?>