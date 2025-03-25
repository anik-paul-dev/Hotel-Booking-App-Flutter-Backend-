<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Facility.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();
$facilityModel = new Facility($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo json_encode($facilityModel->getAll());
        break;
    case 'POST':
        authenticate(true); // Admin only
        $data = json_decode(file_get_contents('php://input'), true);
        $facilityId = $facilityModel->create($data);
        http_response_code(201);
        echo json_encode(['id' => $facilityId]);
        break;
    case 'DELETE':
        authenticate(true); // Admin only
        $id = $_GET['id'] ?? null;
        if ($id) {
            $facilityModel->delete($id);
            echo json_encode(['message' => 'Facility deleted']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID required']);
        }
        break;
}

?>