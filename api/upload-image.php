<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    authenticate(); // Require authentication

    if (isset($_FILES['image'])) {
        $image = $_FILES['image'];
        $uploadDir = __DIR__ . '/../uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($image['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
            $imageUrl = 'http://localhost:8000/uploads/' . $fileName; // Adjust base URL as needed
            echo json_encode(['image_url' => $imageUrl]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload image']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'No image file provided']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>