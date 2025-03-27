<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Room.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();
$roomModel = new Room($db);

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? ''; // e.g., "/api/rooms/4"

if ($method === 'GET') {
    if ($path === '' || $path === '/api/rooms') {
        $all = isset($_GET['all']) && $_GET['all'] === 'true';
        if ($all) {
            authenticate(true);
            echo json_encode($roomModel->getAllRooms());
        } else {
            echo json_encode($roomModel->getAll());
        }
    } else {
        $pathParts = explode('/', trim($path, '/')); // ["api", "rooms", "4"]
        $id = end($pathParts); // "4"
        $room = $roomModel->getById($id);
        if ($room) {
            echo json_encode($room);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Room not found']);
        }
    }
} else {
    switch ($method) {
        case 'POST':
            authenticate(true);
            $data = json_decode(file_get_contents('php://input'), true);
            $roomId = $roomModel->create($data);
            http_response_code(201);
            echo json_encode(['id' => $roomId]);
            break;
        case 'PUT':
            authenticate(true);
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
            authenticate(true);
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
}
?>