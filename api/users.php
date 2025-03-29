<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/auth.php';

// Apply CORS headers from middleware
addCorsHeaders();

$db = (new Database())->connect();
$userModel = new User($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $authData = authenticate(false); // Authenticate but don’t require admin
        if (isset($_GET['firebase_uid'])) {
            $user = $userModel->getByFirebaseUid($_GET['firebase_uid']);
            if ($user) {
                if ($user['firebase_uid'] === $authData['firebase_uid'] || $authData['role'] === 'admin') {
                    echo json_encode([$user]);
                } else {
                    http_response_code(403);
                    echo json_encode(['error' => 'Unauthorized: Cannot access other user data']);
                }
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
            }
        } else {
            authenticate(true); // Admin only for full list
            echo json_encode($userModel->getAll());
        }
        break;

    case 'POST':
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);
        error_log("Raw input: $rawInput"); // Debug to PHP error log
        if ($data === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON', 'raw_input' => $rawInput]);
            exit;
        }
        if (!isset($data['firebase_uid']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields', 'received_data' => $data]);
            exit;
        }
        if ($userModel->getByFirebaseUid($data['firebase_uid'])) {
            http_response_code(409);
            echo json_encode(['error' => 'User already exists']);
            exit;
        }
        $userId = $userModel->create($data);
        http_response_code(201);
        echo json_encode(['message' => 'User created', 'id' => $userId, 'role' => $data['role'] ?? 'user', 'received_data' => $data]);
        break;

    case 'PUT':
        $authData = authenticate();
        $firebaseUid = $authData['firebase_uid'];
        $data = json_decode(file_get_contents('php://input'), true);
        $updateData = array_filter([
            'name' => $data['name'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'address' => $data['address'] ?? null,
            'pincode' => $data['pincode'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'picture' => $data['picture'] ?? null, // Add picture to updateable fields
        ], function ($value) { return $value !== null; });
        if (!empty($updateData)) {
            $userModel->update($firebaseUid, $updateData);
            echo json_encode(['message' => 'User updated']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'No fields to update']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>