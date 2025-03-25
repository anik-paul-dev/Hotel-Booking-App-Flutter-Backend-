<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();
$reviewModel = new Review($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $roomId = $_GET['room_id'] ?? null;
        echo json_encode($reviewModel->getAll($roomId));
        break;
    case 'POST':
        $userId = authenticate();
        $data = json_decode(file_get_contents('php://input'), true);
        $data['user_id'] = $userId;
        $reviewId = $reviewModel->create($data);
        http_response_code(201);
        echo json_encode(['id' => $reviewId]);
        break;
    case 'DELETE':
        authenticate(true); // Admin only
        $id = $_GET['id'] ?? null;
        if ($id) {
            $reviewModel->delete($id);
            echo json_encode(['message' => 'Review deleted']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID required']);
        }
        break;
}

?>