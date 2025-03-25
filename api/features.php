<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Feature.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();
$featureModel = new Feature($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo json_encode($featureModel->getAll());
        break;
    case 'POST':
        authenticate(true); // Admin only
        $data = json_decode(file_get_contents('php://input'), true);
        $featureId = $featureModel->create($data);
        http_response_code(201);
        echo json_encode(['id' => $featureId]);
        break;
    case 'DELETE':
        authenticate(true); // Admin only
        $id = $_GET['id'] ?? null;
        if ($id) {
            $featureModel->delete($id);
            echo json_encode(['message' => 'Feature deleted']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID required']);
        }
        break;
}

?>