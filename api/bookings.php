<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();
$bookingModel = new Booking($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $userId = isset($_GET['user_id']) ? $_GET['user_id'] : (authenticate() ?? null);
        $newOnly = isset($_GET['new_only']) && $_GET['new_only'] === 'true';
        echo json_encode($bookingModel->getAll($userId, $newOnly));
        break;
    case 'POST':
        $userId = authenticate();
        $data = json_decode(file_get_contents('php://input'), true);
        $data['user_id'] = $userId;
        $bookingId = $bookingModel->create($data);
        http_response_code(201);
        echo json_encode(['id' => $bookingId]);
        break;
    case 'PUT':
        authenticate();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $bookingModel->cancel($id);
            echo json_encode(['message' => 'Booking cancelled']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID required']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
?>