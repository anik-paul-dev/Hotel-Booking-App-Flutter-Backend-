<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Query.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();
$queryModel = new Query($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        authenticate(true); // Admin only
        echo json_encode($queryModel->getAll());
        break;
    case 'POST':
        // No authentication required for public submission
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['name'], $data['email'], $data['phone'], $data['subject'], $data['message'], $data['date'], $data['is_read'])) {
            $id = $queryModel->create(
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['subject'],
                $data['message'],
                $data['date'],
                $data['is_read']
            );
            http_response_code(201);
            echo json_encode(['id' => $id, 'message' => 'Query submitted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid request: Missing required fields']);
        }
        break;
    case 'PUT':
        authenticate(true); // Admin only
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id']) && isset($data['is_read'])) {
            $queryModel->markAsRead($data['id'], $data['is_read']);
            echo json_encode(['message' => 'Query status updated']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid request']);
        }
        break;
    case 'DELETE':
        authenticate(true); // Admin only
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            $queryModel->delete($data['id']);
            echo json_encode(['message' => 'Query deleted']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid request']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
?>