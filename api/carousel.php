<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php'; // Adjust path as needed
require_once __DIR__ . '/../models/CarouselImage.php'; // Adjust path as needed
require_once __DIR__ . '/../middleware/auth.php'; // Adjust path as needed

$db = (new Database())->connect(); // Assumes Database class exists in config/database.php
$carouselModel = new CarouselImage($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch all carousel images (publicly accessible)
        $images = $carouselModel->getAll();
        echo json_encode($images);
        break;

    case 'POST':
        // Add a new carousel image (admin only)
        authenticate(true); // Assumes auth.php has an authenticate() function requiring admin role
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['image_url']) || empty($data['image_url'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing or empty image_url']);
            exit;
        }
        $imageId = $carouselModel->create(['image_url' => $data['image_url']]);
        http_response_code(201);
        echo json_encode(['message' => 'Image added', 'id' => $imageId]);
        break;

    case 'DELETE':
        authenticate(true); // Ensure only admins can delete
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }
        $id = $_GET['id'];
        $deletedRows = $carouselModel->delete($id);
        if ($deletedRows > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Image deleted']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Image not found']);
        }
        break;    

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>