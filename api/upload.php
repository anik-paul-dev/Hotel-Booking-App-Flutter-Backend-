<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    authenticate(true); // Admin only
    if (isset($_FILES['image'])) {
        $targetDir = __DIR__ . '/../uploads/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
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