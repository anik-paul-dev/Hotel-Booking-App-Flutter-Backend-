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
        echo json_encode($reviewModel->getAllWithRoomName($roomId));
        break;
    case 'POST':
        $authData = authenticate();
        $userId = $authData['firebase_uid'];
        $data = json_decode(file_get_contents('php://input'), true);
        $data['user_id'] = $userId;
        $reviewId = $reviewModel->create($data);
        
        // Update room rating
        require_once __DIR__ . '/../models/Room.php';
        $roomModel = new Room($db);
        $averageRating = $reviewModel->getAverageRating($data['room_id']);
        $roomModel->updateRating($data['room_id'], $averageRating);
        
        http_response_code(201);
        echo json_encode(['id' => $reviewId]);
        break;
    case 'DELETE':
        authenticate(true); // Admin only
        $id = $_GET['id'] ?? null;
        if ($id) {
            $result = $reviewModel->delete($id);
            $response = ['message' => 'Review deleted']; // Original response
            // Add rating update logic
            if (is_numeric($result)) { // Check if result is room_id
                require_once __DIR__ . '/../models/Room.php';
                $roomModel = new Room($db);
                $averageRating = $reviewModel->getAverageRating($result);
                $roomModel->updateRating($result, $averageRating);
                $response = [
                    'message' => 'Review deleted and rating updated',
                    'room_id' => $result,
                    'new_average_rating' => $averageRating
                ];
            }
            echo json_encode($response); // Single echo
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID required']);
        }
        break;
}

?>