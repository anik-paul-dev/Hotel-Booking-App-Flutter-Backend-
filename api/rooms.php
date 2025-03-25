<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Room.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();
$roomModel = new Room($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $all = isset($_GET['all']) && $_GET['all'] === 'true';
        if ($all) {
            authenticate(true); // Admin only for all rooms
            echo json_encode($roomModel->getAllRooms()); // Fetch all rooms
        } else {
            echo json_encode($roomModel->getAll()); // Fetch only active rooms
        }
        break;
    case 'POST':
        authenticate(true); // Admin only
        $data = json_decode(file_get_contents('php://input'), true);
        $roomId = $roomModel->create($data);
        http_response_code(201);
        echo json_encode(['id' => $roomId]);
        break;
    case 'PUT':
        authenticate(true); // Admin only
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id'])) {
            if (isset($data['is_active']) && count($data) == 2) {
                $roomModel->updateStatus($data['id'], $data['is_active']);
                echo json_encode(['message' => 'Room status updated']);
            } else {
                $roomModel->update($data);
                echo json_encode(['message' => 'Room updated']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid data']);
        }
        break;
    case 'DELETE':
        authenticate(true); // Admin only
        $id = $_GET['id'] ?? null;
        if ($id) {
            $roomModel->delete($id);
            echo json_encode(['message' => 'Room deleted']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID required']);
        }
        break;
}

?>