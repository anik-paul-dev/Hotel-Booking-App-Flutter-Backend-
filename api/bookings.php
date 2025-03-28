<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../middleware/auth.php';

// Enable error logging, disable display for clean JSON output
ini_set('display_errors', 0); // Prevent HTML warnings in response
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log'); // Ensure logs go to a file

$db = (new Database())->connect();
$bookingModel = new Booking($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            $authResult = authenticate();
            $isAdmin = isset($authResult['role']) && $authResult['role'] === 'admin';
            $userId = isset($_GET['user_id']) ? $_GET['user_id'] : ($isAdmin ? null : ($authResult['firebase_uid'] ?? null)); // Null for admin, firebase_uid for users
            $newOnly = isset($_GET['new_only']) && $_GET['new_only'] === 'true';
            $bookings = $bookingModel->getAll($userId, $newOnly);
            error_log("Bookings fetched: " . count($bookings));
            echo json_encode($bookings);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        }
        break;
    case 'POST':
        try {
            $authData = authenticate();
            $userId = $authData['firebase_uid'];
            $data = json_decode(file_get_contents('php://input'), true);
            error_log("Booking POST data: " . print_r($data, true));
            $data['user_id'] = $userId;
            $bookingId = $bookingModel->create($data);
            error_log("Booking created with ID: $bookingId");
            http_response_code(201);
            echo json_encode(['id' => $bookingId]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create booking: ' . $e->getMessage()]);
        }
        break;
    case 'PUT':
        try {
            authenticate();
            $id = $_GET['id'] ?? null;
            if ($id) {
                $bookingModel->cancel($id);
                echo json_encode(['message' => 'Booking cancelled']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'ID required']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to cancel booking: ' . $e->getMessage()]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
?>