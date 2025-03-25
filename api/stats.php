<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $roomsCount = $db->query("SELECT COUNT(*) as count FROM rooms")->fetch(PDO::FETCH_ASSOC)['count'];
    $customersCount = $db->query("SELECT COUNT(*) as count FROM users")->fetch(PDO::FETCH_ASSOC)['count'];
    $reviewsCount = $db->query("SELECT COUNT(*) as count FROM reviews")->fetch(PDO::FETCH_ASSOC)['count'];
    $staffCount = $db->query("SELECT COUNT(*) as count FROM staff")->fetch(PDO::FETCH_ASSOC)['count'];

    $stats = [
        'rooms' => "$roomsCount+",
        'customers' => "$customersCount+",
        'reviews' => "$reviewsCount+",
        'staff' => "$staffCount+"
    ];
    echo json_encode($stats);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>